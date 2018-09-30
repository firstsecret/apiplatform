<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/30
 * Time: 10:11
 */

namespace App\Client\Driver;


use App\Client\Contracts\Request;

class CurlHttp implements Request
{
    protected $ch;

    protected $base_uri;

    public function __construct($config = [])
    {
        $this->ch = curl_init();

        if (isset($config['base_uri'])) $this->base_uri = $config['base_uri'];
    }

    public function get($url, $params = [])
    {
        $res = filter_var($url, FILTER_VALIDATE_URL);

        if (!$res) $url = $this->base_uri . $url;

        //设置抓取的url
        curl_setopt($this->ch, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($this->ch);
        //关闭URL请求
//        curl_close($curl);

        return $data;
    }

    public function post($url, $params = [])
    {

    }

    public function request($method, $url, $params = [])
    {

    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        curl_close($this->ch);
    }
}