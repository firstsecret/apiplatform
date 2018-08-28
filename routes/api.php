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
        return Response()->json(['status_code' => $exception->getStatusCode(), 'message' => $exception->validator->errors(), 'data' => ''], $exception->getStatusCode());
    } else if (get_class($exception) == 'Illuminate\Validation\ValidationException') {
        return Response()->json(['status_code' => 422, 'message' => $exception->validator->errors(), 'data' => ''], 422);
    } else {
        return Response()->json(['status_code' => $exception->getCode(), 'message' => $exception->getMessage(), 'data' => ''], 500);
    }
});

$api = app('Dingo\Api\Routing\Router');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
$api->version('v1', ['namespace' => '\App\Http\Api\V1'], function ($api) {
    $api->group(['prefix' => 'cli','middleware' => 'api.throttle', 'limit' => 10, 'expires' => 1, ], function ($api) {
        $api->get('test', ShowController::class . '@index');

        $api->post('login', AuthController::class . '@login');

        $api->post('register', AuthController::class . '@register');

        $api->group(['middleware' => ['jwt.auth']], function ($api) {
            $api->get('showauth', ApiAuthController::class . '@test');
        });

        // 生成 access_token
        $api->get('token', ApiAuthController::class . '@getAccessToken')->middleware('checkAppKeySecret');

        // 刷新token
        $api->group(['middleware'=>'api.throttle','jwt.auth'], function ($api){
            $api->get('refreshToken', ApiAuthController::class . '@refreshAccessToken');
        });

        // 授权的 api
        $api->group(['middleware' => ['checkAccessToken', 'jwt.auth']], function ($api) {
            $api->get('getUserInfo', ApiAuthController::class . '@uInfo');
        });
    });
});
