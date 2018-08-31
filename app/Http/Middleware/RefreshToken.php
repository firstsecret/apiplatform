<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Exceptions\RefreshJwtException;

class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $model)
    {
        // check token
        $newToken = null;
        if (!in_array($request->getPathInfo(), config($model . '.noNeedLogin'))) {
            $auth = JWTAuth::parseToken();

            if (!$token = $auth->setRequest($request)->getToken()) {
                throw new RefreshJwtException('未获取token');
            }

            try {
                $user = $auth->authenticate($token);

                if (!$user) {
                    throw new RefreshJwtException('未获取到用户');
                }

                // 判断 模型 是否 正确
//                if ($model == 'admin') {
//                    if (!cache('admin-' . $user->id)) {
//                        // 未过期 且 正确
//                        throw new RefreshJwtException('非法的token');
//                    }
//                }
                $request->headers->set('Authorization', 'Bearer ' . $token);
            } catch (TokenExpiredException $e) {
//                var_dump(get_class($e));
                try {
//                sleep(rand(1, 5) / 100);
                    $newToken = JWTAuth::refresh($token);
                    var_dump($newToken);
                    $request->headers->set('Authorization', 'Bearer ' . $newToken); // 给当前的请求设置性的token,以备在本次请求中需要调用用户信息
//                Redis::setex('token_blacklist:' . $token, 30, $newToken);
                    cache(['token_blacklist:' . $token => $newToken], 5);
                } catch (JWTException $e) {
                    // 在黑名单的有效期,放行
                    if ($newToken = cache('token_blacklist:' . $token)) {
                        $request->headers->set('Authorization', 'Bearer ' . $newToken); // 给当前的请求设置性的token,以备在本次请求中需要调用用户信息
//                        return $next($request);
                    }else{
                        // 过期用户
                        throw new RefreshJwtException('账号信息已过期, 请重新登录');
                    }
                }
            } catch (JWTException $e) {
                throw new RefreshJwtException('无效的token');
            }
        }
//        dd('fsdf');
//        header('Authorization232','teststs');
        $response = $next($request);
        $response->headers->set('test','4134');
        if ($newToken) {
//            header('Authorization', 'Bearer ' . $newToken );
            $response->headers->set('Authorization', 'Bearer ' . $newToken);
        }

        return $response;
    }
}
