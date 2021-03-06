<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/28
 * Time: 10:46
 */

namespace App\Http\TransForm;



/**
 * 数据转换，（主要用户保护db 的 字段）
 * Class UsersTransformer
 * @package App\Http\Api\Transformers
 */
class UsersTransformer extends Transformer
{
    /**
     * 单个转换
     * @param $data
     * @return array
     */
    public function transform($data): Array
    {
//        dd($data);
        return [
            'user_name' => $data['name'],
            'user_email' => $data['email']
        ];
    }
}