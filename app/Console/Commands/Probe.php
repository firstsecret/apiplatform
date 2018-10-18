<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Probe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'probe:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '启动服务器监控';

    protected $ws;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ws = new \swoole_websocket_server("0.0.0.0", 8555);

        //监听WebSocket连接打开事件

        //register event
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('close', [$this, 'onClose']);

        $this->ws->start();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }

    public function onMessage($ws, $frame)
    {
        echo "Message: {$frame->data}\n";
        $ws->push($frame->fd, "ffffserver: {$frame->data}");
    }

    public function onClose($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
    }

    public function onOpen($ws, $request)
    {
        $ws->push($request->fd, "hello welcome\n");
    }
}
