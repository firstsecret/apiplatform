<?php

namespace App\Http\Api\V1;


use App\Http\Api\BaseController;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;

/**
 * Class ProductServiceController
 * @author Bevan
 * @Resource("ProductService")
 * @package App\Http\Api\V1
 */
class ProductServiceController extends BaseController
{
    /**
     *  api平台接入用户更新开通的服务
     *
     *  api平台接入用户更新开通的服务
     *
     * @Post("/cli/editServiceSelf")
     * @Response(200, body={"status_code":200,"message":"成功","respData":""})
     * @Request("{'product_ids':'product_ids'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editService(Request $request)
    {
        $product_ids = json_decode($request->input('product_ids'), true);
        $this->checkProductArr($product_ids);
        if (empty($product_ids)) return $this->responseClient(400, '未获取到服务产品信息', []);

        $res = PlatformProFacade::editService($product_ids);

        return $this->res2Response($res, '更新成功', '更新失败', 200, 500);
//        return $res === false ? $this->responseClient(500, '更新失败', []) : $this->responseClient(200, '更新成功', []);
    }

    /**
     * api平台接入用户开通服务
     *
     * api平台接入用户开通服务
     *
     * @Post("/cli/openServiceSelf/{product_id}")
     * @Response(200, body={"status_code":200,"message":"成功","respData":""})
     * @Request("{'product_id':'product_id'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addService($product_id)
    {
        $res = PlatformProFacade::addService($product_id);

        return $this->res2Response($res, '开通成功', '开通失败', 200, 500);
    }

    /**
     * api平台接入用户关闭服务
     *
     * api平台接入用户关闭服务
     *
     * @Post("/cli/delService/{product_id}")
     * @Response(200, body={"status_code":200,"message":"成功","respData":""})
     * @Request("{'product_id':'product_id'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delService($product_id)
    {
        $res = PlatformProFacade::delService($product_id);

        return $this->res2Response($res, '关闭成功', '关闭失败', 200, 500);
    }
}
