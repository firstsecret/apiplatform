<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/25
 * Time: 14:26
 */

namespace App\Services;


use App\Models\Flow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class FlowService
{
    public function getRealTimeFlowCount()
    {
        return $this->realApiCount();
    }

    public function saveFlowCount()
    {
        $ip_status = $this->realApiCount();
        DB::beginTransaction();
        //
        try {
            Flow::whereBetween(['created_at', [date('Y-m-d', time()), date('Y-m-d', strtotime("+1 day"))]])->delete();

            $flowModel = new Flow;
            $flowModel->ip_status = json_encode($ip_status, JSON_UNESCAPED_UNICODE);

            $flowModel->save();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return true;
    }

    /**
     * 模拟 计算 ， 大约 10W ip  , 全部 取出 需要 20M 左右的 内存
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