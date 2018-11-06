<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/6
 * Time: 14:54
 */

namespace App\Observers;


use App\Jobs\UpdateApiMap;
use App\Models\PlatformProduct;

class PlatformProductObserver
{
    public function created(PlatformProduct $platformProduct)
    {
        // update api map
        $this->updateApiMapSignle($platformProduct);
    }

    public function updated(PlatformProduct $platformProduct)
    {
        $this->updateApiMapSignle($platformProduct);
    }

    public function deleted(PlatformProduct $platformProduct)
    {
        $this->updateApiMapSignle($platformProduct);
    }

    private function updateApiMapSignle(PlatformProduct $platformProduct)
    {
        UpdateApiMap::dispatch($platformProduct->toArray());
    }
}