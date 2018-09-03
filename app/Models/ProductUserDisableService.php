<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUserDisableService extends Model
{
    //
    protected $casts = [
        'platform_product_id' => 'array',
    ];
}
