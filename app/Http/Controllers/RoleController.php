<?php

namespace App\Http\Controllers;

use App\Role;
use App\Rules\ImmutableRule;
use App\Rules\UserManagedByRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  User  $managed
     *
     * @return void
     */
    public function index(User $managed)
    {
        return $managed->roles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  User  $managed
     * @param  Role  $role
     *
     * @return void
     */
    public function store(Request $request, User $managed, Role $role)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($role)],
            'group_id'   => ['required', 'exists:groups,id', new UserManagedByRule($managed)],
            'user_id'    => ['sometimes', 'in:'.$managed->id],
            'added_by'   => ['sometimes', 'in:'.Auth::id()],
            'admin'      => ['required', 'boolean'],
            'created_at' => ['sometimes', new ImmutableRule($role)],
            'updated_at' => ['sometimes', new ImmutableRule($role)],
            'deleted_at' => ['sometimes', new ImmutableRule($role)],
        ]);
        $role->fill($data);
        $role->user_id = $managed->id;

        if ( ! $role->save($data)) {
            return response('Could not save role.', 500);
        }

        return $role;
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $managed
     * @param  Role  $role
     *
     * @return Role
     */
    public function show(User $managed, Role $role)
    {
        if ( ! $role->user->is($managed)) {
            // the role doesn't belong to the user
            return response("User hasn't matching role", 404);
        }

        // in the response we want the group, but not the user
        unset($role->user);
        $role->group;

        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User  $managed
     * @param  Role  $role
     *
     * @return Response
     */
    public function update(Request $request, User $managed, Role $role)
    {
        if ( ! $role->user->is($managed)) {
            // the role doesn't belong to the user
            return response("User hasn't matching role", 404);
        }

        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($role)],
            'group_id'   => ['sometimes', 'required', 'exists:groups,id', new UserManagedByRule($managed)],
            'user_id'    => ['sometimes', new ImmutableRule($role)],
            'added_by'   => ['sometimes', new ImmutableRule($role)],
            'admin'      => ['sometimes', 'required', 'boolean'],
            'created_at' => ['sometimes', new ImmutableRule($role)],
            'updated_at' => ['sometimes', new ImmutableRule($role)],
            'deleted_at' => ['sometimes', new ImmutableRule($role)],
        ]);

        if ( ! $role->update($data)) {
            return response('Could not save role.', 500);
        }

        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $role
     * @param  User  $managed
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(User $managed, Role $role)
    {
        if ( ! $role->user->is($managed)) {
            // the role doesn't belong to the user
            return response("User hasn't matching role", 404);
        }

        if ( ! $role->delete()) {
            return response('Could not delete role.', 500);
        }

        return response(null, 204);
    }
}
