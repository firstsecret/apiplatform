<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 15:13
 */

namespace Show\Api\V1;


use App\Http\Api\BaseController;
//use App\Http\Controllers\Controller;
use Show\Services\IndexService;

class IndexController extends BaseController
{
    public function __construct(IndexService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->service->oneService();
    }
}