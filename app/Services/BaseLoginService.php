<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/7
 * Time: 14:08
 */

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;

abstract class BaseLoginService extends BaseService
{
    public $user;

    public function __construct()
    {
        $this->user = JWTAuth::user();
    }
}