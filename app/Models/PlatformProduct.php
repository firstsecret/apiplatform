<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformProduct extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'detail', 'category_id'];

    //
    public function getList($paginte = 15, $type = 'default'): Array
    {
//        dd('4234');
//        $where = $type == 'default' ? []: ['type',$type];
        return $this->paginate($paginte, ['name', 'detail', 'type'])->toArray();
    }

    public function category()
    {
        return $this->belongsTo('App\Models\PlatformProductCategory');
    }
}
