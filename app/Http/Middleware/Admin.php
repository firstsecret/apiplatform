<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        // check admin
        if(!in_array($request->getRequestUri(), config('admin.noNeedLogin'))){
            // check
            var_dump('need auth');
        }
        return $next($request);
    }
}
