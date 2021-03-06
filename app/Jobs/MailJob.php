<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 3;


    /**
     * 执行任务的最长时间
     */

    public  $timeout=30;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        //
        $this->data= $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
//        sleep(1);
        var_dump('邮件发送' . $this->data);
    }
}
