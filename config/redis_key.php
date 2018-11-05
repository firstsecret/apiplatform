<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/5
 * Time: 14:52
 */

return [
    'health_check_timer' => env('HEALTH_CHECK_TIMER', 'health_check_timer'),  // 用于 存储 健康检查 的 定时器 fd
    'node_load_balancing' => env('NODE_LOAD_BALANCING', 'node_load_balancing'), //  用户 存储 目前 可用的 api服务节点
];