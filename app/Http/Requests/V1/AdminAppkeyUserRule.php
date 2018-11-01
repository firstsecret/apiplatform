<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/1
 * Time: 10:22
 */

namespace App\Http\Requests\V1;


use Illuminate\Foundation\Http\FormRequest;

class AdminAppkeyUserRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //
            'id' => 'required|integer|bail',
//            'user_id' => 'required|integer|bail',
            'products' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => '缺少appkey id',
            'id.integer' => 'appkey id必须为整数',
//            'user_id.required' => '缺少用户id',
//            'user_id.integer' => '用户id必须为整数',
            'products.required' => '缺少修改服务产品',
            'products.array' => '服务产品必须为一个数组'
        ];
    }
}