<?php

namespace App\Http\Api;

use App\Exceptions\PlatformProductException;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;


class BaseController extends Controller
{
    //
    use Helpers;

    public function responseClient($status_code = 200, $msg = 'success', $data = [])
    {
        return Response()->json(['status_code' => $status_code, 'message' => $msg, 'respData' => $data]);
    }

    /**
     * token类统一返回信息
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokenResponse($token)
    {
        return $token === false ? $this->responseClient(400, '登录失败,账号或密码错误', []) : $this->responseClient(200, '登录成功', ['access_token' => 'Bearer' . $token]);
    }

    /**
     * 信息统一返回
     * @param $res
     * @param $successMsg
     * @param string $errorMsg
     * @param int $successCode
     * @param int $errorCode
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function res2Response($res, $successMsg, $errorMsg = '',$data = [], $successCode = 200, $errorCode = 500)
    {
        return $res === true ? $this->responseClient($successCode, $successMsg, $data) : $this->responseClient($errorCode, $errorMsg, $data);
    }

    /**
     * 验证 请求 的 产品 id
     * @param $product_ids
     */
    protected function checkProductArr($product_ids)
    {
        foreach ($product_ids as $product_id) {
            if (!is_numeric($product_id)) throw new PlatformProductException('400', '产品编号有误');
        }
    }
}
