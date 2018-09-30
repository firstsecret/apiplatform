<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/9/29
 * Time: 17:23
 */

namespace App\Services\Admin;


use App\Client\httpClient;
use App\User;

class DashboardService
{
    protected $c;

    public function __construct()
    {
        $this->c = new httpClient(['driver' => 'curl']);
    }

    public function totalUser()
    {
        return User::count('id');
    }

    public function nginxStatus()
    {
        $data = $this->c->request->get('http://127.0.0.1:81/status');
//        print_r($data);
        return $this->formatNginxStatus($data);
    }

    public function phpfpmStatus()
    {
        $data = $this->c->request->get('http://127.0.0.1:81/fpm_status');

//        print_r($data);

        return $this->formatPHPfpmStatus($data);
    }

    protected function formatPHPfpmStatus($data)
    {
        $return_data = [];
        // 连接池名
        preg_match('/.*?pool:.*?(\w+).*?/', $data, $match);
        $return_data['pool'] = $match['1'];

        // 当前池接受的请求数
        preg_match('/.*?accepted conn:.*?(\d+).*?/', $data, $match);
        $return_data['accepted_conn'] = $match[1];

        // 总进程数量
        preg_match('/.*?total processes:.*?(\d+).*?/', $data, $match);
        $return_data['total_processes'] = $match['1'];

        // 最大排队数
        preg_match('/.*?max listen queue:.*?(\d+).*?/', $data, $match);
        $return_data['max_listen_queue'] = $match['1'];

        // 空闲进程
        preg_match('/.*?idle processes:.*?(\d+).*?/', $data, $match);
        $return_data['idle_processes'] = $match[1];

        // 活跃进程
        preg_match('/.*?active processes:.*?(\d+).*?/', $data, $match);
        $return_data['active_processes'] = $match[1];

        // 总进程数
        preg_match('/.*?total processes:.*?(\d+).*?/', $data, $match);
        $return_data['total_processes'] = $match[1];

        // 最大活跃进程数
        preg_match('/.*?max active processes:.*?(\d+).*?/', $data, $match);
        $return_data['max_active_processes'] = $match[1];

        // 进程最大数量 限制 次数
        preg_match('/.*?max children reached:.*?(\d+).*?/', $data, $match);
        $return_data['max_children_reached'] = $match[1];

        // 运行时间
        preg_match('/.*?start since:.*?(\d+).*?/', $data, $match);
        $return_data['start_since'] = $match[1];

        // 进程管理方式
        preg_match('/.*?process manager:.*?(\w+).*?/', $data, $match);
        $return_data['process_manager'] = $match[1];

        // 启动时间
        preg_match('/.*?start time:\s+(.*)/', $data, $match);
        $return_data['start_time'] = date('Y-m-d H:i:s', strtotime($match[1]));

        // 目前请求的排队数
        preg_match('/.*?listen queue:.*?(\w+).*?/', $data, $match);
        $return_data['listen_queue'] = $match[1];

        return $return_data;
    }

    protected function formatNginxStatus($str)
    {
        preg_match('/.*?(\d+)\s+(\d+)\s+(\d+).*?/', $str, $match);

        return $match[3];
    }
}