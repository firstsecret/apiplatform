<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/6
 * Time: 10:33
 */

namespace App\Client;




class httpClient
{
    public function __construct($config = [])
    {
        // 获取配置的驱动
        $driver = config('app.http_driver');
        $this->client = new $driver($config);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->client->$name($arguments);
    }

//    public function get($url, $params = [])
//    {
//        return $this->client->get($url, $params);
//    }
//
//    public function post($url, $params = [])
//    {
//        return $this->client->post($url, $params);
//    }
//
//    public function request($method, $url, $params = [])
//    {
//        return $this->client->request($method, $url, $params);
//    }
}