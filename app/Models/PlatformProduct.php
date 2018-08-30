<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformProduct extends Model
{
    //
    public function getList($paginte = 15, $type='default')
    {
        $where = $type == 'default' ? []: ['type',$type];
        return $this->simplePaginate($paginte)->get($where);
    }
}
