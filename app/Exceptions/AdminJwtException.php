<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 9:47
 */

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminJwtException extends HttpException
{
    public function __construct($code = 4040, $message = null, $status_code = 200, \Exception $previous = null)
    {
        parent::__construct($status_code, $message ?: '未知异常错误', $previous, [], $code);
    }
}