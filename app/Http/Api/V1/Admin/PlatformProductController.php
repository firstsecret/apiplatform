<?php

namespace App\Http\Api\V1\Admin;

use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;
use App\Facades\PlatformProduct as PlatformProFacade;


class PlatformProductController extends AdminBaseController
{
    //
    public function add()
    {
        // add product
    }

    public function edit()
    {
        // edit product
    }

    public function delete()
    {

    }

    public function plist()
    {

    }

    public function index()
    {
        return $this->responseClient(200, 'api需认证请求', []);
    }

    public function test()
    {
        return $this->responseClient(200, 'success', []);
    }

    /**
     * 给 某一用户 开通 产品服务
     * @param Request $request
     */
    public function openService(Request $request, $user_id)
    {
//        $request->validate([
//            'user_id' => 'required|integer|bail',
//        ], ['user_id.required' => '用户id必传', 'user_id.integer' => '用户id必须为整型']);
//        if (!is_numeric($user_id)) {
//            return $this->responseClient(400, '用户id必须为数字', []);
//        }

        $product_ids = $request->input('product_ids') == '' ? config('platformProduct.defaultProductService') : $request->input('product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);
        $res = PlatformProFacade::openService($product_ids, $user_id);

        return $this->res2Response($res, '开通成功', '开通失败', 200, 500);
    }

    /**
     * 禁用某一用户的 某些/个 产品 服务
     * @param Request $request
     */
    public function disableUserService(Request $request, $user_id)
    {


        $product_ids = $request->input('product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);

        $res = PlatformProFacade::disableUserService($user_id, $product_ids);

        return $this->res2Response($res, '操作成功', '操作失败', 200, 500);
    }
}
