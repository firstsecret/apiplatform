<?php

namespace App\Http\Api\V1\Internal;

use App\Facades\Internal;
use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;


/**
 * Class InternalController
 * @author Bevan
 * @Resource("Internal/Internal")
 * @package App\Http\Api\V1\Internal
 */
class InternalController extends AdminBaseController
{
    /**
     * 开通新的用户
     *
     * 开通新的用户 返回openid , 如果 满足 内部uuid机制 已存在将返回 对应的 openid 并 提示， 没有则 新增该用户 并生成openid 返回
     *
     * @Post("/cli/admin/openUser")
     * @Response(200, body={"status_code":200,"message": "success", "respData": {"openid": "openid"}})
     * @Request("{'sign':'sign','appKey':'appKey','sequenceId':'sequenceId','reqData':{'name':'name','telephone':'telephone','password':'password','type':'type'}}",contentType="application/x-www-form-urlencoded",headers={"Authorization":"token"})
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

        return $this->res2Response($resWithData['res'], '用户创建成功', $resWithData['errormsg'] ?? '用户创建失败', $resWithData['data'], 200, 403);
    }

    public function testSign()
    {
        $sequenceID = 123456;

        $appKey = 'a3bc712c326d9c84655cd60d16cc640d';

        $appSecret = '0690009e3d2560f8b0b8fbaceb4bf813';

        $reqData = [
            'name' => 'xiaowang23542',
            'telephone' => 13913078563,
            'password' => 123456,
            'type' => 0
        ];
//        var_dump(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        //{"name":"小王","age":18}12345608aee6276db142f4b8ac98fb8ee0ed1b
        $sign = md5(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceID . $appSecret);

        return $this->responseClient(200, '测试sign与请求data生成', ['sign' => $sign, 'reqData' => $reqData]);
    }
}
