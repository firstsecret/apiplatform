<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 16:44
 */

namespace Show\Services;

use App\Client\baseAppService;
//use App\Client\httpClient;
use Illuminate\Support\Facades\Redis;

class IndexService extends baseAppService
{
    public function oneService(): Array
    {
        $apis = Redis::get('api_request_condition');

        $appAdmin = $this->factoryAppAdminUUID(2);
//        dd($this->factoryOpenId($appAdmin));

        dd(config('show'));

        return json_decode($apis, true);
    }

    public function onCurl()
    {
//        $response = $this->client->get('http://bevan.top/api/app1/showtest', ['headers' => [
//            'Authorization' => request()->headers->get('Authorization')
//        ]]);
//
//        return json_decode($response->getBody()->getContents(), true);

    }
}