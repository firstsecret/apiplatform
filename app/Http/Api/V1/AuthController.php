<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Http\Requests\UserRule;
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
        $userInfo = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($userInfo)) {
                return response()->json(['error user or password'], 401);
            }
        } catch (JWTException $e) {
            return $this->response->error('system error', 500);
        }

        return response()->json(compact('token'));
    }

    /**
     *  注册
     */
    public function register(UserRule $request)
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
