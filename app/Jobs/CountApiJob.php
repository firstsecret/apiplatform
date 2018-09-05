<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class CountApiJob implements ShouldQueue
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

    public $timeout = 5;


    protected $apiname;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($apiname)
    {
        //
        $this->apiname = $apiname;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if (!$this->apiname) {
            return;
        }
        $apiname = $this->apiname;
        $apiname_arr = explode('/', $apiname);

//        dd($apiname_arr);

        $apicondition = Redis::get('api_request_condition');
        $apicondition = json_decode($apicondition, true);
//        dd($apicondition);
        if ($apicondition) {
//            dd($apiname_arr[$apiname_arr[1]]);
            if (isset($apicondition[$apiname_arr[1]])) {
                // 存在 该api 模块
                if (isset($apicondition[$apiname_arr[1]][$apiname])) {
                    // 存在 该 api 记录
                    $apicondition[$apiname_arr[1]][$apiname]['total']++;
                    $apicondition[$apiname_arr[1]][$apiname]['success']++;
                    $apicondition[$apiname_arr[1]][$apiname]['lasttime'] = time();
                } else {
                    $apicondition[$apiname_arr[1]][$apiname] = [
                        'api_name' => $apiname,
                        'total' => 1,
                        'success' => 1,
                        'fail' => 0,
                        'lasttime' => time()
                    ];
                }
            } else {
                $apicondition[$apiname_arr[1]] = [];
                $apicondition[$apiname_arr[1]][$apiname] = [
                    'api_name' => $apiname,
                    'total' => 1,
                    'success' => 1,
                    'fail' => 0,
                    'lasttime' => time()
                ];
            }
        } else {
            // 第一次使用redis 情况
            $apicondition[$apiname_arr[1]] = [];
            $apicondition[$apiname_arr[1]][$apiname] = [
                'api_name' => $apiname,
                'total' => 1,
                'success' => 1,
                'fail' => 0,
                'lasttime' => time()
            ];
        }

        Redis::set('api_request_condition', json_encode($apicondition));
    }
}
