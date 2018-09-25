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

//    dd(get_class($exception));
//    if (config('app.debug')) {
//        $request = Request::capture();
//        //交给laravel自带的错误异常接管类处理
//        return app('App\Exceptions\Handler')->render($request, $exception);
//    }

//    return Response()->json(['status_code'=>$exception->getStatusCode(),'message'=>$exception->validator->errors(),'data'=>''],$exception->getStatusCode());
//    var_dump(get_class($exception));
//    if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
//        $status_code = $exception->getStatusCode();
//        $err_message = $exception->getMessage();
//        $code = $exception->getCode();
////        return Response()->json(['status_code' => $exception->getStatusCode(), 'message' => $exception->getMessage(), 'respData' => ''], $exception->getStatusCode());
//    } else

    if (get_class($exception) == 'Illuminate\Validation\ValidationException') {
        $status_code = 422;
        $err_message = $exception->validator->errors();
        $code = 200;
//        return Response()->json(['status_code' => 422, 'message' => $exception->validator->errors(), 'respData' => ''], 422);
    } else {
//        dd($exception->());
        // is has method getStatusCode
        // status_code
        $status_code = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : $exception->getCode();
        $status_code = $status_code == 0 ? 400 : $status_code;
        // code
        $code = $exception->getCode();
        $code = $code == 0 ? 200 : $code;
//        $err_message = $exception->getMessage() == '' ? '路由不存在:' . request()->getRequestUri() . ',method:' . request()->getMethod() : $exception->getMessage();
        // error_message
        $err_message = $exception->getMessage();
        $err_message = $err_message == '' ? '路由不存在:' . request()->getRequestUri() : $err_message;
//        return Response()->json(['status_code' => $status_code, 'message' => $err_message, 'respData' => ''], $status_code);
    }
//    $err_message = (string)strtr($err_message, [' ' => '', "\n" => '', "\t" => '']);
//    dd(json_encode(['status_code' => $status_code, 'message' => $err_message, 'respData' => []]));
    // 统计 api 失败 请求
    \App\Jobs\CountApiJob::dispatch(ltrim(request()->getPathInfo(), '/'), 'fail');
    // 记录 错误 日志
    \Illuminate\Support\Facades\Event::fire(new \App\Events\AsyncLogEvent($err_message, 'error'));
    return Response()->json(['status_code' => $status_code, 'message' => $err_message, 'respData' => []], $code);
});

$api = app('Dingo\Api\Routing\Router');
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
// 项目 应用1
$api->version('v1', ['middleware' => ['api.throttle', 'self.jwt.refresh:user', 'self.jwt.auth', 'api.count:success'], 'namespace' => '\Show\Api\V1'], function ($api) {
    $api->group(['prefix' => 'app1'], function ($api) {
        $api->get('showtest', IndexController::class . '@index');
        $api->get('show2/{type?}', IndexController::class . '@index');
        $api->get('show3', IndexController::class . '@testCurl');
    });
});
//....

$api->version('v1', ['middleware' => ['api.rewriteResp']], function ($api) {
    $api->get('testLua', '\App\Http\Api\V1\ShowController@testLua');
    $api->post('testLua2', '\App\Http\Api\V1\ShowController@testLua2');
    $api->get('testLua3', '\App\Http\Api\V1\ShowController@testLua3');
    $api->any('testLua4', '\App\Http\Api\V1\ShowController@testLua4');
    $api->get('testAsync', '\App\Http\Api\V1\ShowController@testAsync');
    $api->post('testNewLua', '\App\Http\Api\V1\ShowController@testNewLua');
    $api->get('testCon', '\App\Http\Api\V1\ShowController@testNewLua');
    $api->post('testLua5', '\App\Http\Api\V1\ShowController@testLua5');
    $api->post('testUpload', 'App\Http\Api\V1\ShowController@testUpload');
    $api->get('testNewException', 'App\Http\Api\V1\ShowController@testNewException');
    $api->get('testJWT', 'App\Http\Api\V1\ShowController@testJWT');
    $api->get('getNetJWT', 'App\Http\Api\V1\ShowController@getNetJWT');
    $api->get('appMapRedis', 'App\Http\Api\V1\ShowController@appMapRedis');
});

