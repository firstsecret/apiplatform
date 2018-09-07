<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\Internal;
use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;

//use App\Http\Controllers\Controller;

class AuthController extends AdminBaseController
{
    /**
     *  授权应用 token 登录
     */
    public function getAccessToken(Request $request)
    {
        $app_key = urldecode($request->get('app_key'));
        $app_secret = urldecode($request->get('app_secret'));

        $token = Internal::factoryAccessToken($app_key, $app_secret);
        // 生成 token
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
//        return Response()->json(['status_code' => 200, 'msg' => 'token生成成功', 'data' => ['access_token' => 'Bearer' . $reData['access_token'], 'express_in' => $reData['express_in']]]);
    }

    /**
     * 添加其他应用角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewInternal(Request $request)
    {
        $request->validate([
//            'name' => 'required|max:14|min:3|bail',
//            'password' => 'required|max:16|min:6',
            'email' => 'email',
            'telephone' => 'numeric',
//            'type' => 'numeric'
        ], [
//            'name.required' => '用户名必须',
//            'name.max' => '用户名长度不能超过14个字符',
//            'name.min' => '用户名长度不能超过3个字符',
//            'password.required' => '用户密码必须',
//            'password.max' => '用户密码长度不能超过16位',
//            'password.min' => '用户密码长度不能少于6位',
            'email.email' => '邮箱格式错误',
//            'telephone.required' => '手机号码必须',
            'telephone.numeric' => '手机号码必须是纯数字',
//            'type.numeric' => '用户类型只能为数字'
        ]);

        $reqData = [
//            'name' => $request->input('name'),
//            'password' => $request->input('password'),
            'email' => $request->input('email', null),
            'telephone' => $request->input('telephone', null),
        ];

        $resWithData = Internal::createAdminUser($reqData);

        return $this->res2Response($resWithData['res'], '生成成功', '生成失败', $resWithData['data']);
    }
}
