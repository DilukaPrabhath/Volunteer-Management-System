<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationApiAuthMiddleware
{
    public function handle(Request $request, Closure $next, $guard = 'organization-api')
    {
        if (!Auth::guard($guard)->check()) {
            return response()->json(['error' => 'Unauthorized. Token not provided.'], 401);
        }

        return $next($request);
    }
}
