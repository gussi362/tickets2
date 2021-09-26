<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
//middle ware for admin type=1
class Customer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next , $guard = null)
    {
        //print_r( auth()->guard('customer'));
        echo auth()->guard('customer')->user();
        if (auth()->guard('customer')->check()) {
               return $next($request);
           }
           return response()->json('Your are not allowed to view this page');
   
     
    }
}
