<link rel="stylesheet" href="{{asset('/css/waitMe.min.css')}}">
<div class="box box-default" id="server-info">
    <div class="box-header with-border">
        <h3 class="box-title">服务器信息</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td width="120px">服务器当前时间</td>
                    <td><span id="currentTime"></span></td>
                    <td>服务器已运行时间</td>
                    <td><span id="uptime"></span></td>
                </tr>
                <tr>
                    <td>CPU型号 <span id="cpu-core" class="red"></span></td>
                    <td colspan="3" id="cpu-model"></td>
                </tr>
                <tr>
                    <td>CPU使用状况</td>
                    <td colspan="3">
                        @include('admin.dashboard.cpustatus')
                    </td>
                </tr>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>

@include('admin.plugin.modelinfo')

<script src="{{asset('/js/waitMe.min.js')}}"></script>
<script src="{{asset('/js/echarts.min.js')}}"></script>
<script src="{{asset('/js/dashboard/cpu.js')}}"></script>
<script>
    $(function(){
        function run_waitMe(){
            $('#server-info').waitMe({
                effect: 'roundBounce',
                text: 'Please wait...',
                bg: 'rgba(255,255,255,0.7)',
                color:'#000',
                sizeW:'',
                sizeH:'',
                source: 'img.svg'
            });
        }
        run_waitMe()
    })
</script>