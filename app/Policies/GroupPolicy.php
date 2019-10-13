<?php

namespace App\Policies;

use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any groups.
     *
     * @param  \App\User  $manager
     *
     * @return mixed
     */
    public function viewAny(User $manager)
    {
        return $manager->isAdmin();
    }

    /**
     * Determine whether the user can view the group.
     *
     * @param  \App\User  $manager
     * @param  \App\Group  $group
     *
     * @return mixed
     */
    public function view(User $manager, Group $group)
    {
        return $manager->canManageGroup($group);
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\User  $manager
     *
     * @return mixed
     */
    public function create(User $manager)
    {
        //
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\User  $manager
     * @param  \App\Group  $group
     *
     * @return mixed
     */
    public function update(User $manager, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\User  $manager
     * @param  \App\Group  $group
     *
     * @return mixed
     */
    public function delete(User $manager, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can restore the group.
     *
     * @param  \App\User  $manager
     * @param  \App\Group  $group
     *
     * @return mixed
     */
    public function restore(User $manager, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the group.
     *
     * @param  \App\User  $manager
     * @param  \App\Group  $group
     *
     * @return mixed
     */
    public function forceDelete(User $manager, Group $group)
    {
        //
    }
}
