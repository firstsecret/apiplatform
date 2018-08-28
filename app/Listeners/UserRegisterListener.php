<?php

namespace App\Listeners;

use App\Events\UserRegisterEvent;
use App\Jobs\MailJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisterListener implements ShouldQueue
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
     * @param  UserRegisterEvent  $event
     * @return void
     */
    public function handle(UserRegisterEvent $event)
    {
        // 邮件发送
//        $this->dispatch(new MailJob());
        MailJob::dispatch('name');
    }

    /**
     * 失败事件处理器
     *
     * @param  \App\Events\UserRegisterEvent  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(UserRegisterEvent $event, $exception)
    {
        //
    }
}
