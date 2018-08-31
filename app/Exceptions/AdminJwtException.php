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
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(400, $message ?: 'The version given was unknown or has no registered.', $previous, [], $code);
    }
}