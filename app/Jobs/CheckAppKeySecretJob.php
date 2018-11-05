<?php

namespace App\Jobs;

use App\Models\AppUser;
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
//        $valid_time = Redis::get('app_key_last_valid_time');
//
//        $diff = time() - (int)$valid_time;
//
//        if ($diff >= 24 * 3600) {
//            // update
//           (new AppKeySecretService())->mapAppkeysecret();
//           Redis::set('app_key_last_valid_time', time());
//        }
        // scanning
        $data = new RedisScanService(['match' => User::APP_KEY_FLAG . '*']);

        foreach ($data as $k => $d) {
            foreach ($d as $app_key_redis) {
                $app_key = explode(':', $app_key_redis)[1];

                //db check del condition
                $appuser = AppUser::with(['user' => function ($query) {
                    $query->where('type', User::IS_ACTIVE_STATUS);
                }])->where([
                    'model' => User::LOGIC_MODEL,
                    'app_key' => $app_key,
                ])->get(['id']);

                if (!$appuser) {
                    // del
                    Redis::del($app_key_redis);
                }
            }
        }

    }
}
