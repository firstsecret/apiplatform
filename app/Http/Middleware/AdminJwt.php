<?php

namespace App\Http\Middleware;

use App\Exceptions\AdminJwtException;
use Closure;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AdminJwt extends BaseMiddleware
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
        // jump route
//        dd($request->getPathInfo());
        dd(config('admin.noNeedLogin'));
        if(!in_array($request->getPathInfo(), config('admin.noNeedLogin'))){
            // check
//            var_dump(Auth::guard('admin')->check());

            // get user id
            $admin = $this->auth->parseToken()->authenticate();

            if(empty($admin)){
                throw new AdminJwtException('未找到对应用户或已过期');
            }

            // is admin or user
            if(!cache('admin' .  $admin->id)){
                // user or expire time
                throw new AdminJwtException('token非法或已过期');
            }
        }


        return $next($request);
    }
}
