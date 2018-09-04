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
     * 添加新的 产品 服务
     * @param PlatformProductRule $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(PlatformProductRule $request)
    {
        // add product
        $category_id = (int)$request->input('category_id');

        if (!is_numeric($category_id) && $category_id == 0) {
            return $this->responseClient(400, '分类id有误', []);
        }

        $reqData = [
            'name' => $request->input('name'),
            'detail' => $request->input('detail', ''),
            'category_id' => $category_id
        ];

        $res = PlatformProduct::addProduct($reqData);

        return $this->res2Response($res, '新增成功', '新增失败');
    }

    public function edit(PlatformProductRule $request, $product_id)
    {
        $reqData = [
            'name' => $request->input('name'),
            'detail' => $request->input('detail', '')
        ];
        $res = PlatformProduct::updateProduct($reqData, $product_id);

        return $this->res2Response($res, '编辑成功', '编辑失败');
        // edit product
    }

    /**
     * 下架删除 某一个 产品服务
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($product_id)
    {
        $res = PlatformProduct::deleteProduct($product_id);

        return $this->res2Response($res, '删除成功', '删除失败');
    }

    /**
     * 测试
     * @return \Illuminate\Http\JsonResponse
     */
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

        $product_ids = $request->input('reqData.product_ids') == '' ? config('platformProduct.defaultProductService') : $request->input('reqData.product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);
        $res = PlatformProFacade::openService($product_ids, $user_id);

        return $this->res2Response($res, '开通成功', '开通失败');
    }

    /**
     * 禁用某一用户的 某些/个 产品 服务
     * @param Request $request
     */
    public function disableUserService(Request $request, $user_id)
    {


        $product_ids = $request->input('reqData.product_ids');

        $product_ids = is_numeric($product_ids) ? [(int)$product_ids] : json_decode($product_ids, true);
        $this->checkProductArr($product_ids);

        $res = PlatformProFacade::disableUserService($user_id, $product_ids);

        return $this->res2Response($res, '操作成功', '操作失败');
    }
}
