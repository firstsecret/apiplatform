<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        // 用户注册事件
        'App\Events\UserRegisterEvent' => [
            'App\Listeners\UserRegisterListener'
        ],
        // 异步日志 事件
        'App\Events\AsyncLogEvent' => [
            'App\Listeners\AsyncLogListener'
        ],
        // 更新 api 映射
        'App\Events\UpdateApiMapEvent' => [
            'App\Listeners\UpdateApiMapListener'
        ],
        // 更新 appkey 映射
        'App\Events\UpdateAppkeyMapEvent' => [
            'App\Listeners\UpdateAppkeyMapListener'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
