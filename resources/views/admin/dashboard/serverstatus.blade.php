<tr>
    <td width="20%">服务器当前时间</td>
    <td width="30%"><span id="currentTime"></span></td>
    <td width="20%">服务器已运行时间</td>
    <td width="30%"><span id="uptime"></span></td>
</tr>
<tr>
    <td>CPU型号 <span class="red">[<?php echo $svrInfo['cpu']['cores'];?>]</span></td>
    <td colspan="3"><?php echo $svrInfo['cpu']['model'];?></td>
</tr>
<tr>
    <td>CPU使用状况</td>
    <td colspan="3">
        <div id="cpustatus" style="width: 100%; height: 300px;"></div>
    </td>
</tr>