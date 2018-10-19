<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/18
 * Time: 16:03
 */

namespace App\Tool;

use App\Tool\server\DarwinTool;
use App\Tool\server\FreebsdTool;
use App\Tool\server\LinuxTool;
use App\Tool\server\WinntTool;

//use App\Tool\server\ServerTool;

trait ProbeTool
{
    use DarwinTool;
    use LinuxTool;
    use FreebsdTool;
    use WinntTool;

    public $baseInfo;

    public function getServiceBaseInfo()
    {
        $this->baseInfo['getServerHosts'] = get_current_user() . '/' . filter_input(INPUT_SERVER, 'SERVER_NAME') . '(' . gethostbyname(filter_input(INPUT_SERVER, 'SERVER_NAME')) . ')'; // 获取服务器域名/ip
        $this->baseInfo['getServerOS'] = PHP_OS . ' ' . php_uname('r'); // 获取服务器操作系统
        $this->baseInfo['getServerSoftWare'] = filter_input(INPUT_SERVER, 'SERVER_SOFTWARE'); // 获取服务器类型和版本
        $this->baseInfo['getServerLang'] = getenv("HTTP_ACCEPT_LANGUAGE"); // 获取服务器语言
        $this->baseInfo['getServerPort'] = filter_input(INPUT_SERVER, 'SERVER_PORT'); // 获取服务器端口
        $this->baseInfo['getServerHostName'] = php_uname('n'); // 获取服务器主机名
        $this->baseInfo['getServerAdminMail'] = filter_input(INPUT_SERVER, 'SERVER_ADMIN'); // 获取服务器管理员邮箱
        $this->baseInfo['getServerTzPath'] = __FILE__; // 获取探针路径

        return $this->baseInfo;
    }

    function switchOsInfo($is_constantly = true)
    {
        switch (PHP_OS) {
            case "Linux":
                $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = $this->svr_linux())) ? "show" : "none") : "none";
                $svrInfo = array_merge($svrInfo, $this->linux_Network());
                break;
            case "FreeBSD":
                $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = $this->svr_freebsd())) ? "show" : "none") : "none";
                $svrInfo = array_merge($svrInfo, $this->freebsd_Network());
                break;
            case "Darwin":
                $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = $this->svr_darwin())) ? "show" : "none") : "none";
                $svrInfo = array_merge($svrInfo, $this->darwin_Network());
                break;
            case "WINNT":
                $is_constantly = false;
                $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = $this->svr_winnt())) ? "show" : "none") : "none";
                break;
            default :
                break;
        }

        return ['svrShow' => $svrShow, 'svrInof' => $svrInfo];
    }

}