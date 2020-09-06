<?php


namespace App\Services;


use App\Exceptions\UserFederationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Vizir\KeycloakWebGuard\Facades\KeycloakWeb;

class UserFederationService
{
    public function loadLocalUser()
    {
        if ($this->isLocalUserLoaded()) {
            return;
        }

        $authenticated = Auth::user();

        if (!$authenticated) {
            throw new AuthenticationException();
        }

        $user = \App\User::whereEmail($authenticated->id)->first();

        if (!$user) {
            throw new UserFederationException('Local user not found.');
        }

        Auth::shouldUse('web-local');
        Auth::setUser($user);
        Auth::login($user, true);
        KeycloakWeb::forgetToken();
    }

    public function isLocalUserLoaded()
    {
        return Auth::user() instanceof \App\User;
    }

    public function isKeycloakUserLoaded()
    {
        return Auth::user() instanceof \App\User;
    }
}
