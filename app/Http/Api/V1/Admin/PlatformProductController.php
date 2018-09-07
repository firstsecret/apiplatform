<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\PlatformProduct;
use App\Http\Api\AdminBaseController;
use App\Http\Requests\V1\PlatformProductRule;

/**
 * Class PlatformProductController
 * @author Bevan
 * @Resource("Admin\PlatformProduct")
 * @package App\Http\Api\V1\Admin
 */
class PlatformProductController extends AdminBaseController
{
    /**
     * 添加产品服务api
     *
     * 添加产品服务api (admin/operator角色)
     *
     * @Post("/cli/admin/platformProduct")
     * @Response(200, body={"status_code":200,"message": "success", "respData": ""})
     * @Request("{'category_id':'category_id', 'name': 'name', 'detail': 'detail'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
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

    /**
     * 编辑产品服务api
     *
     * 编辑产品服务api (admin/operator角色)
     *
     * @Put("/cli/admin/platformProduct/{product_id}")
     * @Response(200, body={"status_code":200,"message": "success", "respData": ""})
     * @Request("{'product_id':'product_id', 'name': 'name', 'detail': 'detail'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     */
    public function edit(PlatformProductRule $request, $product_id)
    {
        $reqData = [
            'name' => $request->input('name'),
            'detail' => $request->input('detail', '')
        ];
        $res = PlatformProduct::updateProduct($reqData, $product_id);

        return $this->res2Response($res, '编辑成功', '编辑失败');
    }

    /**
     * 下架某一个产品服务api
     *
     *  下架产品服务 (admin/operator角色)
     *
     * @Delete("/cli/admin/platformProduct/{product_id}")
     * @Response(200, body={"status_code":200,"message": "success", "respData": ""})
     * @Request("{'product_id':'product_id'}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($product_id)
    {
        $res = PlatformProduct::deleteProduct($product_id);

        return $this->res2Response($res, '删除成功', '删除失败');
    }

    public function index()
    {
        return $this->responseClient(200, 'api需认证请求', []);
    }

    public function test()
    {
        return $this->responseClient(200, 'success', []);
    }
}
