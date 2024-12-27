<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->load('role');
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        if (!$user->role || $user->role->name !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }
        return $next($request);
    }
}
