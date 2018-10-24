{{--<link rel="stylesheet" href=""{{asset('/packages/codemirror/doc/docs.css')}}>--}}
{{--<link rel="stylesheet" href="{{asset('/packages/codemirror/lib/codemirror.css')}}">--}}
{{--<script src="{{asset('/packages/codemirror/src/codemirror.js')}}"></script>--}}
{{--<form>--}}
    <textarea id="code-conf" name="code-conf" style="width:80%;height: 800px;">
        {{$content}}
    </textarea>
{{--</form>--}}

{{--<button id="{{$config['btn_id']}}" type="button"--}}
        {{--class="btn btn-{{$config['btn_class'] ?? 'success'}}  btn-{{$config['btn_size'] ?? 'sm'}}"--}}
        {{--data-href="{{$config['btn_href'] ?? ''}}"--}}
        {{--data-toggle="button"--}}
        {{--aria-pressed="false"--}}
        {{--autocomplete="off" type="{{$config['btn_type'] ?? 'button'}}">--}}
    {{--{{$config['btn_msg'] ?? '确认'}}--}}
{{--</button>--}}

{{--<script>--}}
    {{--$('#{{$config['btn_id']}}').click(function () {--}}
        {{--var url = '{{$config['btn_url'] ?? ''}}';--}}

        {{--if (!url) {--}}
            {{--return false--}}
        {{--}--}}

        {{--swal({--}}
            {{--title: "确认修改吗",--}}
            {{--type: "warning",--}}
            {{--showCancelButton: true,--}}
            {{--confirmButtonColor: "#DD6B55",--}}
            {{--confirmButtonText: "确认",--}}
            {{--showLoaderOnConfirm: true,--}}
            {{--closeOnConfirm: false,--}}
            {{--cancelButtonText: "取消",--}}
            {{--preConfirm: function () {--}}
                {{--// console.log($('#code-conf').text())--}}
                {{--// console.log(document.getElementById('code-conf').value);--}}
                {{--return new Promise(function (resolve) {--}}
                    {{--$.ajax({--}}
                        {{--method: 'put',--}}
                        {{--url: url,--}}
                        {{--data: {--}}
                            {{--_token: LA.token,--}}
                            {{--file_data: document.getElementById('code-conf').value--}}
                        {{--},--}}
                        {{--success: function (data) {--}}
                            {{--// $.pjax.reload('#pjax-container');--}}
                            {{--//--}}
                            {{--// resolve(data);--}}
                        {{--}--}}
                    {{--});--}}

                {{--});--}}
            {{--}--}}
        {{--}).then(function (result) {--}}
            {{--var data = result.value;--}}
            {{--if (typeof data === 'object') {--}}
                {{--if (data.status) {--}}
                    {{--swal(data.message, '', 'success');--}}
                {{--} else {--}}
                    {{--swal(data.message, '', 'error');--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}

        {{--return false--}}
    {{--});--}}
{{--</script>--}}