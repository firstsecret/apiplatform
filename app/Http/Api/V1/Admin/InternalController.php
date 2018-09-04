<?php

namespace App\Http\Api\V1\Admin;

use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;

//use App\Http\Controllers\Controller;

class InternalController extends AdminBaseController
{
    /**
     *  内部开通 新的用户
     */
    public function openUser(Request $request)
    {
        $reqData = $request->input('reqData');


    }

    public function testSign()
    {
        $sequenceID = 123456;

        $appKey = '439d8c975f26e5005dcdbf41b0d84161';

        $appSecret = '08aee6276db142f4b8ac98fb8ee0ed1b';

        $reqData = [
            'name' => '小王',
            'age' => 18
        ];
//        var_dump(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        $sign = md5(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);

        return $this->responseClient(200, '测试sign与请求data生成', ['sign' => $sign, 'reqData' => $reqData]);
    }
}
