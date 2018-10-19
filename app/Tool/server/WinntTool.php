<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/19
 * Time: 13:37
 */

namespace App\Tool\server;


trait WinntTool
{
    use ServerTool;

    function svr_winnt()
    {
        // 获取CPU信息
        if (get_cfg_var("com.allow_dcom")) {
            $wmi = new \COM('winmgmts:{impersonationLevel=impersonate}');
            $cpuinfo = $this->getWMI($wmi, "Win32_Processor", "Name,LoadPercentage,NumberOfCores,NumberOfLogicalProcessors,L2CacheSize");
        } else if (function_exists('exec')) {
            exec("wmic cpu get LoadPercentage,NumberOfCores,NumberOfLogicalProcessors,L2CacheSize", $cpuwmic);
            exec("wmic cpu get Name", $cpuname);
            $cpuKey = preg_split("/ +/", $cpuwmic[0]);
            $cpuValue = preg_split("/ +/", $cpuwmic[1]);
            foreach ($cpuKey as $k => $v) {
                $cpuinfo[$v] = $cpuValue[$k];
            }
            $cpuinfo['Name'] = $cpuname[1];
        } else {
            return false;
        }

        $res['cpu']['core'] = $cpuinfo['NumberOfCores'];
        $res['cpu']['processor'] = $cpuinfo['NumberOfLogicalProcessors'];
        $res['cpu']['cores'] = $res['cpu']['core'] . '核' . (($res['cpu']['processor']) ? '/' . $res['cpu']['processor'] . '线程' : '');
        $cache = size_format($cpuinfo['L2CacheSize'] * 1024, 0);
        $res['cpu']['model'] = $cpuinfo['Name'] . ' [二级缓存：' . $cache . ']';

        // 获取服务器运行时间
        if (get_cfg_var("com.allow_dcom")) {
            $sysinfo = $this->getWMI($wmi, "Win32_OperatingSystem", "LastBootUpTime,TotalVisibleMemorySize,FreePhysicalMemory");
        } else if (function_exists("exec")) {
            exec("wmic os get LastBootUpTime,TotalVisibleMemorySize,FreePhysicalMemory", $osInfo);
            $osKey = preg_split("/ +/", $osInfo[0]);
            $osValue = preg_split("/ +/", $osInfo[1]);
            foreach ($osKey as $key => $value) {
                $sysinfo[$value] = $osValue[$key];
            }
        } else {
            return false;
        }

        $res['uptime'] = $sysinfo['LastBootUpTime'];
        $str = time() - strtotime(substr($res['uptime'], 0, 14));
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days . "天";
        if ($hours !== 0) $res['uptime'] .= $hours . "小时";
        $res['uptime'] .= $min . "分钟";

        // 获取内存信息
        $mTotal = $sysinfo['TotalVisibleMemorySize'] * 1024;
        $mFree = $sysinfo['FreePhysicalMemory'] * 1024;
        $mUsed = $mTotal - $mFree;

        $res['mTotal'] = size_format($mTotal, 1);
        $res['mFree'] = size_format($mFree, 1);
        $res['mUsed'] = size_format($mUsed, 1);
        $res['mPercent'] = round($mUsed / $mTotal * 100, 1);

        if (get_cfg_var("com.allow_dcom")) {
            $swapinfo = $this->getWMI($wmi, "Win32_PageFileUsage", 'AllocatedBaseSize,CurrentUsage');
        } else if (function_exists("exec")) {
            exec("wmic pagefile get AllocatedBaseSize,CurrentUsage", $swaps);
            $swapKey = preg_split("/ +/", $swaps[0]);
            $swapValue = preg_split("/ +/", $swaps[1]);
            foreach ($swapKey as $sk => $sv) {
                $swapinfo[$sv] = $swapValue[$sk];
            }
        } else {
            return false;
        }
        $sTotal = $swapinfo['AllocatedBaseSize'] * 1024 * 1024;
        $sUsed = $swapinfo['CurrentUsage'] * 1024 * 1024;
        $sFree = $sTotal - $sUsed;

        $res['swapTotal'] = $this->size_format($sTotal, 1);
        $res['swapUsed'] = $this->size_format($sUsed, 1);
        $res['swapFree'] = $this->size_format($sFree, 1);
        $res['swapPercent'] = (floatval($sTotal) != 0) ? round($sUsed / $sTotal * 100, 1) : 0;

        $res['mBool'] = true;
        $res['cBool'] = false;
        $res['rBool'] = false;
        $res['sBool'] = true;

        // CPU状态
        $res['cpu']['percent'] = $cpuinfo['LoadPercentage'];

        return $res;
    }

    function getWMI($wmi, $strClass, $strValue)
    {
        $object = $wmi->ExecQuery("SELECT {$strValue} FROM {$strClass}");
        $value = explode(",", $strValue);
        $arrData = [];
        foreach ($value as $v) {
            foreach ($object as $info) {
                $arrData[$v] = $info->$v;
            }
        }

        return $arrData;
    }
}