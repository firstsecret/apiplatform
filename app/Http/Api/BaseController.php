<?php

namespace App\Http\Api;

use App\Tool\AppTool;
use Illuminate\Http\Request;
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

    public function tokenResponse($token)
    {
        return $token === false ? $this->responseClient(400,'登录失败,账号或密码错误',[]) :  $this->responseClient(200, '登录成功', ['access_token' => 'Bearer' . $token]);
    }
}
