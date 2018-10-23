<iframe id="horizon-iframe" name="horizon-iframe" src="{{config('app.url') . '/horizon'}}" width="100%" frameborder="0"></iframe>
<script>
    var dHeight = window.screen.height
    // console.log(dHeight)
    document.getElementById('horizon-iframe').style.height = dHeight + 'px'
</script>