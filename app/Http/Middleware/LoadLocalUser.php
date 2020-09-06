<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Vizir\KeycloakWebGuard\Facades\KeycloakWeb;

class LoadLocalUser
{
    /**
     * Convert any sso authenticated user to its local user object (identified
     * by email). Redirect to the sso user registration if no local user object
     * exists.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($this->isKeycloakUser($user)) {
            $this->loadLocalUser($user);
        }

        if ($this->isLocalUserLoaded()) {
            return $next($request);
        } else {
            return redirect()->route('register-sso-user');
        }
    }

    private function isKeycloakUser($user)
    {
        return $user instanceof \App\KeycloakUser;
    }

    private function loadLocalUser($keycloakUser)
    {
        $user = \App\User::whereEmail($keycloakUser->id)->first();

        if ($user) {
            Auth::shouldUse('web-local');
            Auth::setUser($user);
            Auth::login($user, true);
            KeycloakWeb::forgetToken();
        }
    }

    private function isLocalUserLoaded()
    {
        return Auth::user() instanceof \App\User;
    }
}
