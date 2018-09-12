<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/27
 * Time: 14:16
 */

namespace App\Http\Api\V1;


use App\Events\AsyncLogEvent;
use App\Events\UserRegisterEvent;
use App\Http\Api\BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class ShowController extends BaseController
{
    /**
     *  测试
     */
    public function index(){
//        echo 'ok api';
        $appkey = urlencode('439d8c975f26e5005dcdbf41b0d84161');
        $appsecret = urlencode('08aee6276db142f4b8ac98fb8ee0ed1b');
//        $appsecret = urlencode('');

        // curl

        $url = 'http://laravelapi.local/api/cli/token?app_key='.$appkey . '&$app_secret=' . $appsecret;

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


        Cache::add('test_service', [1=> [1,3,4],2=>[3,46]],3);


        $va = Cache::get('test_service');

        dd($va);

    }

    public function testEvent()
    {
        // 事件取消
        Event::fire(new UserRegisterEvent());
//        $this->dispatch(new MailJob());

        return Response()->json(['status_code'=>200,'msg'=>'任务投递成功','data'=>'']);
    }

    public function testLogEvent()
    {
        Event::fire(new AsyncLogEvent('日志信息','info'));

        return $this->responseClient(200,'任务投递成功','');
    }

    /**
     *  测试 lua
     */
    public function testLua()
    {
        $lua = new Lua();
        $lua->eval(<<<CODE
    function dummy(foo, bar)
        print(foo, ",", bar, ngx.var.request_method)
    end
CODE
        );
        $lua->call("dummy", array("Lua", "geiliable"));
        $lua->dummy("Lua", "geiliable"); // __call()
        var_dump($lua->call(array("table", "concat"), array(array(1=>1, 2=>2, 3=>3), "-")));
    }
}