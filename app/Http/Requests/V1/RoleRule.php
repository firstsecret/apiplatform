<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class RoleRule extends FormRequest
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
            'name' => 'required|max:16|bail',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '用户名不能为空',
            'name.max' => '最大长度不能超过16个字符',
        ];
    }
}
