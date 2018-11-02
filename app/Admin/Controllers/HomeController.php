<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\view\DashboardController;
use App\Admin\Extensions\Tools\InfoBoxGender;
use App\Exceptions\AppUserException;
use App\Http\Controllers\Controller;
use App\Tool\ProbeTool;
use Encore\Admin\Controllers\Dashboard;
use App\Services\Admin\DashboardService;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

//use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    use ProbeTool;

    public $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('首页控制台');
            $content->description('首页...');
            $server_status = $this->service->getServerStatus();
            $phpfpm = $server_status['fpm_status'];
            $requestNginxAll = $server_status['status'];
            $health_check = $server_status['health_check'];
//            dd($cpuInfo);
//            $phpfpm = $this->service->phpfpmStatus();
//            $requestNginxAll = $this->service->nginxStatus();

//            $health_check = $this->service->health_check();

//            dd($health_check);
            $content->row(function ($row) use ($requestNginxAll, $phpfpm) {
                $row->column(3, new InfoBoxGender('总用户数', 'users', 'aqua', '/', $this->service->totalUser()));
                $row->column(3, new InfoBoxGender('已处理请求数(重启重计)', 'location-arrow', 'yellow', '/', $requestNginxAll['requests']));
                $row->column(3, new InfoBoxGender('历史最大活跃进程数', 'file', 'green', '/', $phpfpm['max_active_processes']));
                $row->column(3, new InfoBoxGender('历史最高请求等待队列数', 'exchange', 'red', '/', $phpfpm['max_listen_queue']));
            });

//            $content->row(Dashboard::title());

//            $this->loadExtScript();

            $content->row(function (Row $row) use ($health_check) {
//                echo '<pre>';
                foreach ($health_check['upstream'] as $nodeName => $upstream) {
                    $row->column(3, function (Column $column) use ($nodeName, $upstream) {
                        $column->append(DashboardController::healthStatus($nodeName, $upstream));
                    });
                }

                // server status
                $row->column(12, function (Column $column) {
                    $column->append(DashboardController::serverstatus());
                });

                // net status
                $row->column(12, function (Column $column) {
                    $column->append(DashboardController::netstatus());
                });

                // memory status
                $row->column(8, function (Column $column) {
                    $column->append(DashboardController::memorystatus());
                });

                $row->column(4, function (Column $column) {
                    $column->append(DashboardController::hddstatus());
                });

//                env;
                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::environment());
                });
                //

//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::dependencies());
//                });
            });
        });
    }

    protected function loadExtScript()
    {
//        $script = $this->getCpuScript();
//
//        Admin::script($script);

//        $mscript = $this->getMemoryScript();
//
//        Admin::script($mscript);
//
//        $monceScript = $this->initMemoryScript();
//
//        Admin::script($monceScript);
//
//        $serverScript = $this->serverScript();
//
//        Admin::script($serverScript);

//        $netScrpit = $this->netScript();
//
//        Admin::script($netScrpit);

//        $hddScript = $this->hddScript();
//
//        Admin::script($hddScript);
    }
}
