<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
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

        $request->validate([
            'name' => 'required|max:16|bail',
        ],['name.required'=>'角色名不能为空','name.max'=>'名称长度不能超过16个字符']);

        $this->role->create([
            'name' => $request->name,
            'detail' => $request->detail
        ]);

        return redirect('/admin/index');
    }

    /**
     *  角色 权限
     */
    public function rolePermission()
    {
        
    }
}
