<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/25
 * Time: 17:00
 */

namespace App\Services;


use Illuminate\Support\Facades\Redis;

/**
 * ip 流量 迭代器 , 目前标准为 : 100W用户(ip) 请求 只需 20M 左右内存
 * Class ApiCountService
 * @author Bevan
 * @package App\Services
 */
class ApiCountService implements \Iterator
{
    protected $key;
    protected $value;
    protected $ip; // current ip
    protected $all_request_ip_today;
    protected $len;

    public function key()
    {
        return $this->key;
    }

    public function rewind()
    {
        $this->key = 0;
        $this->all_request_ip_today = Redis::keys('ip_api_count_*');
        $this->len = count($this->all_request_ip_today);
//        $this->updateIp();
//        $this->value = $this->getValue();
        // TODO: Implement rewind() method.
    }

    protected function getValue()
    {
        return Redis::HGETALL($this->ip);
    }

    public function getIp()
    {
        return substr(strrchr($this->ip, '_'), 1);
    }

    protected function updateIp()
    {
        $this->ip = $this->all_request_ip_today[$this->key];
    }

    public function current()
    {
        // get fields and values
        $this->updateIp();
        $this->value = $this->getValue();
        return $this->value;
        // TODO: Implement current() method.
    }

    public function next()
    {
        $this->key += 1;
        // TODO: Implement next() method.
    }

    public function valid()
    {
        return $this->key < $this->len;
        // TODO: Implement valid() method.
    }
}