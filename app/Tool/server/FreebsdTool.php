<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/19
 * Time: 13:33
 */

namespace App\Tool\server;


trait FreebsdTool
{
//    use ServerTool;

    public function svr_freebsd()
    {
        // 获取cpu信息
        if (false === ($res['cpu']['core'] = $this->getCommand("kern.smp.cpus"))) return false;

        $res['cpu']['cores'] = $res['cpu']['core'] . '核';
        $model = $this->getCommand("hw.model");
        $res['cpu']['model'] = $model;

        // 获取服务器运行时间
        $uptime = $this->getCommand("kern.boottime");
        $uptime = preg_split("/ /", $uptime);
        $uptime = preg_replace('/,/', '', $uptime[3]);

        $str = time() - $uptime;
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days . "天";
        if ($hours !== 0) $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        // 获取内存信息
        if (false === ($mTatol = $this->getCommand("hw.physmem"))) return false;
        $pagesize = $this->getCommand("hw.pagesize");
        $vmstat = $this->getCommand("", "vmstat", "");
        $cached = $this->getCommand("vm.stats.vm.v_cache_count");
        $active = $this->getCommand("vm.stats.vm.v_active_count");
        $wire = $this->getCommand("vm.stats.vm.v_wire_count");
        $swapstat = $this->getCommand("", "swapctl", "-l -k");

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

        $res['mTotal'] = $this->size_format($mTatol, 1);
        $res['mFree'] = $this->size_format($mFree, 1);
        $res['mCached'] = $this->size_format($mCached, 1);
        $res['mUsed'] = $this->size_format($mUsed, 1);
        $res['mBuffers'] = $this->size_format($mBuffers, 1);
        $res['mPercent'] = (floatval($mTatol) != 0) ? round($mUsed / $mTatol * 100, 1) : 0;
        $res['mCachedPercent'] = (floatval($mCached) != 0) ? round($mCached / $mTatol * 100, 1) : 0; //Cached内存使用率
        $res['swapTotal'] = $this->size_format($sTotal, 1);
        $res['swapFree'] = $this->size_format($sFree, 1);
        $res['swapUsed'] = $this->size_format($sUsed, 1);
        $res['swapPercent'] = (floatval($sTotal) != 0) ? round($sUsed / $sTotal * 100, 1) : 0;

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

    function freebsd_Network()
    {
        $netstat = $this->getCommand("-nibd", "netstat");
        $res['nBool'] = $netstat ? true : false;
        $nets = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
        $_net = [];
        foreach ($nets as $net)
        {
            $buf = preg_split("/\s+/", $net);
            if (!empty($buf[0]))
            {
                if (preg_match('/^<Link/i', $buf[2]))
                {
                    $dev_name = trim($buf[0]);
                    $_net[$dev_name]['name'] = $dev_name;
                    if (strlen($buf[3]) < 17)
                    {
                        if (isset($buf[11]) && (trim($buf[11]) != ''))
                        {
                            $_net[$dev_name]['rxbytes'] = netSize($buf[6]);
                            $_net[$dev_name]['txbytes'] = netSize($buf[9]);
                            $_net[$dev_name]['rxspeed'] = $buf[6];
                            $_net[$dev_name]['txspeed'] = $buf[9];
                            $_net[$dev_name]['errors'] = $buf[4] + $buf[8];
                            $_net[$dev_name]['drops'] = $buf[11] + $buf[5];
                        }else{
                            $_net[$dev_name]['rxbytes'] = netSize($buf[5]);
                            $_net[$dev_name]['txbytes'] = netSize($buf[8]);
                            $_net[$dev_name]['rxspeed'] = $buf[5];
                            $_net[$dev_name]['txspeed'] = $buf[8];
                            $_net[$dev_name]['errors'] = $buf[4] + $buf[7];
                            $_net[$dev_name]['drops'] = $buf[10];
                        }
                    }else{
                        if (isset($buf[12]) && (trim($buf[12]) != ''))
                        {
                            $_net[$dev_name]['rxbytes'] = netSize($buf[7]);
                            $_net[$dev_name]['txbytes'] = netSize($buf[10]);
                            $_net[$dev_name]['rxspeed'] = $buf[7];
                            $_net[$dev_name]['txspeed'] = $buf[10];
                            $_net[$dev_name]['errors'] = $buf[5] + $buf[9];
                            $_net[$dev_name]['drops'] = $buf[12] + $buf[6];
                        }else{
                            $_net[$dev_name]['rxbytes'] = netSize($buf[6]);
                            $_net[$dev_name]['txbytes'] = netSize($buf[9]);
                            $_net[$dev_name]['rxspeed'] = $buf[6];
                            $_net[$dev_name]['txspeed'] = $buf[9];
                            $_net[$dev_name]['errors'] = $buf[5] + $buf[8];
                            $_net[$dev_name]['drops'] = $buf[11];
                        }
                    }
                }
            }
        }
        $res['net'] = $_net;
        return $res;
    }
}