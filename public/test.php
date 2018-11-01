<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/6
 * Time: 14:08
 */
//set_time_limit(0);
//
//echo date('Y-m-d H:i:s', time());die;


//array_combine
//
//$generator = call_user_func(function() {
//
//    $input = (yield "foo");
//
//
//
//    print "inside: " . $input . "\n";
//
//});
//
//
//
//print $generator->current() . "\n";
//
//
//
//$generator->send("bar");


//$multi = function multiOp($x, $y){
//   try{
//       yield $x * $y;
//       throw new Exception('dfsfds');
//   }catch (\Exception $e){
//       print 'ERROR!';
//   }
//}
//
//function opfunc($multiDriv, $x ,$y) use ($multi)
//{
//    if($multiDriv == 'multiOp'){
//        $generator = $multi($x, $y);
//
//        return $generator->current();
//    }
//
//    if(!is_numeric($x) || !is_numeric($y)){
//        throw  new \Exception('不是number');
//    }
//
//
//}

//$res = opfunc('multiOp',5,6);
//echo $res;


class Task
{

    protected $generator;
    protected $run = false;

    public function __construct(Generator $generator)
    {

        $this->generator = $generator;

    }


    public function run()
    {
        if ($this->run) {
            $this->generator->next();

        } else {
            $this->generator->current();
            $this->run = true;
        }
    }


    public function finished()
    {
        return !$this->generator->valid();
    }
}

class Scheduler
{
    protected $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }


    public function enqueue(Task $task)
    {
        $this->queue->enqueue($task);

    }

    public function run()
    {
        while (!$this->queue->isEmpty()) {

            $task = $this->queue->dequeue();

            $task->run();

            if (!$task->finished()) {
                $this->queue->enqueue($task);
            }
        }
    }
}


$scheduler = new Scheduler();

$time = time();
//$task = new Task(call_user_func(function () {
//    $sl = rand(2,3);
//    sleep(rand(2, 3));
//    var_dump($sl);
//    yield;
//}));
//
//$task2 = new Task(call_user_func(function () {
//    $sl = rand(2,3);
//    sleep(rand(2, 3));
//    var_dump($sl);
//    yield;
//}));
//
//for ($i = 0; $i < 100; $i++) {
//    $scheduler->enqueue($task);
//    $scheduler->enqueue($task2);
//}

//$scheduler->run();

function testSend()
{

    yield;
    $sleep = rand(2, 3);
    sleep($sleep);
    print('is finish');
}

for ($i = 0; $i < 2; $i++) {
    testSend();
}

echo '<br>耗时:' . (time() - $time) . '秒';


exit;
/**
 *  app 应用方
 */
//$uniqe_arr = [];
//$time = 0;
//for ($i = 1; $i <= 6200; $i++) {
////    $uniqe = substr(md5($i), 0, 8);
//    $uniqe = substr(md5(uniqid($i, true)), 0, 6);
////    $uniqe = md5($i);
//    echo $uniqe . '<br>';
//    if (in_array($uniqe, $uniqe_arr)) {
//        $time++;
//        echo '重复的值:' . $uniqe . '<br>';
//        continue;
//    }
//
//    $uniqe_arr[] = $uniqe;
//}
//
//echo '总重复值:' . $time . '<br>';
//
//var_dump(extension_loaded('uuid'));
//
//var_dump(uuid_create());
//$str = uniqid(1, true);
//var_dump(strlen($str));die;
$uuid_arr = [];
for ($i; $i < 500000; $i++) {
    $str = uniqid($i, true);
    $uuid = substr($str, 0, 8);
//    $uuid .= substr($str, 8, 4) . '-';
//    $uuid .= substr($str, 12, 4) . '-';
//    $uuid .= substr($str, 16, 4) . '-';
//    $uuid .= substr($str, 20, 12);
    $uniqe_arr[] = $uuid;
}

echo count(array_unique($uniqe_arr)) . '<br>';
die;

//function create_uuid($prefix = ""){    //可以指定前缀
//    $str = md5(uniqid(mt_rand(), true));
//    $uuid  = substr($str,0,8) . '';
//    $uuid .= substr($str,8,4) . '';
//    $uuid .= substr($str,12,4) . '';
//    $uuid .= substr($str,16,4) . '';
//    $uuid .= substr($str,20,12);
//    return $prefix . $uuid;
//}
//
//$str = create_uuid('dfdfd');
//
//echo strlen($str);
//$un_arr  = [];
//for ($i=1; $i<= 1000000;$i++){
//    $u = create_uuid();
//    $un_arr[] = $u;
//}
//
//echo count(array_unique($un_arr)) . '<br>';
/**
 *  用户
 */
//$uniqe_arr = [];
//$time = 0;
//for ($i = 1; $i <= 200000; $i++) {
////    $uniqe = substr(md5($i), 0, 16);
//    $uniqe = uniqid($i, true);
////    $uniqe = md5($i);
//
//    $uniqe_arr[] = $uniqe;
//}
//
//echo '总数:' . count(array_unique($uniqe_arr));

//var_dump(array_count_values($uniqe_arr));
//echo '总重复值:' . $time . '<br>';
