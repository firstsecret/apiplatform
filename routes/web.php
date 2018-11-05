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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/web', function () {
    var_dump('web show');
});

Route::get('testCon', '\App\Http\Api\V1\ShowController@testNewLua');

Route::get('testLua', function () {
    var_dump('这里是testLua');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/services/api/testCommand','\App\Http\Api\V1\ShowController@testCommand');

