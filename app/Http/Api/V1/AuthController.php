<?php

namespace App\Http\Api\V1;

use App\Facades\AdminUser;
use App\Http\Api\BaseController;
use App\Http\Requests\V1\UserRegisterRule;
use App\Http\Requests\V1\UserRule;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserRule $request)
    {
        $token = AdminUser::login($request->input('login_name'), $request->input('password'), 'user');

        return $this->tokenResponse($token);
    }

    /**
     *  注册
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
