<?php

namespace App\Http\Middleware;

use Closure;

class BlockNonEnabledUsers
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->enabled) {
            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
