<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
//    public function handle(Request $request, Closure $next)
//    {
//        return $next($request);
//    }

    public function handle(Request $request, Closure $next, $guard = 'volunteer-api')
    {
        if (!Auth::guard($guard)->check()) {
            return response()->json(['error' => 'Unauthorized. Token not provided.'], 401);
        }

        return $next($request);
    }
}
