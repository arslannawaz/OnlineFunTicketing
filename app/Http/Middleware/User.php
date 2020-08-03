<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guard('api')->check()){
            if($request->user()->roles->getRole->id==1) {
                return response()->json("Auth User Failed",401);
            }
            else if($request->user()->roles->getRole->id==2) {
                return response()->json("Auth User Failed",401);
            }
            else if($request->user()->roles->getRole->id==3) {
                return $next($request);
            }
        }
        else{
            return response()->json("Auth User Failed",401);
        }
    }
}
