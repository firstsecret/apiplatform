<?php

namespace App\Http\Api\V1;

use App\Models\AppUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *  api授权
 * Class ApiAuthController
 * @package App\Http\Api\V1
 */
class ApiAuthController extends Controller
{
    //\
    public function test()
    {
        echo '需要授权';
    }

    public function uInfo()
    {
        echo '通过了授权了可以调用api';
    }

    /**
     *  获取 access_token
     */
    public function getAccessToken(Request $request)
    {
        // shengcheng  access_token
        // save redis
        $app_key = urldecode($request->get('app_key'));
        $app_secret = urldecode($request->get('app_secret'));

        // 生成 token
        $user = AppUser::where([
            'app_key' => $app_key,
            'app_secret' => $app_secret
        ])->find(1)->user;

        $token = JWTAuth::fromUser($user);
//        dd($token);
//        $token = bcrypt($app_key . $app_secret);

//        try{
//            Redis::set($token,time(),'EX',2);
////            $redis = new \Redis();
////            $redis->connect('127.0.0.1',6379);
////
////            $redis->set($token, time());
//        }catch (\Exception $e){
//            return Response()->json(['status_code'=>500,'msg'=>'token生成失败']);
//        }
        return Response()->json(['status_code' => 200, 'msg' => 'token生成成功', 'data' => ['access_token' => $token]]);
        // return
    }

    public function refreshAccessToken()
    {
        $newtoken = JWTAuth::refresh();

        return Response()->json(['status_code' => 200, 'msg' => '刷新成功', 'data' => ['access_token' => $newtoken]]);
    }
}
