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

    public static function codeeditor($content)
    {

        return view('admin.plugin.codeeditor', compact('content'));
    }

    public static function custombtn($config = [])
    {
        $config['btn_id'] = 'btn-' . rand();

        return view('admin.plugin.custombtn', compact('config'));
    }
}