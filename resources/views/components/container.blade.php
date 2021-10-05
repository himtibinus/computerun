<div class="container-{{ $level }} {{ isset($show_decorations) ? 'position-relative' : '' }} {{ $class ?? '' }}" id="{{ $id ?? '' }}">
    @if(isset($show_decorations) && is_array($show_decorations) && count($show_decorations) > 0)
        @foreach ($show_decorations as $decor)
            @component('components.imagecompat', ['src' => '/img/accessories/2021/' . $decor . '.png', 'class' => $decor, 'has_alternative_formats' => true])
            @endcomponent
        @endforeach
    @endif
    @if(isset($children))
        @foreach($children as $widget)
            @component('functions.parse-widget', ['widget' => $widget])
            @endcomponent
        @endforeach
    @endif
</div>
