<button id="{{$config['btn_id']}}" type="button"
        class="btn btn-{{$config['btn_class'] ?? 'success'}}  btn-{{$config['btn_size'] ?? 'sm'}}"
        data-href="{{$config['btn_href'] ?? ''}}"
        data-toggle="button"
        aria-pressed="false"
        autocomplete="off" type="{{$config['btn_type'] ?? 'button'}}">
    {{$config['btn_msg'] ?? '确认'}}
</button>

</button>

<script>
    $('#{{$config['btn_id']}}').click(function () {
        {{$config['callback'] ?? ''}}
    });
</script>