<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;

class PlatformProduct extends BaseController
{
    //
    public function index($type = 'default')
    {
        $list = \App\Facades\PlatformProduct::productList($type);

        return $this->responseClient(200,'success',$list);
    }
}
