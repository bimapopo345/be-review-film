<?php  

namespace App\Http\Middleware;  

use Closure;  
use Illuminate\Http\Request;  
use Symfony\Component\HttpFoundation\Response;  
use Tymon\JWTAuth\Facades\JWTAuth;  
use Tymon\JWTAuth\Exceptions\JWTException;  
use Illuminate\Support\Facades\Log;  

class JwtMiddleware  
{  
    public function handle(Request $request, Closure $next): Response  
    {  
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return $next($request); 
        } catch (JWTException $e) {
            Log::error('JWT Error:', ['message' => $e->getMessage()]);
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['message' => 'Token expired'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['message' => 'Token invalid'], 401);
            } else {
                return response()->json(['message' => 'Token not found'], 401);
            }
        }
    }
}
