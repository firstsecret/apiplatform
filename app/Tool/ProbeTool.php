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
use App\Tool\server\ServerTool;
use App\Tool\server\WinntTool;

//use App\Tool\server\ServerTool;

trait ProbeTool
{
    use ServerTool;
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

        return ['svrShow' => $svrShow, 'svrInfo' => $svrInfo];
    }

    public function hddstatus($svrInfo)
    {
        $hddTotal = disk_total_space(".");
        $hddFree = disk_free_space(".");
        $hddUsed = $hddTotal - $hddFree;
        $hddPercent = (floatval($hddTotal) != 0) ? round($hddUsed / $hddTotal * 100, 2) : 0;

        $svrInfo['hddTotal'] = $this->size_format($hddTotal,3);
        $svrInfo['hddFree'] = $hddFree;
        $svrInfo['hddUsed'] = $hddUsed;
        $svrInfo['hddPercent'] = $hddPercent;

        return $svrInfo;
    }

    public function getCpuScript()
    {
        return <<<EOT
                var wsServer = 'ws://47.52.45.228:8555';
                var websocket = new WebSocket(wsServer);
                var nowCpuStatus = {}
                var memoryStatus = {}
                var startInterval
                websocket.onopen = function (evt) {
                    // handle first
                    websocket.send(JSON.stringify({"act":"rt"}))
//                    websocket.send(JSON.stringify({"act":"mm"})) 
                    startInterval = setInterval(function(){
                      if (!$.isEmptyObject(nowCpuStatus)) {
                         
                        getCpuStatus();
                        getNetStatus();
                        getMemory();
                        initNetStatus();
                        initHdd();
                        clearInterval(startInterval);
                      }
                    },1000)
                };
                
                websocket.onclose = function (evt) {
                    console.log("Disconnected");
                };
                
                websocket.onmessage = function (evt) {
                    var respData = JSON.parse(evt.data)
                    nowCpuStatus = respData.data.svrInfo
                    initMemory()
                    memoryStatus = respData.data.svrInfo
                };
                
                websocket.onerror = function (evt, e) {
                    console.log(e)
                    console.log('Error occured: ' + evt.data);
                };
                
                function size_format(bytes, decimals=4)
                {
                    if (bytes === 0) return '0 B';  

                    var k = 1024;  
                    sizes = ['B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];  
                    i = Math.floor(Math.log(bytes) / Math.log(k));

                    return (bytes / Math.pow(k, i)).toPrecision(decimals) + ' ' + sizes[i];
                }

                function getCpuStatus()
                {
                    console.log('cpu status start!')
                    var cpuChart = echarts.init(document.getElementById('cpustatus'));
                    
                    var data = [];
                    var time = [];
                    var _data = [];
                    var cpuPercent = 0;
                    var now = +new Date();

                    function randomData()
                    {
                        now = new Date(+now + 1000);
                        time = (now).getTime();
                        websocket.send(JSON.stringify({"act":"rt"}))
                        cpuPercent = nowCpuStatus.cpuPercent
                        _data = {
                            name: time,
                            value: [
                                time,
                                cpuPercent
                            ]
                        };
                        return _data;
                    }

                    for (var i = 0; i < 60; i++) {
                        data.push(randomData());
                    }

                    var option = {
                        title: {
                            show: true,
                            text: 'CPU使用率',
                            left: 'center'
                        },
                        xAxis: {
                            type : 'time',
                            splitLine: {
                                show: false
                            }
                        },
                        yAxis: {
                            type: 'value',
                            boundaryGap: [0, '100%'],
                            max: 100,
                            splitLine: {
                                show: true
                            },
                            axisLabel: {
                                formatter: '{value} %'
                            }
                        },
                        series: [{
                            name: 'CPU使用率',
                            type: 'line',
                            showSymbol: false,
                            hoverAnimation: false,
                            data: data
                        }]
                    };
                     // echart
                    timeTicket = setInterval(function () {
                          
                        data.shift();
                        data.push(randomData());

                        cpuChart.setOption({
                            series: [{
                                data: data
                            }]
                        });
                    }, 1500);
                    cpuChart.setOption(option);
                   
                 
                }
EOT;
    }

    function initMemoryScript()
    {
        return <<<EOT
          var mBool;
          var cBool;
          var rBool;
          var sBool;
         
          function initMemory()
          {
//               console.log('init memory')
                
               if (!$.isEmptyObject(memoryStatus)){
                   mBool = memoryStatus.mBool
                   cBool = memoryStatus.cBool
                   rBool = memoryStatus.rBool
                   sBool = memoryStatus.sBool
                   
                   $('#MemoryTotal').html(memoryStatus.mTotal)
                   $('#MemoryUsed').html(memoryStatus.mUsed)
                   $('#MemoryFree').html(memoryStatus.mFree)
                   
                   $('#isShowsBoolBase').children().unwrap();
                   
                   if(memoryStatus.sBool > 0) {
                        $("#isShowsBool").children().unwrap();
                        $("#isShowsBoolR").children().unwrap();
                        $("#isShowsBoolC").children().unwrap();

                        $('#SwapTotal').html(memoryStatus.swapTotal)
                        $('#SwapUsed').html(memoryStatus.swapUsed)
                        $('#SwapFree').html(memoryStatus.swapFree)
                        $('#MemoryRealUsed').html(memoryStatus.mRealUsed)
                        $('#MemoryRealFree').html(memoryStatus.mRealFree)
                        $('#MemoryCached').html(memoryStatus.mCached)
                        $('#Buffers').html(memoryStatus.mBuffers)
                   } 
                   
                   
                   initServerMsg()
                  
               }
             
          }  
EOT;
    }

    function getMemoryScript()
    {
        return <<<EOT
          function getMemory()
                {
                    <!--console.log('begin to get getMemory!')-->
                    <!--console.log(memoryStatus)                 -->
                    var myChart = echarts.init(document.getElementById('main'));
                    var memory_type = ['物理内存', 'Cache', '真实内存', 'SWAP'];
                    var is_memory = [memoryStatus.mBool, memoryStatus.cBool, memoryStatus.rBool, memoryStatus.sBool];
                    var percent = ['MemoryPercent', 'MemoryCachedPercent', 'MemoryRealPercent', 'SwapPercent'];
                    var options = [];
                    var centers = 15;
                    for(var i=0;i<memory_type.length;i++)
                    {
                        if(is_memory[i]){
                            <!--console.log('begin load memory options!')-->
                            options[i] = {
                                name: memory_type[i],
                                type: 'gauge',
                                radius: '80%',
                                center: [centers + '%', '50%'],
                                axisLine: {
                                    show: true,
                                    lineStyle: {
                                        width: 10
                                    }
                                },
                                splitLine: {
                                    show: true,
                                    length: '15%'
                                },
                                axisTick: {
                                    show: true,
                                    length: '10%'
                                },
                                axisLabel: {
                                    show: true,
                                    textStyle: {
                                        fontSize: 9
                                    }
                                },
                                detail: {
                                    show: true,
                                    formatter:'{value}%',
                                    offsetCenter: ['0', '65%'],
                                    textStyle: {
                                        fontSize: '14'
                                    }
                                },
                                pointer: {
                                    width: 5
                                },
                                data: [{value: 50, name: memory_type[i]}]
                            };
                            centers = centers + 23;
                        }else{
                            options[i] = "";
                        }
                    }

                    var option = {
                        tooltip : {
                            formatter: "{a} <br/>{b} : {c}%"
                        },
                        series: options
                    };

                    mtimeTicket = setInterval(function () {
                        <!--$.getJSON("?act=rt&callback=?", function (data) {-->
                            <!--for (var i=0; i<percent.length; i++)-->
                            <!--{-->
                                <!--if(data[percent[i]] !== null) option.series[i].data[0].value = data[percent[i]];-->
                            <!--}-->
                        <!--});-->
                        for (var i=0; i<percent.length; i++)
                        {
                            if(nowCpuStatus[percent[i]] !== null && !$.isEmptyObject(option.series[i])) option.series[i].data[0].value = nowCpuStatus[percent[i]];
                        }
                        myChart.setOption(option, true);
                    }, 2000);
                }
EOT;
    }

    function serverScript()
    {
        return <<<EOT
        function initServerMsg()
        {
            $('#currentTime').html(memoryStatus.currentTime)
            $('#uptime').html(memoryStatus.uptime)
            $('#cpu-model').html(memoryStatus.cpu.model)
            $('#cpu-core').html(memoryStatus.cpu.cores)
        }
EOT;

    }

    function netScript()
    {
        return <<<EOT
        function initNetStatus()
        {
            let netNowStatus = memoryStatus.net
            <!--console.log(typeof netNowStatus)-->
            <!--console.log(netNowStatus)-->
            $.each(netNowStatus, function(key, item){
             $('#net-status').append('<tr>' + 
                '<td class="text-center">'+key+'</td>' +
                '<td class="text-center">' +
                    '<span id="'+item.name+'_rxbytes">'+item.rxbytes+'</span>' +
                    '(<span id="'+item.name+'_rxspeed" class="stxt"></span>)' +
                '</td>' +
                '<td class="text-center">' +
                    '<span id="'+item.name+'_txbytes">'+item.txbytes+'</span>' +
                    '(<span id="'+item.name+'_txspeed" class="stxt"></span>)' + 
                '</td>' +
                '<td class="text-center">' +
                    '<span id="'+item.name+'_errors">'+item.errors+'</span>/' +
                    '<span id="'+item.name+'_drops">'+item.drops+'</span>' +
                '</td>' + 
            '</tr>')
            })
        } 

        function ForDight(Dight,How)
        { 
            if (Dight<0){
                var Last=0+"B/s";
            }else if (Dight<1024){
                var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"B/s";
            }else if (Dight<1048576){
                Dight=Dight/1024;
                var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"K/s";
            }else{
                Dight=Dight/1048576;
                var Last=Math.round(Dight*Math.pow(10,How))/Math.pow(10,How)+"M/s";
            }
            return Last; 
        }

        function getNetStatus()
        {
            var inputSpeed = [], outSpeed = [];
            netInterval = setInterval(function (){
                 let netNowStatus = memoryStatus.net
                 <!--console.log(netNowStatus)-->
                 $.each(netNowStatus, function(key, item){
                    $('#' + item.name + '_rxbytes').html(item.rxbytes)
                    $('#' + item.name + '_rxspeed').html(ForDight((item.rxspeed-inputSpeed[item.name]), 3))
                    $('#' + item.name + '_txbytes').html(item.txbytes)
                    $('#' + item.name + '_txspeed').html(ForDight((item.txspeed-outSpeed[item.name]), 3))
                    $('#' + item.name + '_errors').html(item.errors)
                    $('#' + item.name + '_drops').html(item.drops)
                    inputSpeed[item.name] = item.rxspeed;
                    outSpeed[item.name] = item.txspeed;
                 })
            },1000)
        }
EOT;
    }

    public function hddScript()
    {
        return <<<EOT
                function initHdd()
                {
                    var hddChart = echarts.init(document.getElementById('hddstatus'));
                    var hddTotal = nowCpuStatus.hddTotal
                    var hddPercent = nowCpuStatus.hddPercent
                    var hddUsed = nowCpuStatus.hddUsed
                    var hddFree = nowCpuStatus.hddFree
                    var option = {
                        title : {
                            text: '总空间 ' + hddTotal + '， 使用率 ' + hddPercent + '%',
                            right: '10%'
                        },
                        tooltip : {
                            trigger: 'item',
                            formatter: function(data){
                                var seriesName = data.seriesName;
                                var name = data.name;
                                var value = size_format(data.value, 5);
                                var percent = data.percent;
                                return seriesName + '<br />' + name + ': ' + value + ' (' + percent + ' %)';
                            }
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'right',
                            data: ['已用','空闲']
                        },
                        series : [
                            {
                                name: '硬盘使用状况',
                                type: 'pie',
                                radius : '80%',
                                center: ['30%', '50%'],
                                data:[
                                    {value: hddUsed, name:'已用'},
                                    {value: hddFree, name:'空闲'}
                                ],
                                itemStyle: {
                                    emphasis: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };

                    hddChart.setOption(option, true);
                }
EOT;

    }
}