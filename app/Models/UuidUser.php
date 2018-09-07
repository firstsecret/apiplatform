<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UuidUser extends Model
{
    //
    protected $fillable = [
        'user_id', 'model_id', 'model_uuid', 'openid', 'model'
    ];
}
