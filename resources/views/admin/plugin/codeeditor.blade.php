<link rel="stylesheet" href=""{{asset('/packages/codemirror/doc/docs.css')}}>
<link rel="stylesheet" href="{{asset('/packages/codemirror/lib/codemirror.css')}}">
{{--<script src="{{asset('/packages/codemirror/src/codemirror.js')}}"></script>--}}
<form>
    <textarea id="code" name="code" style="width:80%;height: 800px;">
        {{$content}}
    </textarea>
</form>

<script>
    // var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    //
    // });
</script>