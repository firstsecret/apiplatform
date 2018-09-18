<h2>页面未找到</h2>

@php

    echo 'uri:' . request()->getRequestUri();
    echo '<br>';
    echo 'method:' . request()->getMethod();

@endphp