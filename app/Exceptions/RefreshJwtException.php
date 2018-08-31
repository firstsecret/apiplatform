<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 9:47
 */

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;

class RefreshJwtException extends HttpException
{
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(400, $message ?: '未知错误', $previous, [], $code);
    }
}