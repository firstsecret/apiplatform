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
                    websocket.send(JSON.stringify({"act":"mm"})) 
                    startInterval = setInterval(function(){
                      if (!$.isEmptyObject(nowCpuStatus)) {getCpuStatus(); clearInterval(startInterval)}
                    },1000)
                };
                
                websocket.onclose = function (evt) {
                    console.log("Disconnected");
                };
                
                websocket.onmessage = function (evt) {
                    var respData = JSON.parse(evt.data)
                    if(respData.act == 'rt'){
                        nowCpuStatus = respData.data
                    }else if(respData.act == 'mm') {
                        memoryStatus = respData.data
                        // init memory setting
                        initMemory()
                    }
                };
                
                websocket.onerror = function (evt, e) {
                    console.log(e)
                    console.log('Error occured: ' + evt.data);
                };

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
                    cpuChart.setOption(option);
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
                    
                    // memory
                    mTimeTicket = setInterval(function(){
//                        cpuPercent = nowCpuStatus
                    },1500);
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
               console.log('init memory')
               initMinterval = setInterval(function(){
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
                       clearInterval(initMinterval)  

                       getMemory()
                   }
               },1000)
          }  
EOT;

    }

    function getMemoryScript()
    {
        return <<<EOT

          function getMemory()
                {
                    console.log('begin to get getMemory!')
                    console.log(memoryStatus)
                  
                    var myChart = echarts.init(document.getElementById('main'));
                    var memory_type = ['物理内存', 'Cache', '真实内存', 'SWAP'];
                    var is_memory = [memoryStatus.mBool, memoryStatus.cBool, memoryStatus.rBool, memoryStatus.sBool];
                    var percent = ['MemoryPercent', 'MemoryCachedPercent', 'MemoryRealPercent', 'SwapPercent'];
                    var options = [];
                    var centers = 15;
                    for(var i=0;i<memory_type.length;i++)
                    {
                        if(is_memory[i]){
                            console.log('begin load memory options!')
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
}