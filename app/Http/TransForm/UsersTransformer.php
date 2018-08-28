<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/28
 * Time: 10:46
 */

namespace App\Http\Api\Transformers;


use League\Fractal\TransformerAbstract;

/**
 * 数据转换，（主要用户保护db 的 字段）
 * Class UsersTransformer
 * @package App\Http\Api\Transformers
 */
class UsersTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return [
            'user_name' => $data['name'],
            'user_email' => $data['email']
        ];
    }
}