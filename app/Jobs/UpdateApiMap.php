<?php

namespace App\Jobs;

use App\Exceptions\PlatformProductException;
use App\Models\PlatformProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class UpdateApiMap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    /**
     * 执行任务的最长时间
     */

    public $timeout = 120;

    protected $apiMap;

    protected $single = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($platformProduct = [])
    {
        // init
        $platformProduct ? $this->initMap($platformProduct) : '';
    }

    protected function initMap($platformProduct = [])
    {
        if (!isset($platformProduct['api_path']) || !isset($platformProduct['internal_api_path'])) throw new PlatformProductException(4027, '缺少更新的api映射关系', 403);

        $this->apiMap = $platformProduct;
        $this->single = true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // update all mapping
        $this->single ? $this->updateSingle() : $this->updateAllMap();
    }

    public function updateAllMap()
    {
        // 清理
        Redis::del(Redis::keys(config('redis_key.services_map') . '*'));

        PlatformProduct::chunk(200, function ($products) {
            foreach ($products as $item) {
//                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
                if (!$item['api_path'] || !$item['internal_api_path']) continue;
                // map
                Redis::hset(config('redis_key.services_map') . $item['api_path'], 'internal_api_path', $item['internal_api_path']);
                Redis::hset(config('redis_key.services_map') . $item['api_path'], 'request_method', $item['request_method'] ?? 'GET');
                Redis::hset(config('redis_key.services_map') . $item['api_path'], 'internal_request_method', $item['internal_request_method'] ?? 'GET');
            }
        });
    }

    public function updateSingle()
    {
        Redis::hset(config('redis_key.services_map') . $this->apiMap['api_path'], 'internal_api_path', $this->apiMap['internal_api_path']);
        Redis::hset(config('redis_key.services_map') . $this->apiMap['api_path'], 'request_method', $this->apiMap['request_method'] ?? 'GET');
        Redis::hset(config('redis_key.services_map') . $this->apiMap['api_path'], 'internal_request_method', $this->apiMap['internal_request_method'] ?? 'GET');
    }
}
