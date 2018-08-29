<?php

namespace App\Http\Controllers\Admin\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct()
    {
        $this->permission = new \Spatie\Permission\Models\Permission();
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
            'detail' => $request->input('detail','')
        ]);

        return redirect('/admin/index');
    }
}
