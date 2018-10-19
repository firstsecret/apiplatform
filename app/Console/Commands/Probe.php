<?php

namespace App\Console\Commands;

use App\Tool\ProbeTool;
use Illuminate\Console\Command;

class Probe extends Command
{
    use ProbeTool;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'probe {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '启动服务器监控';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $status = $this->argument('status');
//        var_dump($status);
        if ($status == 'start') {
            $this->startServer();
        } else if ($status == 'restart') {
            $this->info('is not support now！');
//            $this->ws->shutdown();
//            $this->startServer();
        } else {
            $this->info('no command match！');
        }
    }

    protected function startServer()
    {
        $ws = new \swoole_websocket_server("0.0.0.0", 8555);

        //监听WebSocket连接打开事件

        $ws->set(array(
            'worker_num' => 1,    //worker process num
//            'backlog' => 128,   //listen backlog
//            'max_request' => 50,
//            'daemonize' => 1,
        ));

        //register event
        $ws->on('open', [$this, 'onOpen']);
        $ws->on('message', [$this, 'onMessage']);
        $ws->on('close', [$this, 'onClose']);

        $ws->start();

        $this->info('prop server is started！');
    }

    public function onMessage($ws, $frame)
    {
//        echo "Message: {$frame->data}\n";

        // get cpu status
        $data = json_decode($frame->data, true);

        if ($data['act'] == 'rt') {
            $sInfo = $this->getServerInfo();

            $jsonRes = $this->initServStatus($sInfo);

            $ws->push($frame->fd, $jsonRes);
        }
//        $ws->push($frame->fd, "ffffserver: {$frame->data}");
    }

    public function getServerInfo()
    {
        return $this->switchOsInfo();
    }

    public function onClose($ws, $fd)
    {
//        echo "client-{$fd} is closed\n";
        // force clear
        $ws->close($fd, true);
    }

    public function onOpen($ws, $request)
    {
//        $sInfo = $this->getServerInfo();
//        $jsonRes = $this->initServStatus($sInfo);
//
//        $ws->push($request->fd, $jsonRes);
    }

    public function initServStatus($sInfo)
    {
        $svrInof = $sInfo['svrInof'];
//        $svrShow = $sInfo['svrShow'];

        $currentTime = date("Y-m-d H:i:s");

        $uptime = $svrInof['uptime'];

        $res = array(
            'currentTime' => $currentTime,
            'uptime' => $uptime,
            'cpuPercent' => $svrInof['cpu']['percent'],
            'MemoryUsed' => $svrInof['mUsed'],
            'MemoryFree' => $svrInof['mFree'],
            'MemoryPercent' => $svrInof['mPercent'],
            'MemoryCachedPercent' => $svrInof['mCachedPercent'],
            'MemoryCached' => $svrInof['mCached'],
            'MemoryRealUsed' => $svrInof['mRealUsed'],
            'MemoryRealFree' => $svrInof['mRealFree'],
            'MemoryRealPercent' => $svrInof['mRealPercent'],
            'Buffers' => $svrInof['mBuffers'],
            'SwapFree' => $svrInof['swapFree'],
            'SwapUsed' => $svrInof['swapUsed'],
            'SwapPercent' => $svrInof['swapPercent']
        );
        $jsonRes = json_encode($res, JSON_UNESCAPED_UNICODE);

        return $jsonRes;
    }
}
