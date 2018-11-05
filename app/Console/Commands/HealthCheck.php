<?php

namespace App\Console\Commands;

use App\Services\Admin\DashboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class HealthCheck extends Command
{

    const NODE_UPSTATUS = 'up';
    const NODE_DOWNSTATUS = 'down';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'healthcheck {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'server nodes health check, cmd supported by start, stop';

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
            case 'stop':
                $this->stopServer();
                break;
        }
    }

    protected function stopServer()
    {
        \swoole_timer_clear(Redis::get(config('redis_key.health_check_timer')));

        $this->info('is stop the healthcheck ！');
    }

    protected function startServer()
    {
        // 清理 之前的定时
        $timer_fd = Redis::get(config('redis_key.health_check_timer'));
        // make sure keep one timer tick
        if ($timer_fd) @\swoole_timer_clear($timer_fd);

        $http_client = $this->initClient();

        $handleService = new DashboardService();

        // 启动定时
        $n_timer_fd = \swoole_timer_tick(5000, function () use ($http_client, $handleService) {
            $http_client->get('/health_status', function ($cli) use ($handleService) {
//            echo "Length: " . strlen($cli->body) . "\n";
                $format_data = $handleService->formatHealthStatusObject($cli->body);

                $health_status = $format_data['upstream']['api.com'];
                // test
                // check api.com
                foreach ($health_status as $node => $status) {
                    if (self::NODE_UPSTATUS == strtolower($status)) {
                        Redis::SADD(config('redis_key.node_load_balancing'), $node);
                    } else {
                        Redis::SREM(config('redis_key.node_load_balancing'), $node);
                    }
                }
            });
        });

        Redis::set(config('redis_key.health_check_timer'), $n_timer_fd);
    }

    public function initClient()
    {
        $client = new \swoole_http_client('127.0.0.1', 80);
        $client->setHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/json',
        ]);
        return $client;
    }
}
