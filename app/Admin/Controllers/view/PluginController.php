<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 13:55
 */

namespace App\Admin\Controllers\view;


class PluginController
{
    public static function horizon()
    {
        return view('admin.plugin.horizon');
    }

    public static function supervisor()
    {
        return view('admin.plugin.supervisor');
    }
}