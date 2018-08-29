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




Route::group(['namespace'=> 'App\Http\Controllers\Admin'], function (){
    Route::get('/index', function () {
        dd('这里是后台首页');
    });

    Route::get('/adminAdd', \Manage\AdminController::class . '@add');

    // 验证权限
    Route::group(['middleware' =>'admin.role:admin,operations|admins'], function(){
        Route::get('/web',function(){
            var_dump('web show');
        });
    });

    Route::get('login', 'LoginController@showLoginForm')->name('admin/login');
    Route::post('login', 'LoginController@login');
});