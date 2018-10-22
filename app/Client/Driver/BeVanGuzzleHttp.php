<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/12
 * Time: 11:03
 */

namespace App\Client\Driver;


use App\Client\Contracts\Request;
use App\Client\Exception\HttpClientException;
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

    /**
     * http get
     * @param $url
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, $params = [])
    {
        return $this->client->get($url, $params);
    }

    /**
     * http post
     * @param $url
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, $params = [])
    {
        return $this->client->post($url, $params);
    }

    /**
     * restful api
     * @param $method
     * @param $url
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $url, $params = [])
    {
        return $this->client->request($method, $url, $params);
    }

    /**
     * async restful api (don't use)
     * @param $method
     * @param $url
     * @param array $params
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function requestAsync($method, $url, $params = [], $successCallBack, $erroCallBack)
    {
        if (!is_callable($successCallBack) || !is_callable($erroCallBack)) throw new HttpClientException(403, '参数不是一个回调方法');

        $promise = $this->client->requestAsync($method, $url);
        $promise->then(
            $successCallBack, $erroCallBack
        )->wait();

//        $promise = $this->client->requestAsync($method, $url, $params);
////        dd($promise);
//        $promise->then(function (ResponseInterface $response) {
//            Log::info('回调成功');
//        }, function (RequestException $e) {
//            Log::info($e->getMessage());
//        });
    }

    /**
     * simple async http con request (need repair) !!! don't use ,is bading
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
     * 异步协程 并发 请求
     * @param $requestData
     */
    public function asyncPoolRequest($requestData)
    {
        $client = $this->client;
        $requests = function ($requestData) use ($client) {
            foreach ($requestData as $key => $request) {
//                yield new \GuzzleHttp\Psr7\Request($request['method'], $request['uri'], $headers, $body);
//                yield (new Client())->requestAsync($request['method'], $request['uri'], $request['options']);
                yield function () use ($client, $request) {
                    return $client->requestAsync($request['method'], $request['uri'], $request['options']);
                };
//                yield new \GuzzleHttp\Psr7\Request($request['method'], $request['uri'], [], []);
            }
        };

        $pool = new Pool($this->client, $requests($requestData), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                // this is delivered each successful response
                $this->fulfilled[$index]['response'] = $response->getBody()->getContents();
//                $this->fulfilled[$index]['methods'] = get_class_methods($response);
//                $this->fulfilled[$index]['class'] = get_class($response);
                $this->fulfilled[$index]['headers'] = $response->getHeaders();
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
//                $this->rejected[$index] = get_class_methods($reason);
//                $this->rejected[$index]['method'] = get_class_methods($reason);
//                $this->rejected[$index]['test'] = $reason->getMessage();
                $this->rejected[$index] = [
                    'response' => $reason->getResponse(),
                    'errorMsg' => $reason->getMessage(),
                    'code' => $reason->getCode()
                ];
            },
        ]);
//        $start_time = microtime(true);
//        var_dump($start_time);

        // sync
        $promise = $pool->promise();

//        $promise->then(function ($requestData) {
//            var_dump('回调的并发请求回调');
//
//            var_dump($requestData);
//        });
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
    }
}
