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
        return Response()->json(['status_code' => $status_code, 'msg' => $msg, 'respData' => $data]);
    }
}
