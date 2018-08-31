<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 10:38
 */

namespace App\Services;


use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserService extends BaseService
{
    public function login($login_name, $password)
    {
        // check login type
        $type = $this->getLoginType($login_name);

        $loginMsg = [
            $type => $login_name,
            'password' => $password
        ];

        // add event

        $token = $this->loginByType($loginMsg);

        if($token !== false){
            $admin = JWTAuth::user();

            cache(['admin-' . $admin->id => $token], config('jwt.ttl'));
        }

        return $token;
    }

    public function getLoginType($login_name)
    {
        if (filter_var($login_name, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif ($this->checkIsPhone($login_name)) {
            return 'telephone';
        } else {
            return 'name';
        }
    }

    public function loginByType(Array $msg)
    {
        return JWTAuth::attempt($msg);
    }
}