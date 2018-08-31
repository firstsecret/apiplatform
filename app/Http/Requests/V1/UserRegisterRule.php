<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
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
            'name' => 'required|max:32|bail',
            'email' => 'required|email|bail',
            'password' => 'required|max:32|min:6|bail'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '登录账号不能为空',
            'name.max' => '登录账号不能超过32个字符',
            'email.email' => '邮箱格式有误',
            'email.required' => '邮箱必须存在',
            'password.required' => '密码不能为空',
            'password.max' => '密码长度不能超过32个字符',
            'password.min' => '密码长度不能少于6个字符',
        ];
    }
}
