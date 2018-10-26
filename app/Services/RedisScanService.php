<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/26
 * Time: 10:37
 */

namespace App\Services;


use Illuminate\Support\Facades\Redis;

class RedisScanService implements \Iterator
{
    protected $key;
    protected $value;
    protected $count = 50;
    protected $match = 'api_count_*';
    protected $redis_scan;
    protected $first_flag = true;

    public function __construct($option = [])
    {
        $this->count = $option['count'] ?? 50;
        $this->match = $option['match'] ?? 'api_count_*';
    }

    public function key()
    {
        return $this->key;
    }

    public function rewind()
    {
        // init
        $this->key = 1;
        $this->value = [];
        // TODO: Implement rewind() method.
    }

    public function next()
    {
        $this->key = $this->redis_scan[0];
        // TODO: Implement next() method.
    }

    public function valid()
    {
        return $this->key > 0;
        // TODO: Implement valid() method.
    }

    public function current()
    {
        $this->redis_scan = Redis::scan($this->key, ['match' => $this->match, 'count' => $this->count]);
        return $this->value = $this->redis_scan[1];
        // TODO: Implement current() method.
    }
}