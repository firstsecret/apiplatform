<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/19
 * Time: 13:20
 */

namespace App\Tool\server;

trait DarwinTool
{
//    use ServerTool;

    // Darwin
    function svr_darwin()
    {
        // 获取CPU信息
        if (false === ($res['cpu']['core'] = $this->getCommand("machdep.cpu.core_count"))) return false;

        $res['cpu']['processor'] = $this->getCommand("machdep.cpu.thread_count");
        $res['cpu']['cores'] = $res['cpu']['core'] . '核' . (($res['cpu']['processor']) ? '/' . $res['cpu']['processor'] . '线程' : '');
        $model = $this->getCommand("machdep.cpu.brand_string");
        $cache = $this->getCommand("machdep.cpu.cache.size") * $res['cpu']['core'];
        $cache = size_format($cache * 1024, 0);
        $res['cpu']['model'] = $model . ' [二级缓存：' . $cache . ']';

        // 获取服务器运行时间
        $uptime = $this->getCommand("kern.boottime");
        preg_match_all('#(?<={)\s?sec\s?=\s?+\d+#', $uptime, $matches);
        $_uptime = explode('=', $matches[0][0])[1];

        $str = time() - $_uptime;
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days . "天";
        if ($hours !== 0) $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        // 获取内存信息
        if (false === ($mTatol = $this->getCommand("hw.memsize"))) return false;
        $vmstat = $this->getCommand("", 'vm_stat', '');
        if (preg_match('/^Pages free:\s+(\S+)/m', $vmstat, $mfree)) {
            if (preg_match('/^File-backed pages:\s+(\S+)/m', $vmstat, $mcache)) {
                // OS X 10.9 or never
                $mFree = $mfree[1] * 4 * 1024;
                $mCached = $mcache[1] * 4 * 1024;
                if (preg_match('/^Pages occupied by compressor:\s+(\S+)/m', $vmstat, $mbuffer)) {
                    $mBuffer = $mbuffer[1] * 4 * 1024;
                }
            } else {
                if (preg_match('/^Pages speculative:\s+(\S+)/m', $vmstat, $spec_buf)) {
                    $mFree = ($mfree[1] + $spec_buf[1]) * 4 * 1024;
                } else {
                    $mFree = $mfree[1] * 4 * 1024;
                }
                if (preg_match('/^Pages inactive:\s+(\S+)/m', $vmstat, $inactive_buf)) {
                    $mCached = $inactive_buf[1] * 4 * 1024;
                }
            }
        } else {
            return false;
        }

        $mUsed = $mTatol - $mFree;

        $res['mTotal'] = $this->size_format($mTatol, 1);
        $res['mFree'] = $this->size_format($mFree, 1);
        $res['mBuffers'] = $this->size_format($mBuffer, 1);
        $res['mCached'] = $this->size_format($mCached, 1);
        $res['mUsed'] = $this->size_format($mUsed, 1);
        $res['mPercent'] = (floatval($mTatol) != 0) ? round($mUsed / $mTatol * 100, 1) : 0;
        $res['mCachedPercent'] = (floatval($mCached) != 0) ? round($mCached / $mTatol * 100, 1) : 0; //Cached内存使用率

        $swapInfo = $this->getCommand("vm.swapusage");
        $swap1 = preg_split('/M/', $swapInfo);
        $swap2 = preg_split('/=/', $swap1[0]);
        $swap3 = preg_split('/=/', $swap1[1]);
        $swap4 = preg_split('/=/', $swap1[2]);

        $sTotal = $swap2[1] * 1024 * 1024;
        $sUsed = $swap3[1] * 1024 * 1024;
        $sFree = $swap4[1] * 1024 * 1024;

        $res['swapTotal'] = $this->size_format($sTotal, 1);
        $res['swapFree'] = $this->size_format($sFree, 1);
        $res['swapUsed'] = $this->size_format($sUsed, 1);
        $res['swapPercent'] = (floatval($sTotal) != 0) ? round($sUsed / $sTotal * 100, 1) : 0;

        $res['mBool'] = true;
        $res['cBool'] = true;
        $res['rBool'] = false;
        $res['sBool'] = true;

        // CPU状态
        $cpustat = $this->getCommand(1, 'sar', '');
        preg_match_all("/Average\s{0,}\:+\s+\w+\s+\w+\s+\w+\s+\w+/s", $cpustat, $_cpu);
        $_cpu = preg_split("/\s+/", $_cpu[0][0]);
        $percent = $_cpu[1] + $_cpu[2] + $_cpu[3];
        $res['cpu']['percent'] = $percent;

        return $res;
    }

    function darwin_Network()
    {
        $netstat = $this->getCommand("-nbdi | cut -c1-24,42- | grep Link", "netstat");
        $res['nBool'] = $netstat ? true : false;
        $nets = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
        $_net = [];
        foreach ($nets as $net)
        {
            $buf = preg_split("/\s+/", $net, 10);
            if (!empty($buf[0]))
            {
                $dev_name = trim($buf[0]);
                $_net[$dev_name]['name'] = $dev_name;
                $_net[$dev_name]['rxbytes'] = $this->netSize($buf[5]);
                $_net[$dev_name]['txbytes'] = $this->netSize($buf[8]);
                $_net[$dev_name]['rxspeed'] = $buf[5];
                $_net[$dev_name]['txspeed'] = $buf[8];
                $_net[$dev_name]['errors'] = $buf[4] + $buf[7];
                $_net[$dev_name]['drops'] = isset($buf[10]) ? $buf[10] : "NULL";
            }
        }
        $res['net'] = $_net;
        return $res;
    }
}