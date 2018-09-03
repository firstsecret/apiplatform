<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;

class ProductServiceController extends BaseController
{
    /**
     * 更新产品服务
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editService(Request $request)
    {
        $product_ids = json_decode($request->input('product_ids'), true);

        if (empty($product_ids)) {
            return $this->responseClient(400, '未获取到服务产品信息', []);
        }

        $res = PlatformProFacade::eidtService($product_ids);

        return $this->res2Response($res, '更新成功', '更新失败', 200, 500);
//        return $res === false ? $this->responseClient(500, '更新失败', []) : $this->responseClient(200, '更新成功', []);
    }

    /**
     * 开通产品服务
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addService($product_id)
    {
        $res = PlatformProFacade::addService($product_id);

        return $this->res2Response($res, '开通成功', '开通失败', 200, 500);
    }

    /**
     * 关闭产品服务
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delService($product_id)
    {
        $res = PlatformProFacade::delService($product_id);

        return $this->res2Response($res, '关闭成功', '关闭失败', 200, 500);
    }
}
