<?php

namespace App\Jobs;

use App\Services\Admin\AppKeySecretService;
use App\Services\RedisScanService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class CheckAppKeySecretJob implements ShouldQueue
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // check
        // last valid time
        $valid_time = Redis::get('app_key_last_valid_time');

        $diff = time() - (int)$valid_time;

        if ($diff >= 24 * 3600) {
            // update
           (new AppKeySecretService())->mapAppkeysecret();
           Redis::set('app_key_last_valid_time', time());
        }


        // scanning
        $data = new RedisScanService(['match'=>User::APP_KEY_FLAG . '*']);
    }
}
