<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Http\TransForm\UsersTransformer;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *  api授权
 * Class ApiAuthController
 * @package App\Http\Api\V1
 */
class ApiAuthController extends BaseController
{
    /**
     * 获取已授权的个人信息
     *
     * 携带 header Authorization
     *
     * @Get("/cli/getUserInfo")
     * @Response(200, body={"status_code":200,"message":"success","resqData":{"user_name":"user_name","user_email":"user_email"}})
     * @Request(contentType="application/x-www-form-urlencoded",headers={"Authorization": "token"})
     * @param UsersTransformer $trans
     * @return \Illuminate\Http\JsonResponse
     */
    public function uInfo(UsersTransformer $trans)
    {
//        var_dump('okkkk');die;
        $user = JWTAuth::user()->toArray();
//        var_dump($user);die;
        $user = $trans->transform($user);

        return Response()->json(['status_code' => 200, 'msg' => '获取成功', 'data' => $user]);
    }

    /**
     * api授权
     *
     * api授权 携带params  ?app_key=&app_secret=
     *
     * @Get("/cli/token")
     * @Response(200, body={"status_code":200,"message":"success","resqData":{"access_token":"token","express_in":7200}})
     * @Request("app_key=appkey&app_secret=appsecret",contentType="application/x-www-form-urlencoded",headers={"Authorization": "token"})
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
        ])->first()->user;

//        if ($user->type) config(['jwt.ttl' => null]);

        $token = JWTAuth::claims(['model' => 'user'])->fromUser($user);
        // 获取过期时间
//        $express_in = config('jwt.ttl') * 60; // second
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
        return $this->tokenResponse($token);
//        return Response()->json(['status_code' => 200, 'msg' => 'token生成成功', 'data' => ['access_token' => 'Bearer' . $token, 'express_in' => $express_in]]);
        // return
    }

    /**
     * 主动刷新token
     *
     * 携带header头 主动请求刷新
     *
     * @Get("/cli/refreshAccessToken")
     * @Request(contentType="application/x-www-form-urlencoded",headers={"Authorization": "token"})
     * @Response(200, body={"status_code":200,"message":"success","resqData":{"access_token":"token","express_in":7200}})
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshAccessToken()
    {
        $old_token = JWTAuth::getToken();//
        $token = JWTAuth::refresh($old_token);//利用旧token生成新的token
        JWTAuth::invalidate($token);
        //将旧的token放入黑名单
//        return response()->json(['status_code' => 0, 'message' => '', 'data' => $token]);
        return $this->tokenResponse($token);
//        return Response()->json(['status_code' => 200, 'msg' => '刷新成功', 'data' => ['access_token' => $token]]);
    }
}
