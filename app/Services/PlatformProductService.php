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
    public function __construct()
    {
        $this->platformProduct = new PlatformProduct();
    }

    /*
     *  產品列表獲取
     */
    public function productList($type = 'default')
    {
        $paginte = config('platformProduct.paginte');

        return $this->platformProduct->getList($paginte, $type);
    }
}