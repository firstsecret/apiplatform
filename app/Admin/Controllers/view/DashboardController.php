<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/18
 * Time: 15:09
 */

namespace App\Admin\Controllers\view;


class DashboardController
{
    public static function healthStatus($nodeName, $f_envs)
    {
//        var_dump($envs);
        $envs = [];
        foreach ($f_envs as $k => $env) {
            $envs[] = [
                'name' => $k,
                'status' => $env
            ];
        }

        return view('admin.dashboard.healthstatus', compact('envs', 'nodeName'));
    }

    public static function cpustatus()
    {
        return view('admin.dashboard.cpustatus');
    }

    public static function memorystatus()
    {
        return view('admin.dashboard.memorystatus');
    }

    public static function serverstatus()
    {
        return view('admin.dashboard.serverstatus');
    }

    public static function netstatus()
    {
        return view('admin.dashboard.netstatus');
    }
}