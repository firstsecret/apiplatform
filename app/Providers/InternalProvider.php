<?php

namespace App\Providers;

use App\Services\InternalService;
use Illuminate\Support\ServiceProvider;

class InternalProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('Internal', function () {
            return new InternalService();
        });
    }
}
