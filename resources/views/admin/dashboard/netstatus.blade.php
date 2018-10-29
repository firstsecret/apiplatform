<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">网络情况</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped" id="net-status">
                <tr>
                    <td class="text-center" width="15%">设备</td>
                    <td class="text-center" width="35%">接收</td>
                    <td class="text-center" width="35%">发送</td>
                    <td class="text-center" width="15%">错误/丢失</td>
                </tr>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>
<script src="{{asset('/js/dashboard/net.js')}}"></script>