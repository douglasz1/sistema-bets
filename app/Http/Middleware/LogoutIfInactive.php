<?php

namespace Bets\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LogoutIfInactive
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
        if (!Auth::user()->active) {
            return redirect('/logout');
        }

        return $next($request);
    }
}
