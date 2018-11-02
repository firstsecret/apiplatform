<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    const MODEL_CREATED_EVENT = 'created';
    const MODEL_UPDATED_EVENT = 'updated';

    protected $fillable = [
        'app_key', 'app_secret', 'user_id', 'model'
    ];

    //
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\PlatformProduct', 'app_key_products', 'app_key_id', 'product_id');
    }

}
