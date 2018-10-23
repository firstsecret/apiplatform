<iframe id="supervisor-iframe" name="horizon-iframe" src="{{config('app.url') . ':9001'}}" width="100%" frameborder="0"></iframe>
<script>
    var dHeight = window.screen.height
    // console.log(dHeight)
    document.getElementById('supervisor-iframe').style.height = dHeight + 'px'
</script>