<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(
                    [
                    'success' => false,
                    'message' => 'Token is Invalid',
                    ], 
                    Response::HTTP_FORBIDDEN
                );
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(
                    [
                        'message' => 'Token is Expired',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }else{
                return response()->json(
                    [
                        'message' => 'Authorization Token not found',
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }
        return $next($request);
    }
}