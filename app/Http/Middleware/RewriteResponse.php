<?php

namespace App\Http\Middleware;

use Closure;

class RewriteResponse
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
//        $response = $next($request);

//        response()->headers->set('RequestUri', $request->getPathInfo());

//        return $next($request)->header('RequestUri',  $request->getPathInfo());
        return $next($request);
    }
}
