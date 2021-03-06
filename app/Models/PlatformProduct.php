<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformProduct extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'detail', 'category_id', 'api_path', 'internal_api_path', 'request_method', 'internal_request_method', 'last_old_api_path'];

    const ONLINE_STATUS = '已上线';

    const DOWNLINE_STATUS = '未上线';

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

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'product_services', 'product_id', 'service_id');
    }

    public function appuser()
    {
        return $this->belongsToMany('App\Models\AppUser', 'app_key_products', 'product_id', 'app_key_id');
    }

    public function getTmpLastOldApiPathAttribute()
    {
        return "{$this->api_path}";
    }

    public function getIsOnlineAttribute($value)
    {
        return $value ? self::ONLINE_STATUS : self::DOWNLINE_STATUS;
    }

    public function setOnlineAttribute($value)
    {
        if ($value == self::ONLINE_STATUS || $value == 1) {
            $this->attributes['is_online'] = 1;
        } else {
            $this->attributes['is_online'] = 0;
        }
    }

    public function getRequestMethodAttribute($value)
    {
        return strtoupper($value);
    }

    public function getInternalRequestMethodAttribute($value)
    {
        return strtoupper($value);
    }

    public function setRequestMethodAttribute($value)
    {
        $this->attributes['request_method'] = strtoupper($value);
    }

    public function setInternalRequestMethodAttribute($value)
    {
        $this->attributes['internal_request_method'] = strtoupper($value);
    }
}
