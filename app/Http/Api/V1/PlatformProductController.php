<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Facades\PlatformProduct as PlatformProFacade;

class PlatformProductController extends BaseController
{
    public function __construct()
    {

    }

    //
    public function index($type = 'default')
    {
        $list = PlatformProFacade::productList($type);

        return $this->responseClient(200,'success',$list);
    }

    /**
     * 获取产品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function allList()
    {
        $list = PlatformProFacade::getCategoriesWithProduct();

        return $this->responseClient(200,'success',$list);
    }
}
