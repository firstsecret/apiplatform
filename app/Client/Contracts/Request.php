<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/6
 * Time: 10:28
 */

namespace App\Client\Contracts;


interface Request
{
    public function get($url, $params = []);

    public function post($url, $params = []);

    public function request($method, $url, $params);
}