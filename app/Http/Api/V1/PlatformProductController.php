<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Facades\PlatformProduct as PlatformProFacade;

/**
 * Class PlatformProductController
 * @author Bevan
 * @Resource("PlatformProduct")
 * @package App\Http\Api\V1
 */
class PlatformProductController extends BaseController
{

    /**
     * api平台可以接入产品列表
     *
     * 可按类型 默认全部 分页
     *
     * @Get("/cli/productList/{?type}")
     * @Response(200, body={"status_code":200,"message":"success","reqData":{"list":"list"}})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($type = 'default')
    {
        $list = PlatformProFacade::productList($type);

        return $this->responseClient(200,'success',$list);
    }

    /**
     * api平台可接入产品分类列表
     *
     * 按分类排序 归类完毕 便于前端展示
     *
     * @Get("/cli/categoriesList")
     * @Response(200, body={"status_code":200,"message":"success","reqData":{"list": "list"}})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @return \Illuminate\Http\JsonResponse
     */
    public function allList()
    {
        $list = PlatformProFacade::getCategoriesWithProduct();

        return $this->responseClient(200,'success',$list);
    }
}
