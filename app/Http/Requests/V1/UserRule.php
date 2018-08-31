<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/27
 * Time: 16:27
 */

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserRule extends FormRequest
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
            'login_name' => 'required|max:32|bail',
            'password' => 'required|max:32|min:6|bail'
        ];
    }

    public function messages()
    {
        return [
            'login_name.required' => '登录账号不能为空',
            'login_name.max' => '登录账号不能超过32个字符',
            'password.required' => '密码不能为空',
            'password.max' => '密码长度不能超过32个字符',
            'password.min' => '密码长度不能少于6个字符',
        ];
    }
}