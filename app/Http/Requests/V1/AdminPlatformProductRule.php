<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class AdminPlatformProductRule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'name' => 'required|bail',
            'detail' => 'required|bail',
            'category_id' => 'required|numeric',
            'api_path' => 'required',
            'internal_api_path' => 'required',
            'request_method' => 'required',
            'internal_request_method' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '缺少服务产品名',
            'detail.required' => '缺少服务产品的描述',
            'category_id.required' => '缺少所属分类',
            'category_id.numeric' => '所属分类id必须为数字',
            'api_path.required' => '缺少请求uri',
            'internal_api_path.required' => '缺少内部映射的请求uri',
            'request_method.required' => '缺少请求方式',
            'internal_request_method' => '缺少内部请求方式'
        ];
    }
}
