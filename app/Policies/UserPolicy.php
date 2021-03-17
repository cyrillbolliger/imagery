<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->super_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can manage the other user.
     *
     * @param  User  $manager  the user currently logged in
     * @param  User  $managed  the user to manage
     *
     * @return bool
     */
    public function manage(User $manager, User $managed): bool
    {
        if ($manager->is($managed)) {
            return true;
        }

        if ( ! $manager->isAdmin()) {
            return false;
        }

        foreach ($manager->adminRoles()->get() as $role) {
            if ($managed->managedBy->is($role->group)) {
                return true;
            }

            if ($managed->managedBy->isDescendantOf($role->group)) {
                return true;
            }
        }

        if ($managed->activatableBy && $managed->activatableBy->is($manager)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create other users.
     *
     * @param  \App\User  $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can list any users.
     *
     * @param  \App\User  $user
     *
     * @return bool
     */
    public function list(User $user): bool
    {
        return true;
    }

    /**
     * Determine if there is a logged in user so it can log out
     *
     * @return bool
     */
    public function logout(): bool
    {
        return Auth::check();
    }
}
