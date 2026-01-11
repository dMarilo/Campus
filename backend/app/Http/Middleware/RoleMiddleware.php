<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $type)
    {
        $user = $request->user();

        if (!$user || $user->type !== $type) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
