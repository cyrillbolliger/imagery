<?php

namespace App\Http\Controllers;

use App\Group;
use App\Rules\CanManageGroupRule;
use App\Rules\ImmutableRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->super_admin) {
            return Group::all();
        }

        return $user->manageableGroups();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Group  $group
     *
     * @return Response|Group
     */
    public function store(Request $request, Group $group)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($group)],
            'parent_id'  => ['required', 'exists:groups,id', new CanManageGroupRule()],
            'added_by'   => ['sometimes', 'in:'.Auth::id()],
            'name'       => ['required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($group)],
            'updated_at' => ['sometimes', new ImmutableRule($group)],
            'deleted_at' => ['sometimes', new ImmutableRule($group)],
        ]);

        $group->fill($data);

        if ( ! $group->save()) {
            return response('Could not save group.', 500);
        }

        return $group;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     *
     * @return Response|Group
     */
    public function show(Group $group)
    {
        return $group;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Group  $group
     *
     * @return Response|Group
     */
    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($group)],
            'parent_id'  => [
                'sometimes',
                'required',
                'exists:groups,id',
                new CanManageGroupRule(), // check if user can manage the new parent group
                function ($attribute, $value, $fail) use ($group) {
                    // check if user can manage the current parent group.
                    // this prevents the user from loosing access to a part of
                    // his groups
                    if ( ! Auth::user()->canManageGroup($group->parent_id)) {
                        $fail('You can only modify the :attribute if your admin of the parent group.');
                    }
                },
                function ($attribute, $value, $fail) use ($group) {
                    // assert the branch doesn't get disconnected or circular
                    if (Group::find($value)->isDescendantOf($group)) {
                        $fail('No, no, no, your child must never be your parent. Rethink about the :attribute.');
                    }
                    if ($value === $group->id) {
                        $fail("You ain't your own parent, are ya?");
                    }
                }
            ],
            'added_by'   => ['sometimes', new ImmutableRule($group)],
            'name'       => ['sometimes', 'required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($group)],
            'updated_at' => ['sometimes', new ImmutableRule($group)],
            'deleted_at' => ['sometimes', new ImmutableRule($group)],
        ]);

        if ( ! $group->update($data)) {
            return response('Could not save group.', 500);
        }

        return $group;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Group $group)
    {
        if ($group->children()->exists()) {
            $resp = [
                'message' => 'Unable to delete group.',
                'errors'  => [
                    'children' => 'Delete child groups first.'
                ],
            ];

            return response($resp, 422);
        }

        if ( ! $group->delete()) {
            return response('Could not delete group.', 500);
        }

        return response(null, 204);
    }
}
