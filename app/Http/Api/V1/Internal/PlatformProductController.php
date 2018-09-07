<?php

namespace App\Http\Api\V1\Internal;

use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;

/**
 * Class PlatformProductController
 * @author Bevan
 * @Resource("Internal\PlatformProduct")
 * @package App\Http\Api\V1\Internal
 */
class PlatformProductController extends AdminBaseController
{
    /**
     * 内部应用开放接口-- 给某一用户开通某个服务
     *
     * 内部应用开放接口-- 给某一用户开通某个服务
     *
     * @Post("/cli/admin/openUserService")
     * @Response(200, body={"status_code":200,"message": "success", "respData": ""})
     * @Request("{'reqData':{'product_ids':'product_ids', 'openid': 'openid'},'sign':'sign','appKey':'appKey','sequenceId':'sequenceId'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function openService(Request $request)
    {
//        $request->validate([
//            'user_id' => 'required|integer|bail',
//        ], ['user_id.required' => '用户id必传', 'user_id.integer' => '用户id必须为整型']);
//        if (!is_numeric($user_id)) {
//            return $this->responseClient(400, '用户id必须为数字', []);
//        }

        $product_ids = $request->input('reqData.product_ids') == '' ? config('platformProduct.defaultProductService') : $request->input('reqData.product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);
        $res = PlatformProFacade::openService($product_ids);

        return $this->res2Response($res, '开通成功', '开通失败');
    }

    /**
     * 禁用某用户的应用服务api
     *
     * 禁用某用户的应用服务api
     *
     * @Post("/cli/admin/disableUserService")
     * @Response(200, body={"status_code":200,"message": "success", "respData": ""})
     * @Request("{'sign':'sign','appKey':'appKey','sequenceId':'sequenceId','reqData':{'product_ids':'product_ids', 'openid': 'openid'}}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disableUserService(Request $request)
    {
        $product_ids = $request->input('reqData.product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);

        $res = PlatformProFacade::disableUserService($product_ids);

        return $this->res2Response($res, '操作成功', '操作失败');
    }
}
