<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
    public function rolePermission($id)
    {
        // find role

        // edit permission
//        app()['cache']->forget('spatie.permission.cache');

        $role = $this->role->find($id);

        $role->givePermissionTo('edit permission');

        return Response()->json(['status_code'=>200,'msg'=>'success','data'=>'']);
    }

    public function roleAdminUser($user_id)
    {
        $admin = Admin::find($user_id);
//        $admin->givePermissionTo('edit permission');
//        $user->givePermissionTo('edit permission');

        $admin->assignRole('opeartor');

        return Response()->json(['status_code'=>200,'msg'=>'success','data'=>'']);
    }
}
