<?php

namespace App\Models\Admin;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends \Spatie\Permission\Models\Role
{
    protected $guard_name='admin'; //指定当前守卫者
    //
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->guard_name),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            'model_id'
        );
    }
}
