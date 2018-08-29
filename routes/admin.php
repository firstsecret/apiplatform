<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::group(['namespace'=> 'App\Http\Controllers\Admin','middleware'=>['admin']], function (){
    Route::get('/index', function () {
        dd('这里是后台首页');
    });

    Route::get('/register', function(){
       echo '<script>alert(\'未开通\')</script>';
    })->name('admin/register');

    Route::get('/adminAdd', \Manage\AdminController::class . '@add');

    // 验证权限
    Route::group(['middleware' =>'admin.role:admin,operations|admins'], function(){
        Route::get('/web',function(){
            var_dump('web show');
        });

        // role
        Route::match(['get','post'], '/roleAdd','Manage\RoleController@add')->name('admin/roleAdd');

        Route::get('roleMsg', 'Manage\RoleController@index');

        // permission
        Route::match(['get','post'],'/permissionAdd', 'Manage\PermissionController@add')->name('admin/permissionAdd');

    });

    Route::get('login', 'LoginController@showLoginForm')->name('admin/login');
    Route::post('login', 'LoginController@login');

    Route::match(['get','post'], 'logout', 'LoginController@logout')->name('admin/logout');
});