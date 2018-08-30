<?php

namespace App\Http\Controllers\Admin\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    //
    public function index()
    {

    }

    /**
     *  添加权限
     */
    public function add(Request $request)
    {
        if($request->isMethod('get')){
            return view('admin.manage.admin-permission-add');
        }


        $request->validate([
           'name' => 'required|max:16'
        ],['name.required'=> '权限名称必须','name.max'=>'权限名称最长16个字符']);

        $this->permission->create([
            'name' => $request->input('name'),
            'detail' => $request->input('detail',''),
            'guard_name' => 'admin'
        ]);

        return redirect('/admin/index');
    }
}
