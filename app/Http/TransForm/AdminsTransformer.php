<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 10:20
 */

namespace App\Http\TransForm;


class AdminsTransformer extends Transformer
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
            'user_email' => $data['email'],
            'user_telephone' => $data['telephone']
        ];
    }
}