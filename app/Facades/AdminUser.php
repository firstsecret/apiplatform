<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 10:42
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class AdminUser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AdminUser';
    }
}