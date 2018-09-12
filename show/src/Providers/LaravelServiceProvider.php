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
    public function boot()
    {
        // TODO: Implement boot() method.

        $path = realpath(__DIR__.'/../../config/show.php');

        $this->publishes([$path => config_path('showtest.php')], 'config');
        $this->mergeConfigFrom($path, 'showtest');
//        var_dump(config_path('showtest.php'));
//        var_dump($path);
//        $this->registerAliases();
    }

    public function registerAliases()
    {
        $this->app->alias('show.service', IndexService::class);
    }
}