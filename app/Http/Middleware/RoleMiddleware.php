<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }

        $userRole = Auth::user()->role_id;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
