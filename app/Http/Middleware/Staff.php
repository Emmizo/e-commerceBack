<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class Staff extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
       $user = JWTAuth::parseToken()->authenticate();
        
        if($user->level !=2){
            return response()->json(['Message' => 'You are not the staff please','Status'=>401],401);
        }
        return $next($request);
    }
}
