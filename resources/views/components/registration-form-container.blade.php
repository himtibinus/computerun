@if(Auth::guest())
    @if(isset($children_guest))
        @foreach($children_guest as $widget)
            @component('functions.parse-widget', ['widget' => $widget])
            @endcomponent
        @endforeach
    @endif
@else
    @if(isset($children))
        @foreach($children as $widget)
            @component('functions.parse-widget', ['widget' => $widget])
            @endcomponent
        @endforeach
    @endif
@endif