$api->version('v1', ['middleware' => 'api.throttle', 'namespace' => '\App\Http\Api\V1'], function ($api) {
    $api->group(['prefix' => 'cli'], function ($api) {
        // 前台無需授权api
        $api->group([], function ($api) {
            $api->group(['limit' => 300, 'expires' => 5], function ($api) {
                $api->post('login', AuthController::class . '@login');

                $api->post('register', AuthController::class . '@register');
            });

            $api->group(['limit' => 200, 'expires' => 10], function ($api) {
                // 生成 access_token
                $api->get('token', ApiAuthController::class . '@getAccessToken')->middleware('checkAppKeySecret');
            });

            $api->get('categoriesList', PlatformProductController::class . '@allList');

            // 测试API
            $api->get('test', ShowController::class . '@index');

            $api->get('testEvent', ShowController::class . '@testEvent');

            // 产品列表
            $api->get('productList/{type?}', PlatformProductController::class . '@index');
//            $api->group(['namespace'=>''], function($api){
//            })
            $api->get('testLogEvent', ShowController::class . '@testLogEvent');
        });

        // 前台需授权的 api
        $api->group(['middleware' => ['self.jwt.refresh:user', 'self.jwt.auth']], function ($api) {
            $api->group(['limit' => 10, 'expires' => 1], function ($api) {
                $api->get('showauth', ApiAuthController::class . '@test');
                $api->get('getUserInfo', ApiAuthController::class . '@uInfo');
            });

            // 开通 产品 服务 (单个)
            $api->get('openServiceSelf/{product_id}', ProductServiceController::class . '@addService');
            // 编辑 产品 服务 (多开通， 多 关闭)
            $api->post('editServiceSelf', ProductServiceController::class . '@editService');
            // 关闭 产品 服务 (单个)
            $api->get('delService/{product_id}', ProductServiceController::class . '@delService');

            $api->group(['limit' => 300, 'expires' => 5], function ($api) {
                // 刷新token
                $api->get('refreshToken', ApiAuthController::class . '@refreshAccessToken');
            });
        });

        // 后台的api （都需登录）
        $api->group(['middleware' => ['admin.jwt.changeAuth', 'self.jwt.refresh:admin', 'admin.jwt.auth'], 'namespace' => 'Admin', 'prefix' => 'admin'], function ($api) {
            $api->group(['middleware' => ['admin.jwt.permission:admins|opeartor']], function ($api) {
                $api->get('index', PlatformProductController::class . '@index');
                $api->get('test', PlatformProductController::class . '@test');

                $api->group(['middleware' => ['admin.jwt.permission:admins|opeartor']], function ($api) {
                    // 删除某个 产品服务
                    $api->delete('platformProduct/{product_id}', PlatformProductController::class . '@delete')->where(['product_id' => '[0-9]+']);
                    // 更新某个 产品服务
                    $api->put('platformProduct/{product_id}', PlatformProductController::class . '@edit')->where(['product_id' => '[0-9]+']);
                    // 添加 产品服务
                    $api->post('platformProduct', PlatformProductController::class . '@add');
                    // 生成 一个新的内部 用户
                    $api->post('createNewInternal', AuthController::class . '@createNewInternal');
                });
            });
            // 后台登录
            $api->post('login', LoginController::class . '@login');
            // 内部应用 获取授权
            $api->get('token', AuthController::class . '@getAccessToken');
        });

        // 测试 sign 中间件
        $api->get('testSign', Internal\InternalController::class . '@testSign');

        // 内部的 应用 可以调用的 api (增加一层 数据 加/解密层)
        $api->group(['middleware' => ['admin.jwt.changeAuth', 'self.jwt.refresh:admin', 'admin.jwt.auth', 'admin.jwt.permission:admins|internal', 'check.request.data:admin'], 'namespace' => 'Internal'], function ($api) {
            $api->group(['prefix' => 'internal'], function ($api) {
                // 开通 某一用户的 服务功能
                $api->post('openUserService', PlatformProductController::class . '@openService');
                // 禁止 某一用户 服务功能
                $api->post('disableUserService', PlatformProductController::class . '@disableUserService');
                // 开通 用户
                $api->post('openUser', InternalController::class . '@openUser');
            });
        });
    });
});
