<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/18
 * Time: 16:01
 */

error_reporting(0);
$title = "PHPProbe探针 ";
$name = "PHPProbe探针 ";
$downUrl = "https://github.com/fbcha/phpprobe";
$version = "v1.4.1";
$is_constantly = true; // 是否开启实时信息, false - 关闭, true - 开启
date_default_timezone_set("Asia/Shanghai");
if (filter_input(INPUT_GET, 'act') == 'phpinfo') {
    phpinfo();
    exit();
}
$getServerHosts = get_current_user() . '/' . filter_input(INPUT_SERVER, 'SERVER_NAME') . '(' . gethostbyname(filter_input(INPUT_SERVER, 'SERVER_NAME')) . ')'; // 获取服务器域名/ip
$getServerOS = PHP_OS . ' ' . php_uname('r'); // 获取服务器操作系统
$getServerSoftWare = filter_input(INPUT_SERVER, 'SERVER_SOFTWARE'); // 获取服务器类型和版本
$getServerLang = getenv("HTTP_ACCEPT_LANGUAGE"); // 获取服务器语言
$getServerPort = filter_input(INPUT_SERVER, 'SERVER_PORT'); // 获取服务器端口
$getServerHostName = php_uname('n'); // 获取服务器主机名
$getServerAdminMail = filter_input(INPUT_SERVER, 'SERVER_ADMIN'); // 获取服务器管理员邮箱
$getServerTzPath = __FILE__; // 获取探针路径
// 检查true or false
function checkstatus($status)
{
    if (false == $status) {
        $out = '<i class="sui-icon icon-pc-error sui-text-danger"></i>';
    } else {
        $out = '<i class="sui-icon icon-pc-right sui-text-success"></i>';
    }
    return $out;
}

// 判断php参数
function isinit($var)
{
    switch ($var) {
        case 'version':
            $out = PHP_VERSION;
            break;
        case 'sapi':
            $out = php_sapi_name();
            break;
        case 'cookie':
            $out = checkstatus(isset($_COOKIE));
            break;
        case 'issmtp':
            $out = checkstatus(get_cfg_var("SMTP"));
            break;
        case 'SMTP':
            $out = get_cfg_var("SMTP");
            break;
        default:
            $out = getini($var);
            break;
    }
    return $out;
}

// 获取php参数信息
function getini($var)
{
    $conf = get_cfg_var($var);
    switch ($conf) {
        case 0:
            $out = checkstatus(0);
            break;
        case 1:
            $out = checkstatus(1);
            break;
        default :
            $out = $conf;
            break;
    }
    return $out;
}

// 检测函数支持
function isfunction($funname = '')
{
    if (!checkFunction($funname)) return "函数错误！";
    return checkstatus(function_exists($funname));
}

// 检测函数规范
function checkFunction($funname = '')
{
    return ($funname == '') ? false : true;
}

// 禁用的函数
function disableFunction()
{
    $fun = get_cfg_var("disable_functions");
    if (empty($fun)) {
        $out = checkstatus($fun);
    } else {
        $funs = explode(',', $fun);
        $tag = '<ul class="sui-tag ext-tag-font">';
        foreach ($funs as $k => $v) {
            $tag .= '<li>' . $v . '</li>';
        }
        $out = $tag . '</ul>';
    }
    return $out;
}

// php扩展
function isExt($ext)
{
    if ($ext == 'gd_info') {
        $is_gd = extension_loaded("gd");
        if ($is_gd) {
            $gd = gd_info();
            $out = $gd["GD Version"];
        } else {
            $out = checkstatus($is_gd);
        }
    } else if ($ext == 'sqlite3') {
        $is_sqlite3 = extension_loaded("sqlite3");
        if ($is_sqlite3) {
            $sqlite3 = SQLite3::version();
            $out = $sqlite3['versionString'];
        } else {
            $out = checkstatus($is_sqlite3);
        }
    }
    return $out;
}

