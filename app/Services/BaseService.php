<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 16:23
 */

namespace App\Services;

use App\Tool\AppTool;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class BaseService
{
    use AppTool;

    public $user;

    public function __construct()
    {
        $this->user = JWTAuth::user();
    }
}