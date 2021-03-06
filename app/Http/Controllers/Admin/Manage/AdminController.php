<?php
namespace App\Http\Controllers\Admin\Manage;

use App\Models\Admin;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AdminController extends Controller
{
    //
    public function index()
    {

    }

    public function add()
    {
        try{
            Admin::create([
                'name' => 'admin',
                'email' => 'admin@qq.com',
                'telephone' => '123456',
                'password' => bcrypt('123456')
            ]);
        }catch (\Exception $e){
            throw new \Exception('创建失败' . $e->getMessage());
        }

        dd('创建成功');
    }

    public function webAdd()
    {
        User::create(
            ['name' => 'test232','email' => 'test@qq.com', 'password' => bcrypt('123456')]
        );
    }
}
