<?php

namespace App\Listeners;

use App\Events\AsyncLogEvent;
use App\Jobs\LogJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AsyncLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AsyncLogEvent $event
     * @return void
     */
    public function handle(AsyncLogEvent $event)
    {
        // 记录事件
        LogJob::dispatch($event->message, $event->level);
//        Log::info('测试 异步 日志事件','info');
    }
}
