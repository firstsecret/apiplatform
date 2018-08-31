<?php

namespace App\Http\Api\V1\Admin;

use App\Http\Api\AdminBaseController;
use Illuminate\Http\Request;

class LoginController extends AdminBaseController
{
    //
    public function login()
    {
        $this->responseClient(200, 'ok', []);
    }
}
