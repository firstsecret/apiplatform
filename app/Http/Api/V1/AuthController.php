<?php

namespace App\Http\Api\V1;

use App\Facades\AdminUser;
use App\Http\Api\BaseController;
use App\Http\Requests\V1\UserRegisterRule;
use App\Http\Requests\V1\UserRule;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthController
 * @author Bevan
 * @Resource("Auth")
 * @package App\Http\Api\V1
 */
class AuthController extends BaseController
{
    /**
     * api开放平台接入用户登录api
     *
     * 登录api,支持电话/邮箱/账号 登录
     *
     * @Post("/cli/login")
     * @Response(200, body={"status_code":200,"message":"success","reqData":{"access_token": "token", "express_in": 7200}})
     * @Request("{'login_name':'login_name', 'password':'password'}",contentType="application/x-www-form-urlencoded")
     * @param UserRule $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserRule $request)
    {
        $token = AdminUser::login($request->input('login_name'), $request->input('password'), 'user');

        return $this->tokenResponse($token);
    }

    /**
     * api开放平台接入用户注册api
     *
     * 注册api, 等待邮箱 短信 激活功能接入
     *
     * @Post("/cli/register")
     * @Response(200, body={"status_code":200,"message":"success","reqData":{"access_token": "token", "express_in": 7200}})
     * @Request("{'name':'account','email':'email','password':'password'}",contentType="application/x-www-form-urlencoded")
     * @param UserRegisterRule $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRule $request)
    {
        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        if ($user->save()) {
            $token = JWTAuth::fromUser($user);

            return $this->tokenResponse($token);
        }

        return $this->responseClient(500, '用户创建失败', []);
    }
}
