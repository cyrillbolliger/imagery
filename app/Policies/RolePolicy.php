<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->super_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can manage the role.
     *
     * @param  User  $manager  the user currently logged in
     * @param  Role  $role  the role to manage
     *
     * @return mixed
     */
    public function manage(User $manager, Role $role): bool
    {
        if ( ! $manager->can('manage', $role->user)) {
            return false;
        }

        if ( ! $manager->canManageGroup($role->group)) {
            return false;
        }

        if ( ! $manager->canManageGroup(request()->group_id)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the manager can view the users roles.
     *
     * @param  User  $manager  the user currently logged in
     *
     * @return mixed
     */
    public function view(User $manager)
    {
        $managed = request()->user;

        return $manager->can('manage', $managed);
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     *
     * @return mixed
     */
    public function update(User $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     *
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     *
     * @return mixed
     */
    public function restore(User $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     *
     * @return mixed
     */
    public function forceDelete(User $user, Role $role)
    {
        //
    }
}
