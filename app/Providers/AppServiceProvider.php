<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\User::observe(\App\Observers\UserObserver::class);
        \App\Role::observe(\App\Observers\RoleObserver::class);
        \App\Group::observe(\App\Observers\GroupObserver::class);
        \App\Logo::observe(\App\Observers\LogoObserver::class);
        \App\Image::observe(\App\Observers\ImageObserver::class);
    }
}
