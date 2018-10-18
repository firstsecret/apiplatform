<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/18
 * Time: 16:03
 */

namespace App\Tool;

trait ProbeTool
{
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

    function linux_Network()
    {
        $net = [];
        $netstat = file_get_contents('/proc/net/dev');
        $res['nBool'] = $netstat ? true : false;
        $bufe = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($bufe as $buf) {
            if (preg_match('/:/', $buf)) {
                list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                $dev_name = trim($dev_name);
                $stats = preg_split('/\s+/', trim($stats_list));
                $net[$dev_name]['name'] = $dev_name;
                $net[$dev_name]['rxbytes'] = $this->netSize($stats[0]);
                $net[$dev_name]['txbytes'] = $this->netSize($stats[8]);
                $net[$dev_name]['rxspeed'] = $stats[0];
                $net[$dev_name]['txspeed'] = $stats[8];
                $net[$dev_name]['errors'] = $stats[2] + $stats[10];
                $net[$dev_name]['drops'] = $stats[3] + $stats[11];
            }
        }
        $res['net'] = $net;
        return $res;
    }

    function netSize($size, $decimals = 2)
    {
        if($size < 1024) {
            $unit="Bbps";
        } else if($size < 10240) {
            $size=round($size/1024, $decimals);
            $unit="Kbps";
        } else if($size < 102400) {
            $size=round($size/1024, $decimals);
            $unit="Kbps";
        } else if($size < 1048576) {
            $size=round($size/1024, $decimals);
            $unit="Kbps";
        } else if ($size < 10485760) {
            $size=round($size/1048576, $decimals);
            $unit="Mbps";
        } else if ($size < 104857600) {
            $size=round($size/1048576,$decimals);
            $unit="Mbps";
        } else if ($size < 1073741824) {
            $size=round($size/1048576, $decimals);
            $unit="Mbps";
        } else {
            $size=round($size/1073741824, $decimals);
            $unit="Gbps";
        }
        $size .= $unit;
        return $size;
    }

    public function getCpuInfo()
    {
        $cpu = [];
        $str = file_get_contents("/proc/stat");
        $mode = "/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
        preg_match_all($mode, $str, $cpu);
        $total = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[5][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        $time = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        return [
            'total' => $total,
            'time' => $time
        ];
    }

    public function svr_linux()
    {
        // 获取CPU信息
        if (false === ($str = file_get_contents("/proc/cpuinfo"))) return false;
        if(is_array($str)) $str = implode(":", $str);
        preg_match_all("/model\sname\s+\:(.*)[\r\n]+/isU", $str, $model);
        preg_match_all("/cache\ssize\s+:\s*(.*)[\r\n]+/isU", $str, $cache);
        preg_match_all("/cpu\sMHz\s+:\s*(.*)[\r\n]+/isU", $str, $mhz);
        preg_match_all("/bogomips\s+:\s*(.*)[\r\n]+/isU", $str, $bogomips);
        preg_match_all("/core\sid\s+:\s*(.[1-9])[\r\n]+/isU", $str, $cores);
        if(false !== is_array($model[1]))
        {
            $res['cpu']['core'] = sizeof($cores[1]);
            $res['cpu']['processor'] = sizeof($model[1]);
            $res['cpu']['cores'] = $res['cpu']['core'].'核'.(($res['cpu']['processor']) ? '/'.$res['cpu']['processor'].'线程' : '');
            foreach($model[1] as $k=>$v)
            {
                $mhz[1][$k] = ' | 频率:'.$mhz[1][$k];
                $cache[1][$k] = ' | 二级缓存:'.$cache[1][$k];
                $bogomips[1][$k] = ' | Bogomips:'.$bogomips[1][$k];
                $res['cpu']['model'][] = $model[1][$k].$mhz[1][$k].$cache[1][$k].$bogomips[1][$k];
            }

            if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
        }
        // 获取服务器运行时间
        if (false === ($str = file_get_contents("/proc/uptime"))) return false;
        $str = explode(" ", $str);
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";

        // 获取内存信息
        if (false === ($str = file_get_contents("/proc/meminfo"))) return false;
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $mems);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

        $mtotal = $mems[1][0] * 1024;
        $mfree = $mems[2][0] * 1024;
        $mbuffers = $buffers[1][0] * 1024;
        $mcached = $mems[3][0] * 1024;
        $stotal = $mems[4][0] * 1024;
        $sfree = $mems[5][0] * 1024;
        $mused = $mtotal - $mfree;
        $sused = $stotal - $sfree;
        $mrealused = $mtotal - $mfree - $mcached - $mbuffers; //真实内存使用

        $res['mTotal'] = size_format($mtotal,1);
        $res['mFree'] = size_format($mfree,1);
        $res['mBuffers'] = size_format($mbuffers,1);
        $res['mCached'] = size_format($mcached,1);
        $res['mUsed'] = size_format($mtotal - $mfree,1);
        $res['mPercent'] = (floatval($mtotal) != 0) ? round($mused/$mtotal * 100, 1) : 0;
        $res['mRealUsed'] = size_format($mrealused,1);
        $res['mRealFree'] = size_format($mtotal - $mrealused,1); //真实空闲
        $res['mRealPercent'] = (floatval($mtotal) != 0) ? round($mrealused/$mtotal * 100, 1) : 0; //真实内存使用率
        $res['mCachedPercent'] = (floatval($mcached)!=0) ? round($mcached/$mtotal*100,1) : 0; //Cached内存使用率
        $res['swapTotal'] = size_format($stotal,1);
        $res['swapFree'] = size_format($sfree,1);
        $res['swapUsed'] = size_format($sused,1);
        $res['swapPercent'] = (floatval($stotal) != 0) ? round($sused/$stotal * 100, 1) : 0;

        $res['mBool'] = true;
        $res['cBool'] = true;
        $res['rBool'] = true;
        $res['sBool'] = true;

        // cpu状态
        if (false === ($str = file_get_contents("/proc/stat"))) return false;
        $cpuinfo1 = getCpuInfo($str);
        sleep(1);
        $cpuinfo2 = getCpuInfo($str);
        $time = $cpuinfo2['time'] - $cpuinfo1['time'];
        $total = $cpuinfo2['total'] - $cpuinfo1['total'];
        $percent = round($time/$total,4);
        $percent = $percent * 100;
        $res['cpu']['percent'] = $percent;
        return $res;
    }

