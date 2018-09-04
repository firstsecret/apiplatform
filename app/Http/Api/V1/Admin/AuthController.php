<?php

namespace App\Http\Api\V1\Admin;

use App\Facades\Internal;
use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;

//use App\Http\Controllers\Controller;

class AuthController extends AdminBaseController
{
    //
    public function createNewInternal(Request $request)
    {
        $this->validate([
            'name' => 'required|max:14|min:3|bail',
            'email' => 'email',
            'telephone' => 'required|numeric'
        ], [
            'name.required' => '用户名必须',
            'name.max' => '用户名长度不能超过14个字符',
            'name.min' => '用户名长度不能超过3个字符',
            'email.email' => '邮箱格式错误',
            'telephone.required' => '手机号码必须',
            'telephone.numeric' => '手机号码必须是纯数字'
        ]);

        $reqData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone')
        ];

        $resWithData = Internal::createUser($reqData);

        return $this->res2Response($resWithData['res'], '生成成功', '生成失败', $resWithData['data']);
    }
}
