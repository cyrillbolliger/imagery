<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Controllers\Upload\RegularUploadStrategy;
use App\Logo;
use App\Rules\CanManageGroupRule;
use App\Rules\FileExtensionRule;
use App\Rules\ImmutableRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LogoController extends Controller
{
    private const ALLOWED_EXT = ['png', 'svg'];
    private const ALLOWED_SIZE = 1.0; // Logos must not exceed 1 MB

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->manageableLogos();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Logo  $logo
     *
     * @return \Illuminate\Http\Response|Logo
     */
    public function store(Request $request, Logo $logo)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($logo)],
            'added_by'   => ['sometimes', new ImmutableRule($logo)],
            'filename'   => ['required', 'string', 'max:255', new FileExtensionRule(self::ALLOWED_EXT)],
            'name'       => ['required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($logo)],
            'updated_at' => ['sometimes', new ImmutableRule($logo)],
            'deleted_at' => ['sometimes', new ImmutableRule($logo)],
            'groups'     => ['required', 'array', 'max:100'],
        ]);

        unset($data['groups']);

        $handler          = $this->makeUploadHandler($data['filename']);
        $data['filename'] = $handler->storeFinal(Logo::getStorageDir());

        $logo->fill($data);

        if ( ! $logo->save()) {
            return response('Could not save logo.', 500);
        }

        $groups = $request->input('groups');
        $this->syncGroups($groups, $logo);

        unset($logo->groups); // do not return associations

        return $logo;
    }

    /**
     * UploadHandler factory
     *
     * @param  string  $filename
     *
     * @return RegularUploadStrategy
     */
    private function makeUploadHandler(string $filename)
    {
        return new RegularUploadStrategy(
            self::ALLOWED_EXT,
            $filename,
            self::ALLOWED_SIZE
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Logo  $logo
     *
     * @return Logo
     */
    public function show(Logo $logo)
    {
        return $logo;
    }

    /**
     * Display a listing of the logos associated with the given group.
     *
     * @param  Group  $group
     *
     * @return Logo[]
     */
    public function listByGroup(Group $group)
    {
        return $group->logos;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response|Logo
     */
    public function update(Request $request, Logo $logo)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($logo)],
            'added_by'   => ['sometimes', new ImmutableRule($logo)],
            'filename'   => [
                'sometimes',
                'required',
                'string',
                'max:255',
                new FileExtensionRule(self::ALLOWED_EXT)
            ],
            'name'       => ['sometimes', 'required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($logo)],
            'updated_at' => ['sometimes', new ImmutableRule($logo)],
            'deleted_at' => ['sometimes', new ImmutableRule($logo)],
            'groups'     => ['sometimes', 'array', 'max:100'],
        ]);

        if ($request->has('groups')) {
            $this->syncGroups($data['groups'], $logo);
            unset($data['groups']);
        }

        if ($request->has('filename')) {
            $handler          = $this->makeUploadHandler($data['filename']);
            $data['filename'] = $handler->storeFinal(Logo::getStorageDir());
        }

        if ( ! $logo->update($data)) {
            return response('Could not save logo.', 500);
        }

        unset($logo->groups); // do not return associations

        return $logo;
    }

    /**
     * Associate the logo only with the given groups.
     *
     * If the group was already associated with groups this user can't manage,
     * silently keep those associations.
     *
     * @param  int[]  $groups  the ids of the groups to associate
     * @param  Logo  $logo
     */
    private function syncGroups(array $groups, Logo $logo)
    {
        // check if user can manage all specified groups
        foreach ($groups as $group) {
            Validator::make(
                ['groups' => $group],
                [
                    'groups' => [
                        'required',
                        'exists:groups,id',
                        new CanManageGroupRule()
                    ]
                ]
            )->validate();
        }

        // if logo is associated with groups, the user can't manage,
        // then keep this association.
        /** @var User $user */
        $user = Auth::user();
        foreach ($logo->groups as $group) {
            if ( ! $user->canManageGroup($group)) {
                $groups[] = $group->id;
            }
        }

        // ensure, the logo is at least associated with one group
        // otherwise it would become an orphan
        if (empty($groups)) {
            $resp = [
                'message' => 'Unable to update logo.',
                'errors'  => [
                    'groups' => ['The logo must be associated with at least one group.']
                ],
            ];
            abort(response($resp, 422));
        }

        // set associations
        $logo->groups()->sync($groups);
    }


    /**
     * Remove the specified resource from storage.
     *
     * Since it is a soft delete, we do not delete the file.
     *
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Logo $logo)
    {
        /** @var User $user */
        $user = Auth::user();
        foreach ($logo->groups as $group) {
            if ( ! $user->canManageGroup($group)) {
                $resp = [
                    'message' => 'Unable to delete logo.',
                    'errors'  => [
                        'groups' => ["There is at least one group you can't manage that depends on this logo."]
                    ],
                ];

                return response($resp, 422);
            }
        }

        $logo->groups()->sync([]); // detach all groups

        if ( ! $logo->delete()) {
            return response('Could not delete logo.', 500);
        }

        return response(null, 204);
    }
}
