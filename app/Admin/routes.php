<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('auth/frontUsers', UserController::class);
    $router->resource('services', NodeServicesController::class);
    $router->resource('platformProduct', PlatformProductController::class);
    $router->resource('platformProductCategory', PlatformProductCategoryController::class);

    $router->get('/horizon', 'HorizonController@index');
    $router->get('/supervisor', 'SupervisorController@index');

    //custom admin api
    $router->group(['prefix' => 'api'], function (Router $router) {
        $router->delete('unbindServicePlatformProduct/{service_id}/{product_id}', NodeServicesController::class . '@unbindServicePlatformProduct');
    });
});
