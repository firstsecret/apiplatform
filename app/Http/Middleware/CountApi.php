<?php

namespace App\Http\Middleware;

use Closure;

class CountApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiname = $request->getPathInfo();

        var_dump($apiname);

        $response = $next($request);

        return $response;
    }
}
