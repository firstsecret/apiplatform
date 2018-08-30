<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformProductCategory extends Model
{
    //
    public  function products()
    {
        return $this->hasMany('\App\Models\PlatformProduct','category_id','id');
    }
}
