<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('lang', $this->getAppLocale());
    }

    private function getAppLocale()
    {
        return str_replace('_', '-', app()->getLocale());
    }
}
