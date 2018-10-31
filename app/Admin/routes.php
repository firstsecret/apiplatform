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
    $router->resource('flow', FlowController::class);

    $router->get('/horizon', 'HorizonController@index');
    $router->get('/supervisor', 'SupervisorController@index');

    // server conf
    $router->group(['prefix' => 'server'], function (Router $router) {
        $router->get('/conf/{setting_conf}', 'ServerConfigController@settingConf');
        // update
        $router->put('/conf/{setting_conf}', 'ServerConfigController@updateConf');
    });

    // custom nginx service viewer
    $router->get('/nginxLog/{file?}', 'NginxLogController@index')->name('custom-nginx-log');

    //custom admin api
    $router->group(['prefix' => 'api'], function (Router $router) {
        $router->delete('unbindServicePlatformProduct/{service_id}/{product_id}', NodeServicesController::class . '@unbindServicePlatformProduct');
    });
});
