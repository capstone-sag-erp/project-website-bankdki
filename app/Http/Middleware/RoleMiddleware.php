<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (!$user) abort(401);

        if (!$user->hasRole($role)) {
            abort(403); // Forbidden
        }

        return $next($request);
    }
}
