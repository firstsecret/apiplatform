<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Models\Admin\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    protected $role;

    public function __construct()
    {
        $this->role = new Role();
    }

    //
    public  function  index()
    {
        //  all roles

    }
}
