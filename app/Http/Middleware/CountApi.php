<?php

namespace App\Http\Middleware;

use App\Jobs\CountApiJob;
use Closure;
//use Illuminate\Support\Facades\Redis;

class CountApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = 'success')
    {
        $apiname = ltrim($request->getPathInfo(), '/');

//        var_dump($apiname);die;
        // async

//        die;
        // api count
        CountApiJob::dispatch($apiname, $type);

        $response = $next($request);

        return $response;
    }
}
