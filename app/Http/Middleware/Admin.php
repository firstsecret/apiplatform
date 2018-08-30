<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

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
//            var_dump(Auth::guard('admin')->check());
            if (!Auth::guard('admin')->check()) { //专门检查后台有没有登录
                return redirect('/admin/login');
            }
        }
        return $next($request);
    }
}
