<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 14:22
 */

namespace App\Admin\Controllers;

use App\Admin\Controllers\view\PluginController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;

class SupervisorController
{
    public function index()
    {
        return Admin::content(function(Content $content){
            $content->header('进程管理工具');
            $content->description('进程管理工具');


            $content->row(function (Row $row)  {
                $row->column(12, function (Column $column) {
                    $column->append(PluginController::supervisor());
                });
            });

        });
    }
}