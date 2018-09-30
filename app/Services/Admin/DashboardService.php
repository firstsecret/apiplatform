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

/**
 * Class DashboardService  支持 curl 与 guzzlehttp(推荐,也是默认) 驱动 获取
 * @author Bevan
 * @package App\Services\Admin
 */
class DashboardService
{
    protected $c;

    protected $base_uri;

    public function __construct()
    {
        $this->c = new httpClient();
//        $this->c = new httpClient(['driver'=>'curl']);

        $this->base_uri = config('server_status_base_uri');
    }

    public function totalUser()
    {
        return User::count('id');
    }

    public function nginxStatus()
    {
        $data = $this->c->request->get($this->base_uri . '/status');

        return is_object($data) ? $this->formatNginxStatusObject($data->getBody()->getContents()) : $this->formatNginxStatus($data);
    }

    public function phpfpmStatus()
    {
        $data = $this->c->request->get($this->base_uri . '/fpm_status');

        return is_object($data) ? $this->formatPHPfpmStatusObject($data->getBody()->getContents()) : $this->formatPHPfpmStatus($data);
    }

    /**
     *
     * @param $data
     * @return array
     */
    protected function formatPHPfpmStatusObject($data)
    {
        $arr = explode("\n", $data);

        $na = [];

        foreach ($arr as $i) {
            $tmp = explode(':', $i);

            switch (count($tmp)) {
                case 0:
                    break;
                case 1:
                    if ($tmp[0]) $na[str_replace(" ", '_', trim($tmp[0]))] = '';
                    break;
                case 2:
                    $na[str_replace(" ", '_', trim($tmp[0]))] = trim($tmp[1]);
                    break;
                default:
                    $key = str_replace(" ", '_', trim($tmp[0]));
                    unset($tmp[0]);
                    $str = implode(':', $tmp);
                    $na[$key] = date('Y-m-d H:i:s', strtotime(trim($str)));
                    break;
            }
        }
        return $na;
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

    protected function handleStatus($str)
    {
        $t = array_filter(explode(" ", $str));
        $t[1] = isset($t[1]) ? trim($t[1]) : "0";
        return $t;
    }

    protected function formatNginxStatusObject($data)
    {
        $arr = array_filter(explode("\n", $data));
        $return_arr = [];

        foreach ($arr as $k => $i) {
            switch ($k) {
                case 0:
                    $tmp = explode(':', $i);
                    $return_arr[lcfirst(str_replace(" ", '_', trim($tmp[0])))] = trim($tmp[1]);
                    break;
                case 3:
                    $tmp = explode(':', $i);
                    $rw = $this->handleStatus($tmp[1]);
                    $return_arr[lcfirst($tmp[0])] = $rw[1];
                    $back = $this->handleStatus($tmp[2]);
                    $return_arr[lcfirst($rw[2])] = $back[1];
                    $return_arr[$back[2]] = trim($tmp[3]);
                    break;
                default:

                    $res = $this->getNginxRequestNum($arr[2]);

                    $return_arr = array_merge($return_arr, $res);
                    break;
            }
        }
        return $return_arr;
    }

    protected function getNginxRequestNum($str)
    {
        preg_match('/.*?(\d+)\s+(\d+)\s+(\d+).*?/', $str, $match);
        $arr['server'] = $match[1];
        $arr['accepts_handled'] = $match[2];
        $arr['requests'] = $match[3];
        return $arr;
    }

    protected function formatNginxStatus($str)
    {
//        dd($str);
        $return_data = $this->getNginxRequestNum($str);
        // 目前的 活跃连接数
        preg_match('/.*?Active connections:.*?(\w+).*?/', $str, $match);
        $return_data['active_connections'] = $match[1];
        // 读取客户端的连接数
        preg_match('/.*?Reading:.*?(\w+).*?/', $str, $match);
        $return_data['reading'] = $match[1];
        // 响应数据到客户端的数量
        preg_match('/.*?Writing:.*?(\w+).*?/', $str, $match);
        $return_data['writing'] = $match[1];
        // Nginx 已经处理完正在等候下一次请求指令的驻留连接
        preg_match('/.*?Waiting:.*?(\w+).*?/', $str, $match);
        $return_data['waiting'] = $match[1];

        return $return_data;
    }
}