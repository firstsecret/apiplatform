<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 16:23
 */

namespace App\Services;

use App\Tool\AppTool;
use Illuminate\Support\Facades\Cache;

abstract class BaseService
{
    use AppTool;

    public function __construct()
    {

    }
}