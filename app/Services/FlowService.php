<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/25
 * Time: 14:26
 */

namespace App\Services;


use App\Jobs\LogJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Mockery\Exception;

class FlowService
{

    public function getRealTimeFlowCount()
    {
//        return App::
    }

    public function updateTotalCount()
    {
        // redis scan
        $api_count = App::make('App\Services\RedisScanService');
        $now = date('Y-m-d', time());
        DB::beginTransaction();
        try {
            foreach ($api_count as $k => $v) {
                $api_number = Redis::MGET($v);
//                $new_api_count = [];
                $insert_sql = '';
                foreach ($v as $vk => $uri) {
                    $request_uri = substr($uri, 0, 254);
//                    $new_api_count[] = [
//                        'request_uri' => $request_uri,
//                        'request_number' => $api_number[$vk],
//                        'created_at' => $now,
//                        'updated_at' => $now
//                    ];
                    $insert_sql .= " ('$request_uri', $api_number[$vk], '$now', '$now'),";
                }
                // ru ku
                $insert_sql = rtrim($insert_sql, ',');
                $sql = "REPLACE INTO flows (request_uri,request_number,created_at,updated_at) VALUES $insert_sql";

                DB::statement($sql);
            }
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
        DB::commit();

        return true;
    }

    /**
     *  清除每日的统计
     */
    public function clearDailyApiRequest()
    {
        $all = Redis::keys('ip_api_count_*');

        Redis::del($all);
    }

    /**
     *  api 请求 统计
     */
    public function saveFlowCount()
    {
        App::singleton('apiCountIterator', function () {
            return new ApiCountService();
        });
        $now = date('Y-m-d', time());
        $redisScan = new RedisScanService(['match' => 'ip_api_count_*', 'count' => 500]);
        DB::beginTransaction();
        try {
            foreach ($redisScan as $key => $ip_api) {
                if (empty($ip_api)) continue;
                $apiCountIterator = App::make('apiCountIterator');
                $apiCountIterator->init(['all_request_ip_today' => $ip_api]);
                foreach ($apiCountIterator as $k => $v) {
                    $ip = $apiCountIterator->getIp();
                    $insert_data = [];

                    foreach ($v as $request_uri => $number) {
                        $insert_data[] = [
                            'ip' => $ip,
                            'request_uri' => substr($request_uri, 0, 254),
                            'today_total_number' => $number,
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    }
                    DB::table('ip_request_flows_copy')->insert($insert_data);
                }
            }
        } catch (\Exception $e) {
            LogJob::dispatch($e->getMessage(), 'error');
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return true;
    }


    /**
     * 模拟 计算 ， 大约 10W ip  , 全部 取出 需要 20M 左右的 内存 (弃用 ! 请使用 ApiCountService)
     * 获取 今日的 实时api 请求统计
     * @return Array
     */
    protected function realApiCount(): Array
    {
        $d = Redis::keys('ip_api_count_*');
        $ip_status = [];
        foreach ($d as $prefix_ip) {
            $reuqest_urls = Redis::hkeys($prefix_ip);
            $request_numbers = Redis::hvals($prefix_ip);
            $ip = substr(strrchr($prefix_ip, '_'), 1);
            // all field
            $tmp_request_arr = [];
            foreach ($reuqest_urls as $k => $reuqest_url) {
                $tmp_request_arr[$reuqest_url] = $request_numbers[$k];
            }
            $ip_status[$ip] = $tmp_request_arr;
        }
        // reflex
        unset($d);
        unset($prefix_ip);
        unset($reuqest_urls);
        unset($request_numbers);
        unset($ip);
        unset($tmp_request_arr);

        return $ip_status;
    }
}