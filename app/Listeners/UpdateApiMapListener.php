<?php

namespace App\Listeners;

use App\Events\UpdateApiMapEvent;
use App\Jobs\UpdateApiMap;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateApiMapListener
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
     * @param  UpdateApiMapEvent $event
     * @return void
     */
    public function handle(UpdateApiMapEvent $event)
    {
        // 异步更新api map
        UpdateApiMap::dispatch();
    }
}
