<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $userRole = Auth::user()->role;

        // Accept a single role string or comma-separated list via pipe separator
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $r) {
                if ($userRole === trim($r)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
