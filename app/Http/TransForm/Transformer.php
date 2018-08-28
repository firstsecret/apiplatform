<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/28
 * Time: 15:51
 */

namespace App\Http\TransForm;

use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
    /**
     * 针对多个数据
     * @param $data
     */
    public function  TransformCollection($data){
        //要调用当子类的transform
        return array_map([$this,'transform'],$data->toArray());
    }

    /**
     *
     * 针对单个内容
     * @param $data
     * @return mixed
     */
    public abstract function transform($item);
}