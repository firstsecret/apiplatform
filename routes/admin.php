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




Route::group([], function (){
    Route::get('/', function () {
        dd('这里是后台首页');
    });

    // 验证权限
    Route::group(['middleware' =>'admin.role:admin,operations|admins'], function(){
        Route::get('/web',function(){
            var_dump('web show');
        });
    });

    Route::get('/login', function(){
       var_dump('登录页面');
    });
});