<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/29
 * Time: 17:23
 */

namespace App\Services\Admin;


use App\User;
use  GuzzleHttp\Client;

class DashboardService
{
    public function totalUser()
    {
        return User::count('id');
    }

    public function nginxStatus()
    {
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:81/status');
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
//        var_dump(gettype($data));
        print_r($data);
        return $this->formatNginxStatus($data);
    }


    protected function formatNginxStatus($str)
    {
        preg_match('/.*?(\d+)\s+(\d+)\s+(\d+).*?/',$str,$match);

        return $match[3];
    }
}