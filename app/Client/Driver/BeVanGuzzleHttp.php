<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/12
 * Time: 11:03
 */

namespace App\Client\Driver;


use App\Client\Contracts\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use function GuzzleHttp\Promise\unwrap;

class BeVanGuzzleHttp implements Request
{
    /**
     * 请求成功的数据
     * @var
     */
    public $fulfilled;

    /**
     * 请求失败的数据
     * @var
     */
    public $rejected;

    /**
     * 并发数
     * @var
     */
    public $concurrency = 5;

    public function __construct($config = [])
    {
        $this->setConcurrency($config);
        $this->client = new Client($config);
    }

    public function setConcurrency($config)
    {
        $this->concurrency = is_array($config) ? ($config['concurrency'] ?? 5) : (int)$config;
    }

    public function get($url, $params = [])
    {
        $this->client->get($url, $params);
    }

    public function post($url, $params = [])
    {
        $this->client->post($url, $params);
    }

    public function request($method, $url, $params = [])
    {
        $this->client->request($method, $url, $params);
    }

    /**
     * simple async http con request (need repair)
     * @param $promise
     * @return array
     * @throws \Throwable
     */
    public function asyncRequest($promise)
    {
        $re_promise = [];
        foreach ($promise as $key => $item) {
            $re_promise[$key] = $this->client->getAsync($item['uri']);
        }

        $results = unwrap($re_promise);

        return $results;
    }

    /**
     * 异步携程 并发 请求
     * @param $requestData
     */
    public function asyncPoolRequest($requestData)
    {
        $requests = function ($requestData) {
            foreach ($requestData as $key => $request) {
                yield new \GuzzleHttp\Psr7\Request($request['method'], $request['uri']);
            }
        };

        $pool = new Pool($this->client, $requests($requestData), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                // this is delivered each successful response
                $this->fulfilled[$index] = $response->getBody()->getContents();
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
//                $this->rejected[$index] = get_class_methods($reason);
                $this->rejected[$index]['response'] = $reason->getResponse();
                $this->rejected[$index]['error'] = $reason->getMessage();
                $this->rejected[$index]['code'] = $reason->getCode();
            },
        ]);
//        $start_time = microtime(true);
//        var_dump($start_time);
        $promise = $pool->promise();
        $promise->wait();

//        var_dump('耗时:' . ((microtime(true) - $start_time) / 1000) . 'ms');
//        var_dump($this->fulfilled);
//        var_dump($this->rejected);
//        var_dump('结束');
//        die;
    }

    public function getResponse()
    {
        return [
            'fulfilled' => $this->fulfilled,
            'rejected' => $this->rejected
        ];
//        var_dump($this->fulfilled);
//        var_dump($this->rejected);
    }
}