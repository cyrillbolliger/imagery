<?php

namespace App\Http\Middleware;

use Closure;

class BlockNonApprovedUsers
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
        if ($request->user()->pending_approval) {
            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
