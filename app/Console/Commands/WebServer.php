<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class WebServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WebServer {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'web server(nginx) handle,cmd supported: relaod, stop, quit, reopen ,start';

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
        $cmd = $this->argument('cmd') ?? 'reload';

        if (!in_array($cmd, ['reload', 'stop', 'quit', 'reopen', 'start'])) {
            $this->error('not support this cmd, only relaod, stop, quit, reopen ,start !');
            exit;
        }

        $process = new \Swoole\Process(function (\Swoole\Process $childProcess) use ($cmd) {
            $storagePath = App::storagePath();
            $shell_cmd = ['-c', $storagePath . '/app/server/nginx/nginx.conf'];
            if ($cmd != 'start') {
                $shell_cmd = array_merge($shell_cmd, ['-s', $cmd]);
            }
            $childProcess->exec('/usr/local/openresty/nginx/sbin/nginx', $shell_cmd);
        });

        $pid = $process->start();

        \swoole_process::signal(SIGCHLD, function ($sig) {
            //必须为false，非阻塞模式
            while ($ret = \swoole_process::wait(false)) {
//                echo "PID={$ret['pid']}\n";
//                var_dump($ret);
//                $res['pid'] =>
//                var_dump($pid);
//                \swoole_process::kill($pid, 0);
                $this->info('is restart ok ！');
                exit;
            }
        });
//        echo "from exec: ". $process->read(). "\n";
    }
}
