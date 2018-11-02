<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 15:11
 */

namespace App\Admin\Controllers;

use App\Admin\Controllers\view\PluginController;

use App\Services\NodeService;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ServerConfigController
{
    protected $file_contents; // get file's content

    protected $now_request_url; // current request url

    protected $put_contents; // waiting to put contents

    protected $service;

    public function __construct(NodeService $nodeService)
    {
        $this->service = $nodeService;
    }

    public function settingConf(Request $request, $setting_conf)
    {
        $this->file_contents = $this->service->getConfFileContent($setting_conf);

        $this->now_request_url = $request->url();
        return Admin::content(function (Content $content) {

            $content->header('nginx配置管理');
            $content->description('nginx配置管理');

            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append(PluginController::codeeditor($this->file_contents));
                });

                // custom btn
                $row->column(12, function (Column $column) {
                    $config = [
                        'btn_url' => $this->now_request_url
                    ];
                    $column->append(PluginController::custombtn($config));
                });
            });
        });
    }

    public function updateConf(Request $request, $setting_conf)
    {
        // get file data
        $contents = $request->input('file_data');

        if (!$contents) {
            return response()->json([
                'status' => false,
                'message' => '文本内容为空，更新失败',
            ]);
        }

        $this->service->updateConfFile($contents, $setting_conf);

        return response()->json([
            'status' => true,
            'message' => '更新成功'
        ]);
    }
}