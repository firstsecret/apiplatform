<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/10
 * Time: 16:26
 */

namespace Show\Providers;


use Illuminate\Support\ServiceProvider;

abstract class AbstractServiceProvider extends ServiceProvider
{
    abstract public function boot();
}