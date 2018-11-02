<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/2
 * Time: 15:17
 */

namespace App\Observers;


use App\Models\Service;
use App\Services\NodeService;

class ServiceObserver
{
    public function updated(Service $service)
    {
        $this->updateHealthCheck();
    }

    public function created(Service $service)
    {
        $this->updateHealthCheck();
    }

    public function deleted(Service $service)
    {
        $this->updateHealthCheck();
    }

    private function updateHealthCheck()
    {
        (new NodeService())->updateNodeByDb();
    }
}