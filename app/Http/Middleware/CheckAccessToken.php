<?php

namespace App\Http\Middleware;

use Closure;

class CheckAccessToken
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

        // 获取 header头

//        var_dump($request->header('app-token'));die;
        // token 是否过期
        return $next($request);
    }
}
