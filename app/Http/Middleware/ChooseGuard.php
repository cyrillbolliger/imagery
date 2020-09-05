<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ChooseGuard
{
    static $SESSION_KEY = 'ssoLogin';

    protected $ssoCallbackPath = 'callback';

    /**
     * Make Auth use the web-sso guard, if it's an sso-login attempt.
     *
     * Uses a cookie to ensure, the first
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->path() === $this->ssoCallbackPath) {
            Auth::shouldUse('web-sso');
            $request->session()->put(self::$SESSION_KEY, true);
            $request->session()->save();
        } else if ($request->session()->pull(self::$SESSION_KEY, false)) {
            Auth::shouldUse('web-sso');
        }

        return $next($request);
    }
}
