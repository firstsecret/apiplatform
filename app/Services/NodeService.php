<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/2
 * Time: 15:19
 */

namespace App\Services;

use App\Events\ReloadServerEvent;
use Illuminate\Support\Facades\Storage;

class NodeService
{
    public function updateConfFile($content, $file_path)
    {
        $file_path = $this->formatFilePath($file_path);
        event(new ReloadServerEvent());
        return Storage::disk('server')->put($file_path, $content);
    }

    public function getConfFileContent($file_path)
    {
        $file_path = $this->formatFilePath($file_path);
        return Storage::disk('server')->get($file_path);
    }

    public function formatFilePath($setting_conf)
    {
        return '/' . str_replace('_', '/', $setting_conf);
    }
}