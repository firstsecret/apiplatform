<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\Internal;
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
        $reqData = json_decode($request->input('reqData'), true);

        // check
        $request->validate([
            'reqData.*.name' => 'required|max:16|min:3',
            'reqData.*.telephone' => 'required|numeric',
            'reqData.*.password' => 'required|max:16|min:6',
            'reqData.*.type' => 'between:0,1',
//            'reqData.*.email' => 'email'
        ], [
            'reqData.*.name.required' => '用户名称必须',
            'reqData.*.name.max' => '用户名长度不能超过16位',
            'reqData.*.name.min' => '用户名长度不能少于3位',
            'reqData.*.telephone.required' => '手机号码必须',
            'reqData.*.telephone.numeric' => '手机号码必须为纯数字',
            'reqData.*.type.between' => 'type只支持0或1',
//            'reqData.*.email.email' => '请输入正确的邮箱'
        ]);

        $resWithData = Internal::openUser($reqData);

        return $this->res2Response($resWithData['res'], '用户创建成功', '用户创建失败', $resWithData['data']);
    }

    /**
     * 测试生成sign
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSign()
    {
        $sequenceID = 123456;

        $appKey = 'a3bc712c326d9c84655cd60d16cc640d';

        $appSecret = '0690009e3d2560f8b0b8fbaceb4bf813';

        $reqData = [
            'name' => 'xiaowang2',
            'telephone' => 13913057593,
            'password' => 123456,
            'type' => 1
        ];
//        var_dump(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        $sign = md5(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);

        return $this->responseClient(200, '测试sign与请求data生成', ['sign' => $sign, 'reqData' => $reqData]);
    }
}
