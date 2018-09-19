<?php

namespace App\Http\Middleware;

use App\Exceptions\SignException;
use App\Models\AppUser;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificateRequstData
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
//        $admin = \App\Models\Admin::find(1);

//        dd(Hash::check('123456', $admin->password));

        $mapModel = [
            'admin' => 'App\Models\Admin',
            'user' => 'App\User'
        ];

        $model = $mapModel[$model];

        $signMethod = 'md5';

        $sequenceID = $request->input('sequenceId'); // client 随机值

        $appKey = $request->input('appKey'); // app key

        if (!$appKey) throw new SignException(4001, 'appkey未获取');
        // 获取 appsecret
        $appUser = AppUser::where(['app_key' => $appKey, 'model' => $model])->first(['app_secret', 'user_id']);
//        $appUser = AppUser::where(['app_key' => $appKey])->first(['app_secret', 'user_id']);

        if (!$appUser) throw new SignException(4002, '对应权限不存在');

        $requester = JWTAuth::parseToken()->user();

        $requesterApp = $requester->app($model);

        if ($requesterApp->app_key != $appKey || $requesterApp->app_secret != $appUser->app_secret) throw new SignException(4003, '授权不一致,请求失败');

        $sign = $request->input('sign');

        $reqData = strtr($request->input('reqData'), [' ' => '', "\n" => '']); // client 请求数据
        // 消除空格
        $appSecret = $appUser->app_secret;

        $makeSign = $signMethod($reqData . $sequenceID . $appSecret);

        if ($makeSign != $sign) throw new SignException(4000, 'sign错误');

        return $next($request);
    }
}
