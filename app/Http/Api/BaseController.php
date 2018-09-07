<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Tool\AppTool;
use Dingo\Api\Routing\Helpers;


class BaseController extends Controller
{
    //
    use Helpers;

    use AppTool;
}
