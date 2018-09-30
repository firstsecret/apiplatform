<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use App\Services\Admin\DashboardService;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
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

            $this->service->nginxStatus();


            $content->row(function ($row) {
                $row->column(3, new InfoBox('总用户数', 'users', 'aqua', '/', $this->service->totalUser()));
                $row->column(3, new InfoBox('正在处理的连接', 'exchange', 'green', '/', '150%'));
                $row->column(3, new InfoBox('总请求数', 'location-arrow', 'yellow', '/', '2786'));
                $row->column(3, new InfoBox('Documents', 'file', 'red', '/', '698726'));
            });

            $content->row(Dashboard::title());

            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::extensions());
//                });
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::dependencies());
//                });
            });
        });
    }
}
