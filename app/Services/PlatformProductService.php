<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/30
 * Time: 10:34
 */

namespace App\Services;


use App\Models\PlatformProduct;

class PlatformProductService
{
    /*
     *  產品列表獲取
     */
    public function productList(PlatformProduct $platformProduct)
    {
        $paginte = config('platformProduct.paginte');
        $platformProduct->getList($paginte);
    }
}