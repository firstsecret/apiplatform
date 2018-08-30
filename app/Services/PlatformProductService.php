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

    public function productList(PlatformProduct $platformProduct, $page, $limit)
    {
        $platformProduct->getList($page, $limit);
    }
}