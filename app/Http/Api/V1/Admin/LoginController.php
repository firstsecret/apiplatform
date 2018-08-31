<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\AdminUser;
use App\Http\Api\AdminBaseController;
use App\Http\Requests\V1\AdminRule;
//use Illuminate\Http\Request;

class LoginController extends AdminBaseController
{
    //
    public function login(AdminRule $request)
    {
        $login_name = $request->input('login_name');
        $password = $request->input('password');

        $token = AdminUser::login($login_name, $password);

        return $token === false ? $this->responseClient(400,'登录失败,账号或密码错误',[]) :  $this->responseClient(200, '登录成功', ['access_token' => 'Bearer' . $token]);
    }
}
