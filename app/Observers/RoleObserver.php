<?php

namespace App\Observers;

use App\Role;
use Illuminate\Support\Facades\Auth;

class RoleObserver
{
    /**
     * Handle the role "creating" event.
     *
     * @param  \App\Role  $role
     *
     * @return void
     */
    public function creating(Role $role)
    {
        // only fire for authenticated users so we don't kill the seeder
        if (Auth::user()) {
            $role->addedBy()->associate(Auth::user());
        }
    }
}
