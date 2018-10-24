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
        } else {
            $this->info('no command match！');
        }
    }

    protected function startServer()
    {
        $ws = new \swoole_websocket_server("0.0.0.0", 8555);

        //监听WebSocket连接打开事件
        $ws->set(array(
            'worker_num' => 2,    //worker process num
            'buffer_output_size' => 4 * 1024 * 1024,  // 4M
//            'backlog' => 128,   //listen backlog
//            'max_request' => 50,
            'daemonize' => 1,
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
        // get cpu status
//        $data = json_decode($frame->data, true);

        $sInfoBase = $this->getServerInfo();
        $sInfo = $this->initServStatus($sInfoBase);
        $svrInfo = $sInfoBase['svrInfo'];
        $svrInfo = array_merge($svrInfo, $sInfo);
//        $svrInfo['currentTime'] = $sInfo['currentTime'];
        // hdd status
        // hdd
        $svrInfo = $this->hddstatus($svrInfo);

        $jsonRes = json_encode(['act' => 'default', 'data' => ['svrInfo' => $svrInfo]], JSON_UNESCAPED_UNICODE);

        if ($ws->exist($frame->fd)) $ws->push($frame->fd, $jsonRes);
        // force clear
        else $ws->close($frame->fd, true);
    }

    public function getServerInfo()
    {
        return $this->switchOsInfo();
    }

    public function onClose($ws, $fd)
    {
//        echo "client-{$fd} is closed\n";

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
        $svrInfo = $sInfo['svrInfo'];
//        $svrShow = $sInfo['svrShow'];

        $currentTime = date("Y-m-d H:i:s");

        $uptime = $svrInfo['uptime'];

        $res = array(
            'currentTime' => $currentTime,
            'uptime' => $uptime,
            'cpuPercent' => $svrInfo['cpu']['percent'],
            'MemoryUsed' => $svrInfo['mUsed'],
            'MemoryFree' => $svrInfo['mFree'],
            'MemoryPercent' => $svrInfo['mPercent'],
            'MemoryCachedPercent' => $svrInfo['mCachedPercent'],
            'MemoryCached' => $svrInfo['mCached'],
            'MemoryRealUsed' => $svrInfo['mRealUsed'],
            'MemoryRealFree' => $svrInfo['mRealFree'],
            'MemoryRealPercent' => $svrInfo['mRealPercent'],
            'Buffers' => $svrInfo['mBuffers'],
            'SwapFree' => $svrInfo['swapFree'],
            'SwapUsed' => $svrInfo['swapUsed'],
            'SwapPercent' => $svrInfo['swapPercent']
        );

        return $res;
    }
}
