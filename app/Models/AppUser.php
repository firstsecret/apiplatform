<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $fillable = [
        'app_key', 'app_secret', 'user_id','model'
    ];

    //
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