    public function svr_freebsd()
    {
        // 获取cpu信息
        if(false === ($res['cpu']['core'] = getCommand("kern.smp.cpus"))) return false;

        $res['cpu']['cores'] = $res['cpu']['core'].'核';
        $model = getCommand("hw.model");
        $res['cpu']['model'] = $model;

        // 获取服务器运行时间
        $uptime = getCommand("kern.boottime");
        $uptime = preg_split("/ /", $uptime);
        $uptime = preg_replace('/,/', '', $uptime[3]);

        $str = time() - $uptime;
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";

        // 获取内存信息
        if(false === ($mTatol = getCommand("hw.physmem"))) return false;
        $pagesize = getCommand("hw.pagesize");
        $vmstat = getCommand("","vmstat", "");
        $cached = getCommand("vm.stats.vm.v_cache_count");
        $active = getCommand("vm.stats.vm.v_active_count");
        $wire = getCommand("vm.stats.vm.v_wire_count");
        $swapstat = getCommand("", "swapctl", "-l -k");

        $mlines = preg_split("/\n/", $vmstat, -1, 1);
        $mbuf = preg_split("/\s+/", trim($mlines[2]), 19);
        $slines = preg_split("/\n/", $swapstat, -1, 1);
        $sbuf = preg_split("/\s+/", $slines[1], 6);

        $app = ($active + $wire) * $pagesize;
        $mTatol = $mTatol;
        $mFree = $mbuf[4] * 1024;
        $mCached = $cached * $pagesize;
        $mUsed = $mTatol - $mFree;
        $mBuffers = $mUsed - $app - $mCached;
        $sTotal = $sbuf[1] * 1024;
        $sUsed = $sbuf[2] * 1024;
        $sFree = $sTotal - $sUsed;

        $res['mTotal'] = size_format($mTatol,1);
        $res['mFree'] = size_format($mFree,1);
        $res['mCached'] = size_format($mCached,1);
        $res['mUsed'] = size_format($mUsed,1);
        $res['mBuffers'] = size_format($mBuffers,1);
        $res['mPercent'] = (floatval($mTatol) != 0) ? round($mUsed/$mTatol * 100, 1) : 0;
        $res['mCachedPercent'] = (floatval($mCached)!=0) ? round($mCached/$mTatol * 100, 1) : 0; //Cached内存使用率
        $res['swapTotal'] = size_format($sTotal,1);
        $res['swapFree'] = size_format($sFree,1);
        $res['swapUsed'] = size_format($sUsed,1);
        $res['swapPercent'] = (floatval($sTotal) != 0) ? round($sUsed/$sTotal * 100, 1) : 0;

        $res['mBool'] = true;
        $res['cBool'] = true;
        $res['rBool'] = false;
        $res['sBool'] = true;

        // CPU状态
        $cpustat = $mbuf;
        $percent = $cpustat[16] + $cpustat[17];
        $res['cpu']['percent'] = $percent;

        return $res;
    }

    public function getCommand($args = '', $commandName = 'sysctl', $option = '-n')
    {
        if (false === ($commandPath = findCommand($commandName))) return false;

        if($command = shell_exec("$commandPath $option $args"))
        {
            return trim($command);
        }
        return false;
    }
    function findCommand($commandName)
    {
        $paths = ['/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin'];

        foreach($paths as $path)
        {
            if (is_executable("$path/$commandName")) return "$path/$commandName";
        }
        return false;
    }
}