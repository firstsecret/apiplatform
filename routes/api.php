<?php

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//$dispatcher = app('Dingo\Api\Dispatcher');

app('api.exception')->register(function (Exception $exception) {
//    if(config('app.debug')){
//        $request=Request::capture();
//        //交给laravelz自带的错误异常接管类处理
//        return app('App\Exceptions\Handler')->render($request,$exception);
//    }else{
//
//        if( get_class($exception)=='Illuminate\Validation\ValidationException'){
//            return Response()->json(['status_code'=>422,'message'=>$exception->validator->errors(),'data'=>''],422);
//        }
//
//    }
//    return Response()->json(['status_code'=>$exception->getStatusCode(),'message'=>$exception->validator->errors(),'data'=>''],$exception->getStatusCode());
//    var_dump($exception);
    if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
        return Response()->json(['status_code' => $exception->getStatusCode(), 'message' => $exception->getMessage(), 'data' => ''], $exception->getStatusCode());
    } else if (get_class($exception) == 'Illuminate\Validation\ValidationException') {
        return Response()->json(['status_code' => 422, 'message' => $exception->validator->errors(), 'data' => ''], 422);
    } else {
        $status_code = $exception->getCode() == 0 ? 400 : $exception->getCode();
        $err_message = $exception->getMessage() == '' ? '路由不存在' : $exception->getMessage();
        return Response()->json(['status_code' => $status_code, 'message' => $err_message, 'data' => ''], $status_code);
    }
});

$api = app('Dingo\Api\Routing\Router');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
// 项目 应用1
$api->version('v1',[], function($api){
    $api->get('showtest', \Show\Api\V1\IndexController::class . '@index');
});
//....


$api->version('v1', ['middleware' => 'api.throttle', 'namespace' => '\App\Http\Api\V1'], function ($api) {
    $api->group(['prefix' => 'cli'], function ($api) {
        // 無需授权api
        $api->group([], function($api){

            $api->group(['limit'=>300,'expires'=>5], function($api){
                $api->post('login', AuthController::class . '@login');

                $api->post('register', AuthController::class . '@register');
            });

            $api->group(['limit'=>200,'expires'=>10], function($api){
                // 生成 access_token
                $api->get('token', ApiAuthController::class . '@getAccessToken')->middleware('checkAppKeySecret');
            });

            $api->get('test', ShowController::class . '@index');

            $api->get('testEvent', ShowController::class . '@testEvent');


            $api->get('productList/{type?}',PlatformProductController::class. '@index');
//            $api->group(['namespace'=>''], function($api){
//
//            })

            $api->get('categoriesList', PlatformProductController::class . '@allList');

        });
        // 后台的api

        // 需授权的 api
        $api->group(['middleware' => ['jwt.auth']], function ($api) {

            $api->group(['limit' => 10, 'expires' => 1], function ($api) {
                $api->get('showauth', ApiAuthController::class . '@test');
                $api->get('getUserInfo', ApiAuthController::class . '@uInfo');
            });

            $api->group(['limit' => 300, 'expires' => 5], function ($api) {
                // 刷新token
                $api->get('refreshToken', ApiAuthController::class . '@refreshAccessToken');
            });
        });
    });
});
