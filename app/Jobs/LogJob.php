<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class LogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;


    /**
     * 执行任务的最长时间
     */

    public  $timeout=5;

    protected  $message;

    protected $level;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $level)
    {
        //
        $this->message = $message;
        $this->level = $level;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
//        var_dump($this->level);
//        var_dump($this->message);
        $method = $this->level;
        Log::$method($this->message);
    }
}
