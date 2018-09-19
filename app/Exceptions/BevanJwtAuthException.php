<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 16:50
 */

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

class BevanJwtAuthException extends HttpException
{
    public function __construct($statusCode = 4030, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($statusCode, $message ?: '未知异常错误', $previous, [], $code);
    }

}