<?php
/**
 * Created by PhpStorm.
 * User: Bevan@569072412@qq.com
 * Date: 2018/8/30
 * Time: 11:16
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class PlatformProduct extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PlatformProduct';
    }
}