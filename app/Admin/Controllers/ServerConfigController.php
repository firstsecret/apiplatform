<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 15:11
 */

namespace App\Admin\Controllers;

use App\Admin\Controllers\view\PluginController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Storage;


class ServerConfigController
{
    protected $setting_conf;

    protected $file_contents;

    public function settingConf($setting_conf)
    {
        $this->setting_conf = $setting_conf;
        $this->file_contents = $this->getConfContent();

        return Admin::content(function (Content $content) {

            $content->header('nginx配置管理');
            $content->description('nginx配置管理');

            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append(PluginController::codeeditor($this->file_contents));
                });

                // custom btn
                $row->column(12, function (Column $column) {
                    $config['callback'] = $this->settingClickEvent();
                    $column->append(PluginController::custombtn($config));
                });
            });
        });
    }

    protected function settingClickEvent()
    {
        $url = '/';
        return <<<EOT
var url = $url;

swal({
                title: "确认修改吗",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                cancelButtonText: "取消",
                preConfirm: function() {
                    return new Promise(function(resolve) {
    
                        $.ajax({
                            method: 'update',
                            url: url,
                            data: {
           
                                _token:LA.token
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');
    
                                resolve(data);
                            }
                        });
    
                    });
                }
            }).then(function(result){
                var data = result.value;
                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            });
            
            return false
EOT;

    }

    protected function getConfContent()
    {
        $file_path = '/' . str_replace('_', '/', $this->setting_conf);

        return Storage::disk('server')->get($file_path);
    }
}