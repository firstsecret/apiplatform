<?php

namespace App\Jobs;

use App\Models\AppUser;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class UpdateAppKeyMap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    protected $appMsg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appMsg = [])
    {
        //
        $appMsg ? $this->initMap($appMsg) : [];
    }

    protected function initMap($appMsg)
    {
        $this->appMsg = $appMsg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->appMsg ? $this->updateSingle() : $this->updateAll();
    }

    protected function updateSingle()
    {
        Redis::hset(User::APP_KEY_FLAG . $this->appMsg['app_key'], User::APP_SECRET_FLAG, $this->appMsg['app_secret']);
        Redis::hset(User::APP_KEY_FLAG . $this->appMsg['app_key'], User::APP_USER_TYPE_FLAG, $this->appMsg['user_type']);
        Redis::hset(User::APP_KEY_FLAG . $this->appMsg['app_key'], User::APP_KEY_TYPE, $this->appMsg['type']);
        Redis::hset(User::APP_KEY_FLAG . $this->appMsg['app_key'], User::APP_USER_ID, $this->appMsg['user_id']);
//        Redis::set($this->appMsg['app_key'], $this->appMsg['app_secret'] . $this->appMsg['type']);
    }

    protected function updateAll()
    {
        AppUser::with(['user' => function ($query) {
            $query->where('type', User::IS_ACTIVE_STATUS);
        }])->where('model', User::LOGIC_MODEL)->chunk(200, function ($users) {
            //filter
            $users = $users->filter(function ($u, $key) {
                return $u['user'];
            })->toArray();

            // handle
            foreach ($users as $user) {
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], User::APP_SECRET_FLAG, $user['app_secret']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], User::APP_USER_TYPE_FLAG, $user['user']['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], User::APP_KEY_TYPE, $user['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], User::APP_USER_ID, $user['user_id']);
            }
        });

//        AppUser::chunk(200, function ($users) {
//            foreach ($users as $u) {
////                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
//                Redis::set($u['app_key'], $u['app_secret'] . $u['type']);
//            }
//        });
    }
}
