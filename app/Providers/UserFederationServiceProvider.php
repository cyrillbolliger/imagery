<?php

namespace App\Providers;

use App\Services\UserFederationService;
use Illuminate\Support\ServiceProvider;

class UserFederationServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        UserFederationService::class => UserFederationService::class,
    ];
}
