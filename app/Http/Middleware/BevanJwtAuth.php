<?php

namespace App\Http\Middleware;

use App\Exceptions\BevanJwtAuthException;
use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class BevanJwtAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $model = 'user')
    {
        $this->checkClaimModel($model);

        // check
        $this->authenticate($request);

        return $next($request);
    }

    public function checkClaimModel($model = 'user')
    {
        if ($model != $this->auth->getClaim('model')) throw new BevanJwtAuthException(400, 'token令牌场景错误,验证失败');
    }
}
