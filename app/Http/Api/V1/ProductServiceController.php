<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;

class ProductServiceController extends BaseController
{
    //
    public function openService($product_id)
    {
        PlatformProFacade::openService($product_id);
    }

    /**
     * 更新权限
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

        return $this->productServiceResponse($res, '更新成功', '更新失败', 200, 500);
//        return $res === false ? $this->responseClient(500, '更新失败', []) : $this->responseClient(200, '更新成功', []);
    }

    public function addService($product_id)
    {
        $res = PlatformProFacade::addService($product_id);

        return $this->productServiceResponse($res, '开通成功', '开通失败', 200, 500);
    }

    /**
     * 产品信息统一返回
     * @param $res
     * @param $successMsg
     * @param string $errorMsg
     * @param int $successCode
     * @param int $errorCode
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function productServiceResponse($res, $successMsg, $errorMsg = '', $successCode = 200, $errorCode = 400, $data = [])
    {
        return $res === false ? $this->responseClient($successCode, $successMsg, $data) : $this->responseClient($errorCode, $errorMsg, $data);
    }
}
