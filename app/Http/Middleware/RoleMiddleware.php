<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->user()?->role?->name;

        if (! $role || ! in_array($role, $roles, true)) {
            abort(403, 'Anda tidak punya akses untuk aksi ini.');
        }

        return $next($request);
    }
}