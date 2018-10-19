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

    protected $ws;

    protected $is_constantly; // 是否 实时信息

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
        $this->ws = new \swoole_websocket_server("0.0.0.0", 8555);

        //监听WebSocket连接打开事件

        $this->ws->set(array(
            'worker_num' => 1,    //worker process num
//            'backlog' => 128,   //listen backlog
//            'max_request' => 50,
//            'daemonize' => 1,
        ));

        //register event
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('close', [$this, 'onClose']);

        $this->ws->start();

        $this->info('prop server is started！');
    }

    public function onMessage($ws, $frame)
    {
//        echo "Message: {$frame->data}\n";

        // get cpu status
        $data = json_decode($frame->data, true);

        if ($data['act'] == 'rt') {
            $this->getServerInfo();

            $jsonRes = $this->initServStatus();

            $ws->push($frame->fd, $jsonRes);
        }
//        $ws->push($frame->fd, "ffffserver: {$frame->data}");
    }

    public function getServerInfo()
    {
        $sStatus = $this->switchOsInfo($this->is_constantly);

        $this->svrShow = $sStatus['svrShow'];
        $this->svrInof = $sStatus['svrInof'];
    }

    public function onClose($ws, $fd)
    {
//        echo "client-{$fd} is closed\n";
    }

    public function onOpen($ws, $request)
    {
        $this->getServerInfo();

        $jsonRes = $this->initServStatus();

        $ws->push($request->fd, $jsonRes);
    }

    public function initServStatus()
    {
        if ($this->is_constantly) {
            $currentTime = date("Y-m-d H:i:s");
            $uptime = $this->svrInfo['uptime'];
        }

        $res = array(
            'currentTime' => $currentTime,
            'uptime' => $uptime,
            'cpuPercent' => $this->svrInfo['cpu']['percent'],
            'MemoryUsed' => $this->svrInfo['mUsed'],
            'MemoryFree' => $this->svrInfo['mFree'],
            'MemoryPercent' => $this->svrInfo['mPercent'],
            'MemoryCachedPercent' => $this->svrInfo['mCachedPercent'],
            'MemoryCached' => $this->svrInfo['mCached'],
            'MemoryRealUsed' => $this->svrInfo['mRealUsed'],
            'MemoryRealFree' => $this->svrInfo['mRealFree'],
            'MemoryRealPercent' => $this->svrInfo['mRealPercent'],
            'Buffers' => $this->svrInfo['mBuffers'],
            'SwapFree' => $this->svrInfo['swapFree'],
            'SwapUsed' => $this->svrInfo['swapUsed'],
            'SwapPercent' => $this->svrInfo['swapPercent']
        );
        $jsonRes = json_encode($res, JSON_UNESCAPED_UNICODE);

        return $jsonRes;
    }
}
