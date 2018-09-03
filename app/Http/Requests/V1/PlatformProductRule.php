<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class PlatformProductRule extends FormRequest
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
            'name' => 'required|max:16|min:3|bail',
            'detail' => 'max:64|bail'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '产品名称必须存在',
            'name.max' => '产品名称最大长度不能超过16个字符',
            'name.min' => '产品名称不能少于3个字符',
            'detail.max' => '描述不能超过64个字符'
        ];
    }
}
