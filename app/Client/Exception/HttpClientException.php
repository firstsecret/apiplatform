<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/12
 * Time: 17:33
 */

namespace App\Client\Exception;


use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpClientException extends HttpException
{
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(400, $message ?: '未知异常错误', $previous, [], $code);
    }
}