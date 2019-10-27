<?php

namespace App\Policies;

use App\Logo;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any logos.
     *
     * @param  \App\User  $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the logo.
     *
     * @param  \App\User  $user
     * @param  \App\Logo  $logo
     *
     * @return mixed
     */
    public function view(User $user, Logo $logo)
    {
        return $user->canUseLogo($logo);
    }

    /**
     * Determine whether the user can create logos.
     *
     * @param  \App\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the logo.
     *
     * @param  \App\User  $user
     * @param  \App\Logo  $logo
     *
     * @return mixed
     */
    public function update(User $user, Logo $logo)
    {
        return $user->canManageLogo($logo);
    }

    /**
     * Determine whether the user can delete the logo.
     *
     * @param  \App\User  $user
     * @param  \App\Logo  $logo
     *
     * @return mixed
     */
    public function delete(User $user, Logo $logo)
    {
        return $user->canManageLogo($logo);
    }

    /**
     * Determine whether the user can restore the logo.
     *
     * @param  \App\User  $user
     * @param  \App\Logo  $logo
     *
     * @return mixed
     */
    public function restore(User $user, Logo $logo)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the logo.
     *
     * @param  \App\User  $user
     * @param  \App\Logo  $logo
     *
     * @return mixed
     */
    public function forceDelete(User $user, Logo $logo)
    {
        //
    }
}
