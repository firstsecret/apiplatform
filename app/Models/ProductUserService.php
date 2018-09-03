<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUserService extends Model
{
    //
    protected $fillable = ['user_id', 'platform_product_id'];

    public $timestamps = false;

    protected $casts = [
        'platform_product_id' => 'array',
    ];

//    /**
//     * 获取用户的对应的产品属性
//     * @param $value
//     * @return mixed
//     */
//    public function getPlatformProductIdAttribute($value)
//    {
//        return json_decode($value, true);
//    }
//
//    /**
//     *设置用户的对应的产品属性
//     * @param $value
//     */
//    public function setPlatformProductIdAttribute($value)
//    {
//        $this->attributes['platform_product_id'] = json_encode($value);
//    }
}
