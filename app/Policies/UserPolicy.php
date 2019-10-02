<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     *
     * @return mixed
     */
    public function get(User $user, User $model)
    {
        return $this->canManage($user, $model);
    }

    /**
     * @param  User  $manager  the user currently logged in
     * @param  User  $managed  the user to manage
     *
     * @return bool
     */
    private function canManage(User $manager, User $managed): bool
    {
        if ($manager->is($managed) || $manager->super_admin) {
            return true;
        }

        if ( ! $manager->isAdmin()) {
            return false;
        }

        foreach ($manager->roles()->admin()->get() as $role) {
            if ($managed->managedBy->is($role->group)) {
                return true;
            }

            if ($managed->managedBy->isDescendantOf($role->group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
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
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     *
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     *
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     *
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     *
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
