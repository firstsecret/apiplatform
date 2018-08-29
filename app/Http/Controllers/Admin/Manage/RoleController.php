<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Models\Admin\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::guard('admin')->user();

        dd($user);
    }

    public function add(Request $request)
    {
        if($request->isMethod('get')){
            return view('admin.manage.admin-role-add');
        }
    }
}
