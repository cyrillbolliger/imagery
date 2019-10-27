<?php

namespace App\Observers;

use App\Logo;
use Illuminate\Support\Facades\Auth;

class LogoObserver
{
    /**
     * Handle the logo "creating" event.
     *
     * @param  \App\Logo  $logo
     *
     * @return void
     */
    public function creating(Logo $logo)
    {
        // only fire for authenticated users so we don't kill the seeder
        if (Auth::user()) {
            $logo->addedBy()->associate(Auth::user());
        }
    }
}
