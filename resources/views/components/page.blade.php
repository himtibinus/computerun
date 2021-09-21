<?php
    $title = (strlen($title) > 0) ? $title : 'Untitled Page';
    $kicker = (strlen($kicker) > 0) ? $kicker : null;
?>
<!DOCTYPE html>
<html>
    <head>
        @component('components.meta', ['title' => $title])
        @endcomponent
    </head>
    <body class="is-bootstrap" style="overflow-x: hidden">
        @if(isset($children))
            @foreach($children as $widget)
                @component('functions.parse-widget', ['widget' => $widget])
                @endcomponent
            @endforeach
        @endif
        <div class="container-0 pb-0">
            @component('components.sponsors')
            @endcomponent
            <img class="container-clip for-footer" src="/img/backgrounds/7.png">
            @component('components.imagecompat', ['src' => '/img/accessories/2021/wave-end-1.svg', 'class' => 'w-100 p-0 mt-5 mb-0'])
            @endcomponent
        </div>
        @component('components.footer')
        @endcomponent
    </body>
</html>
