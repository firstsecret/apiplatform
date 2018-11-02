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
    public function __construct($code = 4010, $message = null, $status_code = 0, \Exception $previous = null)
    {
        parent::__construct($status_code, $message ?: '未知错误', $previous, [], $code);
    }
}