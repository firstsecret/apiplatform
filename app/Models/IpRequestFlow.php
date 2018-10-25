<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpRequestFlow extends Model
{
    //
    protected $fillable = ['ip', 'request_uri', 'today_total_number'];
}
