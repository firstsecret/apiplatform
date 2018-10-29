<div id="main" style="width: 100%;height:200px;"></div>
<div style="width: 100%;text-align: center;">
    <div class="stxt">
        <span id="isShowsBoolBase" style="display: none;">
            物理内存：共 <span id="MemoryTotal" class="red"></span>,
            已用 <span id="MemoryUsed" class="red"></span>,
            空闲 <span id="MemoryFree" class="red"></span> -
        </span>

        <span id="isShowsBoolC" style="display: none">
            Cache： <span id="MemoryCached" class="red"></span> |
            Buffers缓冲为 <span id="Buffers" class="red"></span>
        </span>
    </div>

    <div class="stxt">
        <span id="isShowsBoolR" style="display: none;">
            真实内存使用 <span id="MemoryRealUsed" class="red"></span> ,
            真实内存空闲 <span id="MemoryRealFree" class="red"></span> -
        </span>

        <span id="isShowsBool" style="display: none;">
            SWAP区：共 <span id="SwapTotal" class="red"></span> ,
            已使用 <span id="SwapUsed" class="red"></span> ,
            空闲 <span id="SwapFree" class="red"></span>
        </span>
    </div>
</div>
<script src="{{asset('/js/dashboard/memory.js')}}"></script>