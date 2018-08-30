<?php

namespace App\Http\Api\V1;

use App\Http\Api\BaseController;
use App\Facades\PlatformProduct as PlatformProFacade;

class PlatformProduct extends BaseController
{
    //
    public function index($type = 'default')
    {
        $list = PlatformProFacade::productList($type);

        return $this->responseClient(200,'success',$list);
    }

    public function allList()
    {
        $list = PlatformProFacade::getCategoriesWithProduct();

        //sort
        $this->treeSort($list);

        return $this->responseClient(200,'success',$list);
    }
}
