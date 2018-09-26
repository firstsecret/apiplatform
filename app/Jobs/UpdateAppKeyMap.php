<?php

namespace App\Jobs;

use App\Models\AppUser;
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

    protected $single = false;

    protected $appMsg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appMsg = [])
    {
        //
        $appMsg ? $this->initMap($appMsg) : '';
    }

    protected function initMap($appMsg)
    {
        $this->appMsg = $appMsg;
        $this->single = true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->single ? $this->updateSingle() : $this->updateAll();
    }

    protected function updateSingle()
    {
        Redis::set($this->appMsg['app_key'], $this->appMsg['app_secret'] . $this->appMsg['type']);
    }

    protected function updateAll()
    {
        AppUser::chunk(200, function ($users) {
            foreach ($users as $u) {
//                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
                Redis::set($u['app_key'], $u['app_secret'] . $u['type']);
            }
        });
    }
}
