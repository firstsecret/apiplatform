<?php

namespace App\Http\Api\V1\Admin;

use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;


class PlatformProductController extends AdminBaseController
{
    //
    public function add()
    {
        // add product
    }

    public function edit()
    {
        // edit product
    }

    public function delete()
    {

    }

    public function plist()
    {

    }

    public function index()
    {
        return $this->responseClient(200,'api需认证请求',[]);
    }
}
