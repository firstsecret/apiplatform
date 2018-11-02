<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/2
 * Time: 10:08
 */

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AppUserException  extends HttpException
{
    public function __construct($status_code = 4040, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($status_code, $message ?: '未知异常错误', $previous, [], $code);
    }
}