<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/27
 * Time: 16:27
 */

namespace App\Http\Requests;


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
            'name' => 'required|max:32',
            'email' => 'required|unique:users',
            'password' => 'required|max:16|min:3'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '用户名不能为空',
            'name.max' => '用户名不能为空',
            'email.required' => '邮箱不能为空',
            'email.unique' => '该邮箱已被注册',
            'password.required' => '密码不能为空',
            'password.max' => '密码不能为空',
            'password.min' => '密码不能为空',
        ];
    }
}