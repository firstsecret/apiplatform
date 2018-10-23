<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 13:37
 */

namespace App\Admin\Controllers;


use App\Admin\Controllers\view\PluginController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;

class HorizonController
{
    public function index()
    {
       return Admin::content(function(Content $content){
           $content->header('任务队列管理工具');
           $content->description('任务队列管理工具');


           $content->row(function (Row $row)  {
               $row->column(12, function (Column $column) {
                   $column->append(PluginController::horizon());
               });
           });

       });
    }
}