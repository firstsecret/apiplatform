<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/30
 * Time: 10:34
 */

namespace App\Services;


use App\Exceptions\PlatformProductException;
use App\Models\PlatformProduct;
use App\Models\PlatformProductCategory;
use App\Models\ProductUserDisableService;
use App\Models\ProductUserService;
use App\User;

class PlatformProductService extends BaseService
{

    /**
     * 产品列表获取
     * @param string $type
     * @return Array
     */
    public function productList($type = 'default'): Array
    {
        $paginte = config('platformProduct.paginte');

        return (new PlatformProduct())->getList($paginte, $type);
    }

    /**
     * 分类获取
     * @return Array
     * @throws \Exception
     */
    public function getCategoriesWithProduct(): Array
    {
        // has cache
//        cache()
        if (cache('categoriesWithProduct')) {
            return cache('categoriesWithProduct');
        } else {
            $lists = $this->getCategoriesWithProductFromDb();

            cache(['categoriesWithProduct' => $lists], config('cache.categories_with_product'));

            return $lists;
        }
    }

    /**
     * 获取信息
     * @return Array
     */
    protected function getCategoriesWithProductFromDb(): Array
    {
        $cate_lists = PlatformProductCategory::with('products:id,name,detail,category_id')->get(['id', 'name', 'detail', 'parent_id'])->toArray();

        // sort
        return $this->treeSort($cate_lists);
    }

    /**
     * 为用户开通产品服务 (被动)
     * @param $product_id
     */
    public function openService($product_id, $user_id)
    {
        // 验证用户是否存在

        if (!User::find($user_id)) throw new PlatformProductException(400, '用户不存在');
        return $this->addService($product_id, $user_id);
    }

    /**
     * 禁用用户的产品服务
     * @param $product_ids
     * @param $user_id
     */
    public function disableUserService($product_id, $user_id)
    {
        $user_id = $user_id == null ? $this->user->id : $user_id;

        $disableProducts = ProductUserDisableService::where('user_id', $user_id)->first();

        if ($disableProducts) {
            $platform_product_id = $disableProducts->platform_product_id;
            $platform_product_id = $platform_product_id == null ? [] : $platform_product_id;

            if (is_array($product_id)) {
                $platform_product_id = array_merge($platform_product_id, $product_id);
            } else {
                $platform_product_id[] = $product_id;
            }
            $platform_product_id = array_unique($platform_product_id);

            $disableProducts->platform_product_id = $platform_product_id;

            $res = $disableProducts->save();
        } else {
            $res = ProductUserDisableService::create([
                'user_id' => $user_id,
                'platform_product_id' => $product_id
            ]);
        }

        return $res === false ? false : true;
    }

    /**
     * 开通产品服务 （用户主动）
     * @param $product_id
     */
    public function addService($product_id, $user_id = null)
    {
        $user_id = $user_id == null ? $this->user->id : $user_id;
        $this->checkIsProductDisable($product_id, $user_id);

        $pu = ProductUserService::where('user_id', $user_id)->first();

        if ($pu) {
            $platform_product_id = $pu->platform_product_id;
            $platform_product_id = $platform_product_id == null ? [] : $platform_product_id;
//            array_push($platform_product_id, $product_id);
            if (is_array($product_id)) {
                $platform_product_id = array_merge($platform_product_id, $product_id);
            } else {
                $platform_product_id[] = $product_id;
            }
            $platform_product_id = array_unique($platform_product_id);

            $pu->platform_product_id = $platform_product_id;

            $res = $pu->save();
        } else {
//            dd($product_id);
//            $pu = new ProductUserService();
//            $pu->user_id = $user_id;
//            $pu->platform_product_id = $product_id;
//
//            $res = $pu->save();

            $res = ProductUserService::create([
                'user_id' => $user_id,
                'platform_product_id' => $product_id
            ]);

        }

        return $res === false ? false : true;
    }

    /**
     * 更新用户 开通的产品 对应的服务
     * @param array $products_id
     * @return bool
     */
    public function editService(Array $products_ids, $user_id = null)
    {
        $user_id = $user_id == null ? $this->user->id : $user_id;
        $this->checkIsProductDisable($products_ids, $user_id);

        $res = ProductUserService::where('user_id', $user_id)
            ->update([
                'platform_product_id' => $products_ids
            ]);

        return $res === false ? false : true;
    }

    /**
     * 验证 是否可 开通 该 服务
     * @param $product_ids
     * @return bool
     */
    public function checkIsProductDisable($product_ids, $user_id)
    {
        $users_disable_service = ProductUserDisableService::where('user_id', $user_id)->get()->toArray();

        if (empty($users_disable_service)) return true;

        if (is_string($product_ids)) $product_ids = [$product_ids];

        foreach ($product_ids as $product_id) {
            if (in_array($product_id, $users_disable_service)) throw new PlatformProductException(400, '服务已被禁用', []);
        }

        return true;
    }
}