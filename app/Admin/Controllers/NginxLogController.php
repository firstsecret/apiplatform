<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/30
 * Time: 14:56
 */

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\NginxLogViewer;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class NginxLogController
{
    public function index($file=null, Request $request)
    {

        return Admin::content(function (Content $content) use ($file, $request) {
            $offset = $request->get('offset');

            $viewer = new NginxLogViewer($file);

            $content->body(view('admin.plugin.nginxconf', [
                'logs'      => $viewer->fetch($offset),
                'logFiles'  => $viewer->getLogFiles(),
                'fileName'  => $viewer->file,
                'end'       => $viewer->getFilesize(),
                'tailPath'  => route('custom-nginx-log', ['file' => $viewer->file]),
                'prevUrl'   => $viewer->getPrevPageUrl(),
                'nextUrl'   => $viewer->getNextPageUrl(),
                'filePath'  => $viewer->getFilePath(),
                'size'      => static::bytesToHuman($viewer->getFilesize()),
            ]));

            $content->header($viewer->getFilePath());
        });
    }

    protected static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}