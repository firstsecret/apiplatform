<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 14:05
 */

return [
    'admin' => env('JWT_REFRESH_ADMIN_MODEL','App\Models\Admin'),
    'user' => env('JWT_REFRESH_USER_MODEL','App\User')
];