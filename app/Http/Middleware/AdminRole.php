<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $gurad, $role)
    {
        // check role premission
//        var_dump('guard:' . $guard);

        if (Auth::guard($gurad)->guest()) { //判读当前用户是否登录
            throw UnauthorizedException::notLoggedIn();
        }

        $roleArr = explode('|', $role);

        if(!Auth::guard($gurad)->user()->hasAnyRole($roleArr)){
            throw UnauthorizedException::forRoles($roleArr);
        }

//        var_dump($roleArr);
//        var_dump('ok');
        return $next($request);
    }
}
