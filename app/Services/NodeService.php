<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/2
 * Time: 15:19
 */

namespace App\Services;

use App\Events\ReloadServerEvent;
use App\Models\Service;
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

    public function updateNodeByDb()
    {
//        $str = $this->getConfFileContent('nginx/upstream/upstream.conf');
        $nodes = Service::all(['service_host', 'service_host_port'])->toArray();

        $upstream_node_str = $this->spliceUpstreamNode($nodes);

//        dd($upstream_node_str);
        $this->updateConfFile($upstream_node_str, 'nginx/upstream/upstream.conf');
    }

    private function spliceUpstreamNode($nodes)
    {
        $node_str = "upstream api.com {\n";
        foreach ($nodes as $node) {
            $node_str .= '   server ' . $node['service_host'] . ':' . $node['service_host_port'] . ";\n";
        }
        $node_str .= "}";

        return $node_str;
    }
}