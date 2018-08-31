<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class AdminJwtChange
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
        Config::set('jwt.user', 'App\Models\Admin');
        Config::set('auth.providers.users.model', \App\Models\Admin::class);

        return $next($request);
    }
}
