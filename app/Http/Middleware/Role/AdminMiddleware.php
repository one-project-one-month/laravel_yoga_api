<?php

namespace App\Http\Middleware\Role;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user() && Auth::user()->role_id == 1) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => "You can't access this route!"
        ],403);
    }
}
