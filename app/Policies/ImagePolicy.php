<?php

namespace App\Policies;

use App\Image;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
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
     * Determine whether the user can view any images.
     *
     * @param  \App\User  $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the image.
     *
     * @param  \App\User  $user
     * @param  \App\Image  $image
     *
     * @return mixed
     */
    public function view(User $user, Image $image)
    {
        // your own images
        if ($image->user->is($user)) {
            return true;
        }

        // all final images
        if ($image->isFinal()) {
            return true;
        }

        // only shareable raw images
        return $image->isShareable();
    }

    /**
     * Determine whether the user can create images.
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
     * Determine whether the user can update the image.
     *
     * @param  \App\User  $user
     * @param  \App\Image  $image
     *
     * @return mixed
     */
    public function update(User $user, Image $image)
    {
        return $image->user->is($user);
    }

    /**
     * Determine whether the user can delete the image.
     *
     * @param  \App\User  $user
     * @param  \App\Image  $image
     *
     * @return mixed
     */
    public function delete(User $user, Image $image)
    {
        return $image->user->is($user);
    }
}
