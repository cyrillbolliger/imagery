<?php

namespace App\Http\Controllers;

use App\Role;
use App\Rules\ImmutableRule;
use App\Rules\UserManagedByRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function index(User $user)
    {
        return $user->roles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @param  Role  $role
     *
     * @return Role
     */
    public function show(User $user, Role $role)
    {
        if ( ! $role->user->is($user)) {
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
     * @param  User  $user
     * @param  Role  $role
     *
     * @return Response
     */
    public function update(Request $request, User $user, Role $role)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($role)],
            'group_id'   => ['sometimes', 'required', 'exists:groups,id', new UserManagedByRule($user)],
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
     *
     * @return Response
     */
    public function destroy(Role $role)
    {
        //
    }
}
