<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    public function products()
    {
        return $this->belongsToMany('App\Models\PlatformProduct','product_services','service_id','product_id');
    }
}
