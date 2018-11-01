<?php

namespace App\Console;

use App\Jobs\ApiLuaCountJob;
//use App\Jobs\CheckAppKeySecretJob;
use App\Jobs\CheckAppKeySecretJob;
use App\Jobs\UpdateAppKeyMap;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\WebServer::class
    ];

//    protected $description = '定时检查更新redis中appkey与appsecret';

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // check 无效的 appkey  缓存
        $schedule->job(new CheckAppKeySecretJob)
            ->description('检查appkey与appsecret缓存是否有效,去除无效缓存')
            ->runInBackground()
            ->dailyAt('02:00')
            ->onOneServer();

        $schedule->job(new UpdateAppKeyMap)
            ->description('更新appkey与secret在redis中的状态')
            ->runInBackground()
            ->dailyAt('00:01')
            ->onOneServer();

        // horizon
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // 每日流量统计
        $schedule->call(function () {
            ApiLuaCountJob::dispatch();
        })->description('每日流量统计')->dailyAt('23:58')->runInBackground()->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
