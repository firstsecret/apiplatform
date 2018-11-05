<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/5
 * Time: 14:52
 */


/**
 *
 * 标注 lua , 说明在 nginx 中间件中 也有修改, 如需修改 ,还需修改 lua 代码 重启nginx server , 谨慎!!!
 */
return [
    'health_check_timer' => env('HEALTH_CHECK_TIMER', 'health_check_timer'),  // 用于 存储 健康检查 的 定时器 fd -- key value

    /*****************************  lua **************************/
    'node_load_balancing' => env('NODE_LOAD_BALANCING', 'node_load_balancing'), //  用户 存储 目前 可用的 api服务节点 -- set
    'services_map' => env('SERVICES_MAP', 'services_map'), // 服务内外请求映射   -- hash
];