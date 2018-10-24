<?php

namespace App\Listeners;

use App\Events\ReloadServerEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReloadServerListener
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
     * @param  ReloadServerEvent  $event
     * @return void
     */
    public function handle(ReloadServerEvent $event)
    {
        // relaod server
    }
}
