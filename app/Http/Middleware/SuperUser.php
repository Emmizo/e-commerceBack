<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class SuperUser extends BaseMiddleware
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
        if($user->level !=3){
            return response()->json(['Message' => 'You are not the Super User please','Status'=>401],401);
        }
        return $next($request);
    }
}
