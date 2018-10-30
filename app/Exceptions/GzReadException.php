<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/30
 * Time: 16:29
 */

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class GzReadException extends HttpException
{
    public function __construct($statusCode = 400, $message = null, $code = 4070, \Exception $previous = null)
    {
        parent::__construct($statusCode, $message ?: '未知异常错误', $previous, [], $code);
    }
}