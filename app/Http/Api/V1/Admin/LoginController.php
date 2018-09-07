<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\AdminUser;
use App\Http\Api\AdminBaseController;
use App\Http\Requests\V1\AdminRule;

/**
 * Class LoginController
 * @author Bevan
 * @Resource("Admin\Login")
 * @package App\Http\Api\V1\Admin
 */
class LoginController extends AdminBaseController
{
    /**
     * 后台管理系统登录api
     *
     * 后台管理系统登录 ,admin与operator可登录
     *
     * @Post("/cli/admin/login")
     * @Response(200, body={"status_code":200,"message":"success","respData":{"access_token":"token","express_in":7200}})
     * @Request("{'login_name':'login_name', 'password': 'password'}",contentType="application/x-www-form-urlencoded")
     * @param AdminRule $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminRule $request)
    {
        $login_name = $request->input('login_name');
        $password = $request->input('password');

        $token = AdminUser::login($login_name, $password, 'admin');

        return $this->tokenResponse($token);
    }
}
