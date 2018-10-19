<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/19
 * Time: 13:30
 */

namespace App\Tool\server;


trait LinuxTool
{
    use ServerTool;

    public function svr_linux()
    {
        // 获取CPU信息
        if (false === ($str = file_get_contents("/proc/cpuinfo"))) return false;
        if (is_array($str)) $str = implode(":", $str);
        preg_match_all("/model\sname\s+\:(.*)[\r\n]+/isU", $str, $model);
        preg_match_all("/cache\ssize\s+:\s*(.*)[\r\n]+/isU", $str, $cache);
        preg_match_all("/cpu\sMHz\s+:\s*(.*)[\r\n]+/isU", $str, $mhz);
        preg_match_all("/bogomips\s+:\s*(.*)[\r\n]+/isU", $str, $bogomips);
        preg_match_all("/core\sid\s+:\s*(.[1-9])[\r\n]+/isU", $str, $cores);
        if (false !== is_array($model[1])) {
            $res['cpu']['core'] = sizeof($cores[1]);
            $res['cpu']['processor'] = sizeof($model[1]);
            $res['cpu']['cores'] = $res['cpu']['core'] . '核' . (($res['cpu']['processor']) ? '/' . $res['cpu']['processor'] . '线程' : '');
            foreach ($model[1] as $k => $v) {
                $mhz[1][$k] = ' | 频率:' . $mhz[1][$k];
                $cache[1][$k] = ' | 二级缓存:' . $cache[1][$k];
                $bogomips[1][$k] = ' | Bogomips:' . $bogomips[1][$k];
                $res['cpu']['model'][] = $model[1][$k] . $mhz[1][$k] . $cache[1][$k] . $bogomips[1][$k];
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
        if ($days !== 0) $res['uptime'] = $days . "天";
        if ($hours !== 0) $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

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

        $res['mTotal'] = $this->size_format($mtotal, 1);
        $res['mFree'] = $this->size_format($mfree, 1);
        $res['mBuffers'] = $this->size_format($mbuffers, 1);
        $res['mCached'] = $this->size_format($mcached, 1);
        $res['mUsed'] = $this->size_format($mtotal - $mfree, 1);
        $res['mPercent'] = (floatval($mtotal) != 0) ? round($mused / $mtotal * 100, 1) : 0;
        $res['mRealUsed'] = $this->size_format($mrealused, 1);
        $res['mRealFree'] = $this->size_format($mtotal - $mrealused, 1); //真实空闲
        $res['mRealPercent'] = (floatval($mtotal) != 0) ? round($mrealused / $mtotal * 100, 1) : 0; //真实内存使用率
        $res['mCachedPercent'] = (floatval($mcached) != 0) ? round($mcached / $mtotal * 100, 1) : 0; //Cached内存使用率
        $res['swapTotal'] = $this->size_format($stotal, 1);
        $res['swapFree'] = $this->size_format($sfree, 1);
        $res['swapUsed'] = $this->size_format($sused, 1);
        $res['swapPercent'] = (floatval($stotal) != 0) ? round($sused / $stotal * 100, 1) : 0;

        $res['mBool'] = true;
        $res['cBool'] = true;
        $res['rBool'] = true;
        $res['sBool'] = true;

        // cpu状态
        if (false === ($str = file_get_contents("/proc/stat"))) return false;
        $cpuinfo1 = $this->getCpuInfo($str);
        sleep(1);
        $cpuinfo2 = $this->getCpuInfo($str);
        $time = $cpuinfo2['time'] - $cpuinfo1['time'];
        $total = $cpuinfo2['total'] - $cpuinfo1['total'];
        $percent = round($time / $total, 4);
        $percent = $percent * 100;
        $res['cpu']['percent'] = $percent;
        return $res;
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
}