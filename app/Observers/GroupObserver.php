<?php

namespace App\Observers;

use App\Group;
use Illuminate\Support\Facades\Auth;

class GroupObserver
{
    /**
     * Handle the group "creating" event.
     *
     * @param  \App\Group  $group
     *
     * @return void
     */
    public function creating(Group $group)
    {
        // only fire for authenticated users so we don't kill the seeder
        if (Auth::user()) {
            $group->addedBy()->associate(Auth::user());
        }
    }
}