// php已编译模块
function loadExt()
{
    $exts = get_loaded_extensions();
    if ($exts) {
        $tag = '<ul class="sui-tag ext-tag-font">';
        foreach ($exts as $k => $v) {
            $tag .= '<li>' . $v . '</li>';
        }
        $out = $tag . '</ul>';
    } else {
        $out = checkstatus($exts);
    }
    return $out;
}

// 判断操作系统平台
switch (PHP_OS) {
    case "Linux":
        $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = svr_linux())) ? "show" : "none") : "none";
        $svrInfo = array_merge($svrInfo, linux_Network());
        break;
    case "FreeBSD":
        $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = svr_freebsd())) ? "show" : "none") : "none";
        $svrInfo = array_merge($svrInfo, freebsd_Network());
        break;
    case "Darwin":
        $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = svr_darwin())) ? "show" : "none") : "none";
        $svrInfo = array_merge($svrInfo, darwin_Network());
        break;
    case "WINNT":
        $is_constantly = false;
        $svrShow = (false !== $is_constantly) ? ((false !== ($svrInfo = svr_winnt())) ? "show" : "none") : "none";
        break;
    default :
        break;
}
function getCpuInfo()
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

// linux
function svr_linux()
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

    $res['mTotal'] = size_format($mtotal, 1);
    $res['mFree'] = size_format($mfree, 1);
    $res['mBuffers'] = size_format($mbuffers, 1);
    $res['mCached'] = size_format($mcached, 1);
    $res['mUsed'] = size_format($mtotal - $mfree, 1);
    $res['mPercent'] = (floatval($mtotal) != 0) ? round($mused / $mtotal * 100, 1) : 0;
    $res['mRealUsed'] = size_format($mrealused, 1);
    $res['mRealFree'] = size_format($mtotal - $mrealused, 1); //真实空闲
    $res['mRealPercent'] = (floatval($mtotal) != 0) ? round($mrealused / $mtotal * 100, 1) : 0; //真实内存使用率
    $res['mCachedPercent'] = (floatval($mcached) != 0) ? round($mcached / $mtotal * 100, 1) : 0; //Cached内存使用率
    $res['swapTotal'] = size_format($stotal, 1);
    $res['swapFree'] = size_format($sfree, 1);
    $res['swapUsed'] = size_format($sused, 1);
    $res['swapPercent'] = (floatval($stotal) != 0) ? round($sused / $stotal * 100, 1) : 0;

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
    $percent = round($time / $total, 4);
    $percent = $percent * 100;
    $res['cpu']['percent'] = $percent;
    return $res;
}

