<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Models\AppUser;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *  api授权
 * Class ApiAuthController
 * @package App\Http\Api\V1
 */
class ApiAuthController extends BaseController
{
    //\
    public function test()
    {
        echo '需要授权';
    }

    public function uInfo()
    {
        var_dump('okkkk');die;
        $user=JWTAuth::user()->toArray();

        return Response()->jons(['status_code'=>200,'msg'=>'获取成功','data'=>$user]);
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
        // 获取过期时间
        $express_in = '';
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
        return Response()->json(['status_code' => 200, 'msg' => 'token生成成功', 'data' => ['access_token' => $token,'express_in'=>$express_in]]);
        // return
    }

    public function refreshAccessToken()
    {

        $old_token = JWTAuth::getToken();//
        $token = JWTAuth::refresh($old_token);//利用旧token生成新的token
        JWTAuth::invalidate($token);
        //将旧的token放入黑名单
//        return response()->json(['status_code' => 0, 'message' => '', 'data' => $token]);
        return Response()->json(['status_code' => 200, 'msg' => '刷新成功', 'data' => ['access_token' => $token]]);
    }
}
