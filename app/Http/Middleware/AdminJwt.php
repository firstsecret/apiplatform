<?php

namespace App\Http\Middleware;


use Closure;

class AdminJwt extends BevanJwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $model = 'admin')
    {
        // jump route
//        dd($request->getPathInfo());
//        dd(config('admin.noNeedLogin'));
        if(!in_array($request->getPathInfo(), config('admin.noNeedLogin'))){
            // check

//            var_dump(Auth::guard('admin')->check());
            // get user id
            $this->checkClaimModel($model);

            $this->authenticate($request);
//            $admin = $this->auth->parseToken()->authenticate();
//
//            if(empty($admin)){
//                throw new AdminJwtException(4044,'用户不存在');
//            }

            // is admin or user
//            if(!cache('admin-' .  $admin->id)){
//                // user or expire time
//                throw new AdminJwtException(4043,'token非法或已过期');
//            }
        }

        return $next($request);
    }
}
