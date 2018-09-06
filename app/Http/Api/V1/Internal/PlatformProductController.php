<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\PlatformProduct;
use App\Http\Api\AdminBaseController;
use App\Http\Requests\V1\PlatformProductRule;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;


class PlatformProductController extends AdminBaseController
{
    /**
     * 给 某一用户 开通 产品服务
     * @param Request $request
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
     * 禁用某一用户的 某些/个 产品 服务
     * @param Request $request
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
