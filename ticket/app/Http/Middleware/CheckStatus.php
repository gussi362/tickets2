<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//middle ware for admin type=1
class CheckStatus
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
        if (auth()->user() && auth()->user()->type == 1) {
               return $next($request);
           }
           return response()->json('Your are not allowed to view this page');
   
     
    }
}
