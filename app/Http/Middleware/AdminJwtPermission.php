<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AdminJwtPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roleArr = explode('|', $roles);

        $admin = JWTAuth::user();

        if(!$admin->hasAnyRole($roleArr)){
            throw UnauthorizedException::forRoles($roleArr);
        }

        return $next($request);
    }
}
