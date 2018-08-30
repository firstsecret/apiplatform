<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformProduct extends Model
{
    //
    public function getList($paginte = 15, $type = 'default')
    {
//        dd('4234');
//        $where = $type == 'default' ? []: ['type',$type];
        return $this->paginate($paginte,['name','detail','type'])->toArray();
    }
}
