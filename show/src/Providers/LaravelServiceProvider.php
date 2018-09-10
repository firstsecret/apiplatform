<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/10
 * Time: 16:24
 */

namespace Show\Providers;


use Show\Services\IndexService;

class LaravelServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        // TODO: Implement boot() method.
        $path = realpath(__DIR__ . '/../../config/show.php');

        $this->publishes([$path => config_path('show.php')]);
    }

    public function boot()
    {
        // TODO: Implement boot() method.
        $path = realpath(__DIR__ . '/../../config/show.php');

        $this->publishes([$path => config_path('show.php')]);

//        $this->registerAliases();
    }

    public function registerAliases()
    {
        $this->app->alias('show.service', IndexService::class);
    }
}