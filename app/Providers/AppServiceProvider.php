<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);


        // config
        Config::load();
        // sql listen
//        DB::listen(function ($query) {
//            // $query->sql
//            // $query->bindings
//            // $query->time
//            echo $query->sql . ',time:'. $query->time / 1000 . '<br>';
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
