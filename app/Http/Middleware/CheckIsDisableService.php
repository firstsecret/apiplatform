<?php

namespace App\Http\Middleware;

use App\Exceptions\PlatformProductException;
use App\Models\PlatformProduct;
use App\Models\ProductUserDisableService;
use Closure;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckIsDisableService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 验证 授权用户 是否 允许访问 和 开通 该服务
        $user = JWTAuth::parseToken()->user();

        // 根据 request path 获取 对应的 产品 id
        $product_id = $this->getProductId($request);

        $this->checkIsDisable($user->id, $product_id);

        return $next($request);
    }

    /**
     *  验证是否 可以 访问该 服务
     * @param $user_id
     * @param $product_id
     */
    protected function checkIsDisable($user_id, $product_id)
    {
        $disable_service = [];

        if (Cache::has('user_disable_service')) {
            $user_disable_service = Cache::get('user_disable_service');

            if (isset($user_disable_service[$user_id])) $disable_service = json_decode($user_disable_service[$user_id]['platform_product_id'], true);
        } else {
            // db
            $disable_service = ProductUserDisableService::get(['user_id', 'platform_product_id'])->toArray();
            // 拼凑 数组
            $new_d_service = [];
            foreach ($disable_service as $d_service) {
                $new_d_service[$d_service['user_id']] = $d_service;
            }

            Cache::forever('user_disable_service', $new_d_service);

            if (isset($new_d_service[$user_id])) $disable_service = json_decode($new_d_service[$user_id]['platform_product_id'], true);
        }

        if (in_array($product_id, $disable_service)) throw new PlatformProductException('该服务已被禁止,请联系客服咨询原因');
    }

    /**
     * 获取 服务id
     * @param $request
     * @return null
     */
    protected function getProductId($request)
    {
        $pathinfo = ltrim($request->getPathInfo(), '/');

        if (Cache::has('platform_products')) {
            $platform_products = Cache::get('platform_products');
        } else {
            // 兼容 处理
            $platform_products = PlatformProduct::get(['id', 'name', 'detail', 'created_at', 'category_id', 'api_path'])->toArray();
            $new_platform_products = [];

            foreach ($platform_products as $product) {
                $new_platform_products[$product['api_path']] = $product;
            }
            // 永久 存储
            Cache::forever('platform_products', $new_platform_products);

            $platform_products = $new_platform_products;
        }
        
        if (!isset($platform_products[$pathinfo])) throw new PlatformProductException('请求服务不存在,请核对');
//        foreach ($platform_products as $product) {
//            if ($pathinfo == $product['api_path']) {
//                $product_id = $product['id'];
//                break;
//            }
//        }

        return $platform_products[$pathinfo]['id'];
    }
}
