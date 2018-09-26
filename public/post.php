<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/26
 * Time: 10:18
 */


var_dump($_SERVER['REQUEST_METHOD']);

if(strtolower($_SERVER['REQUEST_METHOD']) == 'get')
{
    require __DIR__ . '/show.html';
}else{
    // post
    var_dump($_POST);
}