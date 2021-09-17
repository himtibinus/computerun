<div class="container-{{ $level }}">
    @if(isset($children))
        @foreach($children as $widget)
            @component('functions.parse-widget', ['widget' => $widget])
            @endcomponent
        @endforeach
    @endif
</div>
