<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 15:13
 */

namespace Show\Api\V1;


use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo '应用1服务类 接口';
    }
}