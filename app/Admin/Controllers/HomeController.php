<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\view\DashboardController;
use App\Admin\Extensions\Tools\InfoBoxGender;
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

            $serviceBaseInfo = $this->getServiceBaseInfo();
            $cpuInfo = $this->linux_Network();
//            dd($cpuInfo);
            $phpfpm = $this->service->phpfpmStatus();
            $requestNginxAll = $this->service->nginxStatus();

            $health_check = $this->service->health_check();
//            dd($health_check);
            $content->row(function ($row) use ($requestNginxAll, $phpfpm) {
                $row->column(3, new InfoBoxGender('总用户数', 'users', 'aqua', '/', $this->service->totalUser()));
                $row->column(3, new InfoBoxGender('已处理请求数(重启重计)', 'location-arrow', 'yellow', '/', $requestNginxAll['requests']));
                $row->column(3, new InfoBoxGender('历史最大活跃进程数', 'file', 'green', '/', $phpfpm['max_active_processes']));
                $row->column(3, new InfoBoxGender('历史最高请求等待队列数', 'exchange', 'red', '/', $phpfpm['max_listen_queue']));
            });

//            $content->row(Dashboard::title());

            $script = <<<EOT
                var wsServer = 'ws://47.52.45.228:8555';
                var websocket = new WebSocket(wsServer);
                websocket.onopen = function (evt) {
//                    console.log("Connected to WebSocket server.");

                    websocket.send(JSON.stringify({"act":"rt"}))
                };
                
                websocket.onclose = function (evt) {
                    console.log("Disconnected");
                };
                
                websocket.onmessage = function (evt) {
                    console.log(evt.data);
                };
                
                websocket.onerror = function (evt, e) {
                    console.log(e)
                    console.log('Error occured: ' + evt.data);
                };

                function getCpuStatus()
                {
                    var cpuChart = echarts.init(document.getElementById('cpustatus'));

                    var data = [];
                    var time = [];
                    var _data = [];
                    var cpuPercent = 0;
                    var now = +new Date();

                    function randomData()
                    {
                        now = new Date(+now + 1000);
                        time = (now).getTime();
                        $.getJSON("?act=rt&callback=?", function (d) {
                            cpuPercent = d['cpuPercent'];
                        });
                        _data = {
                            name: time,
                            value: [
                                time,
                                cpuPercent
                            ]
                        };
                        return _data;
                    }

                    for (var i = 0; i < 60; i++) {
                        data.push(randomData());
                    }

                    var option = {
                        title: {
                            show: true,
                            text: 'CPU使用率',
                            left: 'center'
                        },
                        xAxis: {
                            type : 'time',
                            splitLine: {
                                show: false
                            }
                        },
                        yAxis: {
                            type: 'value',
                            boundaryGap: [0, '100%'],
                            max: 100,
                            splitLine: {
                                show: true
                            },
                            axisLabel: {
                                formatter: '{value} %'
                            }
                        },
                        series: [{
                            name: 'CPU使用率',
                            type: 'line',
                            showSymbol: false,
                            hoverAnimation: false,
                            data: data
                        }]
                    };
                    cpuChart.setOption(option);
                    timeTicket = setInterval(function () {
                        data.shift();
                        data.push(randomData());

                        cpuChart.setOption({
                            series: [{
                                data: data
                            }]
                        });
                    }, 1000);
                }
                
//                getCpuStatus()
EOT;

            Admin::script($script);

            $content->row(function (Row $row) use ($health_check) {
//                echo '<pre>';
                foreach ($health_check['upstream'] as $nodeName => $upstream) {
                    $row->column(3, function (Column $column) use ($nodeName, $upstream) {
                        $column->append(DashboardController::healthStatus($nodeName, $upstream));
                    });
                }
//                exit;
                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::environment());
                });
//
                $row->column(4, function (Column $column) {
                    $column->append(DashboardController::cpustatus());
                });
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::dependencies());
//                });
            });
        });
    }
}
