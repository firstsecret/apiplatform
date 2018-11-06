<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/27
 * Time: 14:16
 */

namespace App\Http\Api\V1;


use App\Client\httpClient;
use App\Events\AsyncLogEvent;
use App\Events\UserRegisterEvent;
use App\Exceptions\AppUserException;
use App\Exceptions\BevanJwtAuthException;
use App\Http\Api\BaseController;
use App\Jobs\ApiLuaCountJob;
use App\Jobs\CheckAppKeySecretJob;
use App\Jobs\LogJob;
use App\Jobs\ReloadJob;
use App\Jobs\UpdateAppKeyMap;
use App\Models\AppUser;
use App\Models\PlatformProduct;
use App\Models\PlatformProductCategory;
use App\Models\ProductServices;
use App\Services\Admin\AppKeySecretService;
use App\Services\ApiCountService;
use App\Services\FlowService;
use App\Services\NodeService;
use App\Services\RedisScanService;
use App\Tool\ProbeTool;
use App\User;
use Encore\Admin\LogViewer\NginxLogViewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psr\Http\Message\ResponseInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowController extends BaseController
{
    use ProbeTool;

    public function __construct()
    {
//        echo 'is comming';
    }

    /**
     *  测试
     */
    public function index()
    {
//        echo 'ok api';
//        $appkey = urlencode('439d8c975f26e5005dcdbf41b0d84161');
//        $appsecret = urlencode('08aee6276db142f4b8ac98fb8ee0ed1b');
////        $appsecret = urlencode('');
//
//        // curl
//
//        $url = 'http://laravelapi.local/api/cli/token?app_key=' . $appkey . '&$app_secret=' . $appsecret;

//        $ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,$url);
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//        curl_setopt($ch,CURLOPT_HEADER,0);
//        $output = curl_exec($ch);
//
//        curl_close($ch);

//        if($output == false){
//            return Response()->json(['status_code'=>500,'msg'=>'请求失败']);
//        }else{
//            var_dump($output);die;
//            return Response()->json(['status_code'=>200,'msg'=>'success','data'=>['access_token'=>'']]);
//        }

//        $dispatcher = app('Dingo\Api\Dispatcher');

//        $dispatcher->get();
//        $res = $this->api->get('cli/token?app_key=' . $appkey . '&app_secret=' . $appsecret);

//        var_dump($res);die;


//        Cache::add('test_service', [1 => [1, 3, 4], 2 => [3, 46]], 3);
//
//
//        $va = Cache::get('test_service');
//
//        dd($va);

//        var_dump(PlatformProductCategory::with('products')->find(3));die;
//        var_dump(PlatformProduct::with('services')->find(2)->toArray());die;

        dd(PlatformProduct::with('services')->find(2)->toArray());

    }

    public function testSign()
    {
        $app_key = '4a5a48028c6b973820e2d719be41e384';
        $app_secret = 'c2069e2ffd507a83f85e92d1543e14fd';
        $sequenceId = 123456;
        $reqData = [
            'name' => 'bevan',
            'age' => 18
        ];

        $sign = md5(json_encode($reqData, JSON_UNESCAPED_UNICODE) . $sequenceId . $app_secret);

        return $this->responseClient(200, 'success', ['app_key' => $app_key, 'sequenceId' => $sequenceId, 'reqData' => $reqData, 'sign' => $sign]);
    }

    public function testEvent()
    {
        // 事件取消
        Event::fire(new UserRegisterEvent());
//        $this->dispatch(new MailJob());

        return Response()->json(['status_code' => 200, 'msg' => '任务投递成功', 'data' => '']);
    }

    public function testLogEvent()
    {
        Event::fire(new AsyncLogEvent('日志信息', 'info'));

        return $this->responseClient(200, '任务投递成功', '');
    }

    /**
     *  测试 lua
     */
    public function testLua()
    {
//        $lua = new Lua();
//        $lua->eval(<<<CODE
//    function dummy(foo, bar)
//        print(foo, ",", bar, ngx.var.request_method)
//    end
//CODE
//        );
//        $lua->call("dummy", array("Lua", "geiliable"));
//        $lua->dummy("Lua", "geiliable"); // __call()
//        var_dump($lua->call(array("table", "concat"), array(array(1 => 1, 2 => 2, 3 => 3), "-")));

        $client = new httpClient();
        $promise = [
            '0' => [
                'method' => 'post',
                'uri' => 'http://bevan.top/api/testLua2',
                'options' => []
            ],
            '1' => [
                'method' => 'post',
                'uri' => 'http://bevan.top/api/testLua2',
                'options' => [
                    'header' => ['Content-type' => 'html/json'],
                    'json' => ['name' => 'bevan', 'age' => 18]
                ]
            ],
            '2' => [
                'method' => 'get',
                'uri' => 'http://bevan.top/api/testLua4',
                'options' => []
            ],
            '3' => [
                'method' => 'get',
                'uri' => 'http://bevan.top/api/testLua3',
                'options' => []
            ],
        ];

        $client->request->asyncPoolRequest($promise);

        $responseArr = $client->request->getResponse();

        return $this->responseClient(200, '返回数据', $responseArr);
    }

    public function testLua2(Request $request)
    {
        $reData['name'] = $request->input('name');
        $reData['age'] = $request->input('age');
        return $this->responseClient(200, 'lua2', $reData);
    }

    public function testLua3()
    {
//        sleep(1);
        throw new \Exception('出现错误了');
        return $this->responseClient(200, 'lua3');
    }

    public function testLua4(Request $request)
    {
//        sleep(1);
//        $user = User::find(2)->toArray();
        $user = [];
//        User::where('id', 2)->update(['name' => 'bevan']);
//        $res = User::withTrashed()->get();
        $realIp = $request->header('X-Forwarded-For');

        return $this->responseClient(200, 'lua4', ['test' => 'fdsf', 'remote_addr' => $request->ip(), 'real_ip' => $realIp]);
    }

    public function testLua5(Request $request)
    {
        $req_data['headers'] = $request->header();
        $req_data['input'] = $request->input();

        return $this->responseClient(200, 'success', $req_data);
    }

    public function testAsync()
    {
        $client = new httpClient();

        $client->request->requestAsync('post', 'http://bevan.top/api/testLua2', [], function ($res) {
            // end
//            var_dump(get_class_methods($res));
//
//            var_dump('isok');
//            Log::info(get_class_methods($res));
            return $this->responseClient(200, '回调返回成功', ['name' => 'bevan']);
        }, function ($e) {
            $errormsg = $e->getMessage();
            $status_code = $e->getCode();
            Log::info(get_class_methods($errormsg));
//            return $this->responseClient($status_code, $errormsg, []);
        });

//        var_dump('dfddf');
        die;
    }

    public function testNewLua(Request $request)
    {
//        dd($request);
        $input = $request->all();
        $headers = $request->header();

        return $this->responseClient(200, '成功', [
            'headers' => $headers,
            'input' => $input
        ]);

//        $lua = new \Lua();
//        $lua->eval(<<<CODE
//    function dummy(foo, bar)
//        print(foo, ",", bar)
//    end
//CODE
//        );

//        $lua->eval(<<<CODE
//    local address = ngx.var.remote_addr
//    print(address)
//CODE
//        );

//        $lua->eval(<<<CODE
//local http = require "http"
//local httpc = http.new()
//local url = "http://bevan.top/api/testLua4"
//local resStr --响应结果
//local res, err = httpc:request_uri(url, {
//    method = "POST",
//    --args = str,
//    body = str,
//    headers = {
//        ["Content-Type"] = "application/json",
//    }
//})
//
//if not res then
//    ngx.log(ngx.WARN,"failed to request: ", err)
//    return resStr
//end
//--请求之后，状态码
//ngx.status = res.status
//if ngx.status ~= 200 then
//    ngx.log(ngx.WARN,"非200状态，ngx.status:"..ngx.status)
//    return resStr
//end
//--header中的信息遍历，只是为了方便看头部信息打的日志，用不到的话，可以不写的
//for key, val in pairs(res.headers) do
//    if type(val) == "table" then
//        ngx.log(ngx.WARN,"table:"..key, ": ", table.concat(val, ", "))
//    else
//        ngx.log(ngx.WARN,"one:"..key, ": ", val)
//    end
//end
//--响应的内容
//resStr = res.body
//CODE
//    );

//        $lua->call("dummy", array("Lua", "geiliable\n"));
//        $lua->dummy("Lua", "geiliable"); // __call()
//        var_dump($lua->call(array("table", "concat"), array(array(1=>1, 2=>2, 3=>3), "-")));

//        $lua->eval("lua_statements");     //eval lua codes
//        $lua->include("lua_script_file"); //import a lua script

//        $lua->assign("name", 'bevan'); //assign a php variable to Lua
//        $lua->register("name", 'testlua'); //register a PHP function to Lua with "name"
//
//        $lua->call('testlua', array() /*args*/);
//        $lua->call($resouce_lua_anonymous_function, array() /*args);
//   $lua->call(array("table", "method"), array(...., "push_self" => [true | false]) /*args*/);
//
//        $lua->{$lua_function}(array()/*args*/);
    }

    public function testUpload(Request $request)
    {
        $file = $request->file('file');

        if (!$file) throw new \Exception('未获取上传文件', 401);

        $path = $file->store('public/imgs');
        $path = strstr($path, '/imgs');
        $get_path = 'storage' . $path;

        $url = asset($get_path);
        return $this->responseClient(200, 'success', ['path' => $path, 'url' => $url]);
    }

    public function testNewException()
    {
//        Redis::SREM('ip_blacklist', '172.30.202.241');
        Redis::SADD('ip_blacklist', '49.72.219.19');
        $list = Redis::smembers('ip_blacklist');
        dd($list);
//        throw  new BevanJwtAuthException(4033, '新的异常处理', 500, null);
    }

    public function testJWT()
    {
        $user = User::findOrFail(1);

        $token = JWTAuth::claims(['model' => 'user'])->fromUser($user);

        return $this->tokenResponse($token);
    }

    public function getNetJWT(Request $request)
    {
        return $this->responseClient(200, 'get', [
            'headers' => $request->header(),
            'input' => $request->input()
        ]);
    }

    public function appMapRedis()
    {
//        $users = AppUser::where('model', 'App\User')->get(['id', 'app_key', 'app_secret','type'])->toArray();

        // 建议 分片 处理
//        foreach ($users as $u) {
//            Redis::set($u['app_key'], $u['app_secret'] . $u['type']);
//        }

        // 分块
//        AppUser::chunk(2, function ($users) {
//            foreach ($users as $u) {
////                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
//                Redis::set($u['app_key'], $u['app_secret'] . $u['type']);
//            }
//        });

//        $valid_time = Redis::get('app_key_last_valid_time');
//
//        $diff = time() - (int)$valid_time;
//
//        if (empty(Redis::keys('app_key:*')) || $diff >= 24 * 3600) {
//            // update
//            (new AppKeySecretService())->mapAppkeysecret();
//        }

        CheckAppKeySecretJob::dispatch();

        // cursor handle
//        foreach (AppUser::where('model', 'App\User')->cursor() as $user) {
//            Redis::set($user['app_key'], $user['app_secret'] . $user['type']);
//        }

        return $this->responseClient(200, '成功', []);
    }

    public function testAdminConfig()
    {
//        AppUser::find(1);
//        dd(config('test_admin_key'));
//        var_dump(config('test_admin_secret'));
//        dd(User::find(7)->appuser());

//        tfdfad
    }

    public function testApiCount()
    {
//        dd(Redis::hgetall('ip_api_count_220.250.63.189'));
//        $a = new ApiCountService();
//
//        foreach ($a as $d){
//            dd($d);
//        }

        $f = new FlowService();

        $f->updateTotalCount();

        dd('ok');
//        $api_count = new RedisScanService(['count' => 5]);
//        $now = date('Y-m-d', time());
//
//        try {
//            foreach ($api_count as $k => $v) {
//                $api_number = Redis::MGET($v);
//                $new_api_count = [];
//                $insert_sql = '';
//                foreach ($v as $vk => $uri) {
//                    $request_uri = substr($uri, 0, 254);
//                    $new_api_count[] = [
//                        'request_uri' => $request_uri,
//                        'request_number' => $api_number[$vk],
//                        'created_at' => $now,
//                        'updated_at' => $now
//                    ];
//                    $insert_sql .= " ('$request_uri', $api_number[$vk], '$now', '$now'),";
//                }
//                // ru ku
//                $insert_sql = rtrim($insert_sql, ',');
//                $sql = "REPLACE INTO flows (request_uri,request_number,created_at,updated_at) VALUES $insert_sql";
//
//                DB::statement($sql);
//            }
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//        }
//
//        return $this->responseClient();

//        $d= Redis::scan(1,['match' =>'api_count_*']);
//        dd($d);

//        $count = Redis::get('api_request_condition');
//
//        dd($count);
//        $d = Redis::keys('ip_api_count_*');

//        dd(strlen(json_encode($d)));
//        foreach ($d as $prefix_ip){
//            $v = Redis::HGETALL($prefix_ip);
//
//            dd($v);
//        }

//        $apiCountService = new ApiCountService();
//
//        foreach ($apiCountService as $k => $v){
////            var_dump($apiCountService->getIp());
//            dd($v);
//        }

//        $apiCountIterator = new ApiCountService();
//        $now = date('Y-m-d H:i:s', time());
//        DB::beginTransaction();
//        try {
//            foreach ($apiCountIterator as $k => $v) {
//                $ip = $apiCountIterator->getIp();
//                $insert_data = [];
//
//                foreach ($v as $request_uri => $number) {
//                    $insert_data[] = [
//                        'ip' => $ip,
//                        'request_uri' => substr($request_uri,0,254),
//                        'today_total_number' => $number,
//                        'created_at' => $now,
//                        'updated_at' => $now
//                    ];
//                }
//                DB::table('ip_request_flows')->insert($insert_data);
//            }
//        } catch (\Exception $e) {
//            dd($apiCountIterator);
////            dd($e->getMessage());
////            DB::rollBack();
////            throw $e;
//        }
//        DB::commit();
//        ApiLuaCountJob::dispatch();
//
//        $this->responseClient(200,'ok',[]);
    }

    public function testCommand(Request $request)
    {

//        throw new AppUserException(5031,'dfddf',401);
//        UpdateAppKeyMap::dispatch();

//        (new NodeService())->updateNodeByDb();
//        App::singleton('testApp',function(){
//            return new ShowController();
//        });
//
//        $a = App::make('testApp');
//        $b = App::make('testApp');
//        $c = App::make('testApp');
//
//        dd($a,$b,$c);
//        $ips = Redis::keys('ip_api_count_*');

//        return ['as'=>'dfd'];
        dd(Redis::keys('services_map*'));
//        dd(Redis::exists(User::APP_KEY_FLAG . 'd7fbc1f0f38c3ee95fb7cdc17f7f9401'));

//        $exitCode = Artisan::call('webserver', ['cmd' => 'restart']);
//        dd($this->getCommand("machdep.cpu.core_count"));
//        ReloadJob::dispatch();
//        $file = (new \App\Admin\Extensions\Tools\NginxLogViewer())->getLastModifiedLog();
//        $viewer = new \App\Admin\Extensions\Tools\NginxLogViewer('bevan.top_nginx.log-20181026.gz');
//        $viewer = new NginxLogViewer('bevan.top_nginx.log-20181026.gz');
//        $viewer = new \App\Admin\Extensions\Tools\NginxLogViewer('bevan.top_nginx.log');

//        $c = $viewer->fetch();
//        $c2 = $viewer2->fetch();
//        var_dump('c2:'. $c2);
//
//        dd($c);
//        var_dump('c:'. $c);
//        exit;
//        dd($exitCode);
//        $ddd = Redis::scan(0,['match'=>'*' . User::APP_KEY_FLAG . '*','count'=>100]);
//        foreach ($ddd as $a){
//            var_dump($a);
//        }
//        exit;
//        var_dump(Redis::keys(User::APP_KEY_FLAG . '*'));
//        dd(Redis::EXISTS('api_app_key:4a5a48028c6b973820e2d719be41e384'));
        $data = new RedisScanService(['match' => 'api_app_key*']);

        foreach ($data as $k => $d) {
            foreach ($d as $app_key_redis) {
                $app_key = explode(':', $app_key_redis)[1];

                //db check del condition
                $appuser = AppUser::with(['user' => function ($query) {
                    $query->where('type', User::IS_ACTIVE_STATUS);
                }])->where(['model' => User::LOGIC_MODEL, 'app_key' => $app_key])->get(['id']);

                if (!$appuser) {
                    // del
                    Redis::del($app_key_redis);
                }
            }
        }

        exit;

//        $d = Redis::keys(User::APP_KEY_FLAG . '*');
//        $arr = [];
//        foreach ($d as $item){
//            $arr[] = Redis::hgetall($item);
//        }
//        dd($arr);

        AppUser::with(['user' => function ($query) {
            $query->where('type', User::IS_ACTIVE_STATUS);
        }])->where('model', User::LOGIC_MODEL)->chunk(200, function ($users) {
            //filter
            $users = $users->filter(function ($u, $key) {
                return $u['user'];
            })->toArray();

            // handle
            foreach ($users as $user) {
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'app_secret', $user['app_secret']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'user_type', $user['user']['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'app_key_type', $user['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'user_id', $user['user_id']);
            }
//            dd($users->toArray());
//            foreach ($users as $u) {
//                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
//                Redis::set($u['app_key'], $u['app_secret'] . $u['type']);
//            }
        });
//        $app_secret = Redis::get('apiplatform_service_base_uri');
//
//        dd($request->server);
    }
}