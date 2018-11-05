<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class HealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'healthcheck: {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'server nodes health check';

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
        $cmd = $this->argument('cmd');

        switch ($cmd) {
            case 'start':
                $this->startServer();
                break;

        }
    }

    protected function startServer()
    {
        // 清理 之前的定时
        $timer_fd = Redis::get('health_check_timer');
        if ($timer_fd) swoole_timer_clear($timer_fd);

        $http_client = $this->initClient();
        // 启动定时
        $n_timer_fd = swoole_timer_tick(5000, function () use ($http_client) {
            // http client
            $http_client->send('/health_status');
            $data = $http_client->recv();
            echo $data;
        });
    }

    public function initClient()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        if (!$client->connect('127.0.0.1', 80, 5)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        return $client;
    }
}
