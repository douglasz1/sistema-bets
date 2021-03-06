<?php

namespace Bets\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::user()->hasRoles($role)) {
            return redirect()->route('index')->with('error', 'Ação não autorizada.');
            // abort(403, 'Ação não autorizada.');
        }

        return $next($request);
    }
}
