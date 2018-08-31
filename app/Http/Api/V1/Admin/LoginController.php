<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\AdminUser;
use App\Http\Api\AdminBaseController;
use App\Http\Requests\V1\AdminRule;

class LoginController extends AdminBaseController
{
    //
    public function login(AdminRule $request)
    {
        $login_name = $request->input('login_name');
        $password = $request->input('password');

        $token = AdminUser::login($login_name, $password, 'admin');

        return $this->tokenResponse($token);
    }
}
