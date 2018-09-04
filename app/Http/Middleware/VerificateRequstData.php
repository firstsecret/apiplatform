<?php

namespace App\Http\Middleware;

use App\Exceptions\SignException;
use App\Models\AppUser;
use Closure;

class VerificateRequstData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $admin = \App\Models\Admin::find(1);

//        dd(Hash::check('123456', $admin->password));

        $signMethod = 'md5';

        $sequenceID = $request->input('sequenceId'); // client 随机值

        $appKey = $request->input('appKey'); // app key

        // 获取 appsecret
        $appUser = AppUser::field('app_secret,user_id')->where('app_key', $appKey)->first()->toArray();

        if (!$appUser) throw new SignException('400', 'appkey不存在', []);

        $sign = $request->input('sign');

        $reqData = $request->input('reqData'); // client 请求数据

        $appSecret = $appUser['app_secret'];

        $makeSign = $signMethod($reqData . $sequenceID . $appSecret);

        if ($makeSign != $sign) throw new SignException('400', 'sign错误', []);

        return $next($request);
    }
}
