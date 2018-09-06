<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/6
 * Time: 10:33
 */

namespace App\Client;


use App\Client\Contracts\Request;

class httpClient implements Request
{
    public function __construct()
    {
        // 获取配置的驱动
        $driver = config('app.http_driver');
        $this->client = new $driver;
    }

    public function get($url, $params = [])
    {
        return $this->client->get($url, $params);
    }

    public function post($url, $params = [])
    {
        return $this->client->post($url, $params);
    }

    public function request($method, $url, $params = [])
    {
        return $this->client->request($method, $url, $params);
    }
}