// freebsd
function svr_freebsd()
{
    // 获取cpu信息
    if (false === ($res['cpu']['core'] = getCommand("kern.smp.cpus"))) return false;

    $res['cpu']['cores'] = $res['cpu']['core'] . '核';
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
    if ($days !== 0) $res['uptime'] = $days . "天";
    if ($hours !== 0) $res['uptime'] .= $hours . "小时";
    $res['uptime'] .= $min . "分钟";

    // 获取内存信息
    if (false === ($mTatol = getCommand("hw.physmem"))) return false;
    $pagesize = getCommand("hw.pagesize");
    $vmstat = getCommand("", "vmstat", "");
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

    $res['mTotal'] = size_format($mTatol, 1);
    $res['mFree'] = size_format($mFree, 1);
    $res['mCached'] = size_format($mCached, 1);
    $res['mUsed'] = size_format($mUsed, 1);
    $res['mBuffers'] = size_format($mBuffers, 1);
    $res['mPercent'] = (floatval($mTatol) != 0) ? round($mUsed / $mTatol * 100, 1) : 0;
    $res['mCachedPercent'] = (floatval($mCached) != 0) ? round($mCached / $mTatol * 100, 1) : 0; //Cached内存使用率
    $res['swapTotal'] = size_format($sTotal, 1);
    $res['swapFree'] = size_format($sFree, 1);
    $res['swapUsed'] = size_format($sUsed, 1);
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

// Darwin
function svr_darwin()
{
    // 获取CPU信息
    if (false === ($res['cpu']['core'] = getCommand("machdep.cpu.core_count"))) return false;

    $res['cpu']['processor'] = getCommand("machdep.cpu.thread_count");
    $res['cpu']['cores'] = $res['cpu']['core'] . '核' . (($res['cpu']['processor']) ? '/' . $res['cpu']['processor'] . '线程' : '');
    $model = getCommand("machdep.cpu.brand_string");
    $cache = getCommand("machdep.cpu.cache.size") * $res['cpu']['core'];
    $cache = size_format($cache * 1024, 0);
    $res['cpu']['model'] = $model . ' [二级缓存：' . $cache . ']';

    // 获取服务器运行时间
    $uptime = getCommand("kern.boottime");
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
    if (false === ($mTatol = getCommand("hw.memsize"))) return false;
    $vmstat = getCommand("", 'vm_stat', '');
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

    $res['mTotal'] = size_format($mTatol, 1);
    $res['mFree'] = size_format($mFree, 1);
    $res['mBuffers'] = size_format($mBuffer, 1);
    $res['mCached'] = size_format($mCached, 1);
    $res['mUsed'] = size_format($mUsed, 1);
    $res['mPercent'] = (floatval($mTatol) != 0) ? round($mUsed / $mTatol * 100, 1) : 0;
    $res['mCachedPercent'] = (floatval($mCached) != 0) ? round($mCached / $mTatol * 100, 1) : 0; //Cached内存使用率

    $swapInfo = getCommand("vm.swapusage");
    $swap1 = preg_split('/M/', $swapInfo);
    $swap2 = preg_split('/=/', $swap1[0]);
    $swap3 = preg_split('/=/', $swap1[1]);
    $swap4 = preg_split('/=/', $swap1[2]);

    $sTotal = $swap2[1] * 1024 * 1024;
    $sUsed = $swap3[1] * 1024 * 1024;
    $sFree = $swap4[1] * 1024 * 1024;

    $res['swapTotal'] = size_format($sTotal, 1);
    $res['swapFree'] = size_format($sFree, 1);
    $res['swapUsed'] = size_format($sUsed, 1);
    $res['swapPercent'] = (floatval($sTotal) != 0) ? round($sUsed / $sTotal * 100, 1) : 0;

    $res['mBool'] = true;
    $res['cBool'] = true;
    $res['rBool'] = false;
    $res['sBool'] = true;

    // CPU状态
    $cpustat = getCommand(1, 'sar', '');
    preg_match_all("/Average\s{0,}\:+\s+\w+\s+\w+\s+\w+\s+\w+/s", $cpustat, $_cpu);
    $_cpu = preg_split("/\s+/", $_cpu[0][0]);
    $percent = $_cpu[1] + $_cpu[2] + $_cpu[3];
    $res['cpu']['percent'] = $percent;

    return $res;
}

function getCommand($args = '', $commandName = 'sysctl', $option = '-n')
{
    if (false === ($commandPath = findCommand($commandName))) return false;

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

// winnt
function svr_winnt()
{
    // 获取CPU信息
    if (get_cfg_var("com.allow_dcom")) {
        $wmi = new COM('winmgmts:{impersonationLevel=impersonate}');
        $cpuinfo = getWMI($wmi, "Win32_Processor", "Name,LoadPercentage,NumberOfCores,NumberOfLogicalProcessors,L2CacheSize");
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
        $sysinfo = getWMI($wmi, "Win32_OperatingSystem", "LastBootUpTime,TotalVisibleMemorySize,FreePhysicalMemory");
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
        $swapinfo = getWMI($wmi, "Win32_PageFileUsage", 'AllocatedBaseSize,CurrentUsage');
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
    $res['swapTotal'] = size_format($sTotal, 1);
    $res['swapUsed'] = size_format($sUsed, 1);
    $res['swapFree'] = size_format($sFree, 1);
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

// 网络流量
// linux
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
            $net[$dev_name]['rxbytes'] = netSize($stats[0]);
            $net[$dev_name]['txbytes'] = netSize($stats[8]);
            $net[$dev_name]['rxspeed'] = $stats[0];
            $net[$dev_name]['txspeed'] = $stats[8];
            $net[$dev_name]['errors'] = $stats[2] + $stats[10];
            $net[$dev_name]['drops'] = $stats[3] + $stats[11];
        }
    }
    $res['net'] = $net;
    return $res;
}

// darwin
function darwin_Network()
{
    $netstat = getCommand("-nbdi | cut -c1-24,42- | grep Link", "netstat");
    $res['nBool'] = $netstat ? true : false;
    $nets = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
    $_net = [];
    foreach ($nets as $net) {
        $buf = preg_split("/\s+/", $net, 10);
        if (!empty($buf[0])) {
            $dev_name = trim($buf[0]);
            $_net[$dev_name]['name'] = $dev_name;
            $_net[$dev_name]['rxbytes'] = netSize($buf[5]);
            $_net[$dev_name]['txbytes'] = netSize($buf[8]);
            $_net[$dev_name]['rxspeed'] = $buf[5];
            $_net[$dev_name]['txspeed'] = $buf[8];
            $_net[$dev_name]['errors'] = $buf[4] + $buf[7];
            $_net[$dev_name]['drops'] = isset($buf[10]) ? $buf[10] : "NULL";
        }
    }
    $res['net'] = $_net;
    return $res;
}

// freebsd
function freebsd_Network()
{
    $netstat = getCommand("-nibd", "netstat");
    $res['nBool'] = $netstat ? true : false;
    $nets = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
    $_net = [];
    foreach ($nets as $net) {
        $buf = preg_split("/\s+/", $net);
        if (!empty($buf[0])) {
            if (preg_match('/^<Link/i', $buf[2])) {
                $dev_name = trim($buf[0]);
                $_net[$dev_name]['name'] = $dev_name;
                if (strlen($buf[3]) < 17) {
                    if (isset($buf[11]) && (trim($buf[11]) != '')) {
                        $_net[$dev_name]['rxbytes'] = netSize($buf[6]);
                        $_net[$dev_name]['txbytes'] = netSize($buf[9]);
                        $_net[$dev_name]['rxspeed'] = $buf[6];
                        $_net[$dev_name]['txspeed'] = $buf[9];
                        $_net[$dev_name]['errors'] = $buf[4] + $buf[8];
                        $_net[$dev_name]['drops'] = $buf[11] + $buf[5];
                    } else {
                        $_net[$dev_name]['rxbytes'] = netSize($buf[5]);
                        $_net[$dev_name]['txbytes'] = netSize($buf[8]);
                        $_net[$dev_name]['rxspeed'] = $buf[5];
                        $_net[$dev_name]['txspeed'] = $buf[8];
                        $_net[$dev_name]['errors'] = $buf[4] + $buf[7];
                        $_net[$dev_name]['drops'] = $buf[10];
                    }
                } else {
                    if (isset($buf[12]) && (trim($buf[12]) != '')) {
                        $_net[$dev_name]['rxbytes'] = netSize($buf[7]);
                        $_net[$dev_name]['txbytes'] = netSize($buf[10]);
                        $_net[$dev_name]['rxspeed'] = $buf[7];
                        $_net[$dev_name]['txspeed'] = $buf[10];
                        $_net[$dev_name]['errors'] = $buf[5] + $buf[9];
                        $_net[$dev_name]['drops'] = $buf[12] + $buf[6];
                    } else {
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

// 服务器测试
$server_testinfo = array(
    'fbcha' => array(
        'name' => '作者电脑',
        'url' => '',
        'logo' => '',
        'intData' => '0.108秒',
        'floatData' => '0.328秒',
        'ioData' => '0.016秒',
        'cpuData' => 'Core(TM) i3-3220 CPU @ 3.30GHz x 2'
    )
);
function getTest($val)
{
    $out = '';
    if ($val === 'intData') {
        $timeStart = gettimeofday();
        for ($i = 0; $i < 3000000; $i++) {
            $t = 1 + 1;
        }
        $timeEnd = gettimeofday();
        $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
        $out = round($time, 3) . "秒";
    } else if ($val === 'floatData') {
        $t = pi();
        $timeStart = gettimeofday();
        for ($i = 0; $i < 3000000; $i++) {
            sqrt($t);
        }
        $timeEnd = gettimeofday();
        $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
        $out = round($time, 3) . "秒";
    } else if ($val === 'ioData') {
        $fp = fopen(PHPPROBE, 'r');
        $timeStart = gettimeofday();
        for ($i = 0; $i < 10000; $i++) {
            fread($fp, 10240);
            rewind($fp);
        }
        $timeEnd = gettimeofday();
        fclose($fp);
        $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
        $out = round($time, 3) . "秒";
    } else {
        $out = "参数错误!";
    }
    return $out;
}

function getSvrTestUrl($val)
{
    $out = $logo = $name = '';
    $val['logo'] && $logo = '<img class="svr-logo" src="' . $val['logo'] . '" />';
    $name = $val['logo'] ? '<div class="svr-logo-text">' . $val['name'] . '</div>' : $val['name'];
    if ($val['url']) {
        $out = '<a href="' . $val['url'] . '"  target="_blank">' . $logo . $name . '</a>';
    } else {
        $out = $logo . $name;
    }
    return $out;
}

if (filter_input(INPUT_GET, 'act') == 'st') {
    $sts = array(
        'intData' => getTest('intData'),
        'floatData' => getTest('floatData'),
        'ioData' => getTest('ioData')
    );
    $stJsonRes = json_encode($sts);
    echo filter_input(INPUT_GET, 'callback') . '(' . $stJsonRes . ')';
    exit();
}
if (filter_input(INPUT_GET, 'act') == 'test') {
    $posts = filter_input_array(INPUT_POST);
    if ($posts['type'] == 'mysql') {
        $link = mysql_connect($posts['host'] . ":" . $posts['port'], $posts['user'], $posts['pwd']);
        echo $link ? checkstatus(true) : checkstatus(false);
        mysqli_close($link);
    } else if ($posts['type'] == 'fun') {
        echo $posts['funname'] ? isfunction($posts['funname']) : '<span class="stxt red">请输入函数名</span>';
    } else {
        echo false;
    }
    exit();
}
if ($is_constantly) {
    $currentTime = date("Y-m-d H:i:s");
    $uptime = $svrInfo['uptime'];
}
// hdd
$hddTotal = disk_total_space(".");
$hddFree = disk_free_space(".");
$hddUsed = $hddTotal - $hddFree;
$hddPercent = (floatval($hddTotal) != 0) ? round($hddUsed / $hddTotal * 100, 2) : 0;
if (filter_input(INPUT_GET, 'act') == 'rt' && $is_constantly) {
    $res = array(
        'currentTime' => $currentTime,
        'uptime' => $uptime,
        'cpuPercent' => $svrInfo['cpu']['percent'],
        'MemoryUsed' => $svrInfo['mUsed'],
        'MemoryFree' => $svrInfo['mFree'],
        'MemoryPercent' => $svrInfo['mPercent'],
        'MemoryCachedPercent' => $svrInfo['mCachedPercent'],
        'MemoryCached' => $svrInfo['mCached'],
        'MemoryRealUsed' => $svrInfo['mRealUsed'],
        'MemoryRealFree' => $svrInfo['mRealFree'],
        'MemoryRealPercent' => $svrInfo['mRealPercent'],
        'Buffers' => $svrInfo['mBuffers'],
        'SwapFree' => $svrInfo['swapFree'],
        'SwapUsed' => $svrInfo['swapUsed'],
        'SwapPercent' => $svrInfo['swapPercent']
    );
    $jsonRes = json_encode($res);
    echo filter_input(INPUT_GET, 'callback') . '(' . $jsonRes . ')';
    exit();
}
if (filter_input(INPUT_GET, 'act') == 'ort' && $svrInfo['nBool']) {
    $oRes = array(
        'Network' => $svrInfo['net']
    );
    $ortRes = json_encode($oRes);
    echo filter_input(INPUT_GET, 'callback') . '(' . $ortRes . ')';
    exit();
}
