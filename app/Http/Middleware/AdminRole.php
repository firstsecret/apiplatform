<?php

namespace App\Http\Middleware;

use Closure;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard, $role)
    {
        // check role premission
        var_dump('guard:' . $guard);

        $roleArr = explode('|', $role);

        var_dump($roleArr);

        return $next($request);
    }
}
