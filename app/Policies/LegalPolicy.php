<?php

namespace App\Policies;

use App\Legal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalPolicy
{
    use HandlesAuthorization;

    /**
     * Allow everything for the super admin (bypasses all other checks)
     *
     * @param  User  $user
     * @param $ability
     *
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if ($user->super_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any legals.
     *
     * @param  \App\User  $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the legal.
     *
     * @param  \App\User  $user
     * @param  \App\Legal  $legal
     *
     * @return mixed
     */
    public function view(User $user, Legal $legal)
    {
        return $user->can('view', $legal->image);
    }

    /**
     * Determine whether the user can create legals.
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
     * Determine whether the user can update the legal.
     *
     * @param  \App\User  $user
     * @param  \App\Legal  $legal
     *
     * @return mixed
     */
    public function update(User $user, Legal $legal)
    {
        //
    }

    /**
     * Determine whether the user can delete the legal.
     *
     * @param  \App\User  $user
     * @param  \App\Legal  $legal
     *
     * @return mixed
     */
    public function delete(User $user, Legal $legal)
    {
        //
    }

    /**
     * Determine whether the user can restore the legal.
     *
     * @param  \App\User  $user
     * @param  \App\Legal  $legal
     *
     * @return mixed
     */
    public function restore(User $user, Legal $legal)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the legal.
     *
     * @param  \App\User  $user
     * @param  \App\Legal  $legal
     *
     * @return mixed
     */
    public function forceDelete(User $user, Legal $legal)
    {
        //
    }
}
