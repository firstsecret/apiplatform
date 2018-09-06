<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 15:13
 */

namespace Show\Api\V1;


use App\Http\Api\BaseController;
use Show\Services\IndexService;

class IndexController extends BaseController
{
    public function __construct(IndexService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->oneService();

        return $this->responseClient(200, '获取成功', $data);
    }

    public function testCurl()
    {
        $data = $this->service->onCurl();

        return $this->responseClient(200, 'success', $data);
    }
}