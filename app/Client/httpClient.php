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
    public $request;

    public function __construct($config = [])
    {
        // 获取配置的驱动
        $driver = config('app.http_driver');
        $this->request = new $driver($config);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->client->$name($arguments);
    }
}