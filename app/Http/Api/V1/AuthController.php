<?php

namespace App\Http\Api\V1;

use App\Facades\AdminUser;
use App\Http\Api\BaseController;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $token = AdminUser::login($request->input('login_name'), $request->input('password'), 'user');

        return $this->tokenResponse($token);
    }

    /**
     *  注册
     */
    public function register(\App\Http\Requests\V1\UserRule $request)
    {
        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        if ($user->save()) {
            $token = JWTAuth::fromUser($user);

            return ['token' => $token];
        }

        return ['status_code' => 500, 'msg' => '用户创建失败'];
    }
}
