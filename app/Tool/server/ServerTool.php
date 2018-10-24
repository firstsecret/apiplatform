<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/19
 * Time: 13:21
 */

namespace App\Tool\server;


trait ServerTool
{
    function netSize($size, $decimals = 2)
    {
        if ($size < 1024) {
            $unit = "Bbps";
        } else if ($size < 10240) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 102400) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 1048576) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 10485760) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else if ($size < 104857600) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else if ($size < 1073741824) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else {
            $size = round($size / 1073741824, $decimals);
            $unit = "Gbps";
        }
        $size .= $unit;
        return $size;
    }

    function size_format($bytes, $decimals = 2)
    {
        $quant = array(
            'TB' => 1099511627776, // pow( 1024, 4)
            'GB' => 1073741824, // pow( 1024, 3)
            'MB' => 1048576, // pow( 1024, 2)
            'KB' => 1024, // pow( 1024, 1)
            'B ' => 1, // pow( 1024, 0)
        );

        foreach ($quant as $unit => $mag) {
            if (doubleval($bytes) >= $mag) {
                return number_format($bytes / $mag, $decimals) . ' ' . $unit;
            }
        }
        return false;
    }

    public function getCommand($args = '', $commandName = 'sysctl', $option = '-n')
    {
        if (false === ($commandPath = $this->findCommand($commandName))) return false;

        if ($command = shell_exec("$commandPath $option $args")) {
            return trim($command);
        }
        return false;
    }

    function findCommand($commandName)
    {
        $paths = ['/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin'];

        foreach ($paths as $path) {
            if (is_executable("$path/$commandName")) return "$path/$commandName";
        }
        return false;
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
}