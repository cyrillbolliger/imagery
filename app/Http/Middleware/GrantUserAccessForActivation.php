<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class GrantUserAccessForActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $activationToken = $request->query('activation');

        if ($activationToken) {
            $manager = $request->user();
            $managed = User::whereActivationToken($activationToken)->first();

            if ($managed) {
                $managed->activatableBy()->associate($manager);
                $managed->saveOrFail();
            }
        }

        return $next($request);
    }
}
