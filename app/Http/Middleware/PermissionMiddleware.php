<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permissionKey)
    {
        $user = $request->user();
        if (!$user) abort(401);

        if (!$user->canKey($permissionKey)) {
            abort(403);
        }

        return $next($request);
    }
}
