<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 16:44
 */

namespace Show\Services;


use Illuminate\Support\Facades\Redis;

class IndexService
{
    public function oneService(): Array
    {
        $apis = Redis::get('api_request_condition');

        return json_decode($apis,true);
    }
}