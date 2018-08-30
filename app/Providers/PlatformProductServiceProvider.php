<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PlatformProductServiceProvider extends ServiceProvider
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
        $this->app->singleton('PlatformProduct', function (){

        });
    }
}
