<div class="about-container margin-2 after-container-clip">
    @if(isset($children))
        @foreach($children as $widget1)
            <div class="py-4 pe-4">
                <h2 class="partial-underline custom">{{ $widget1['title'] }}
                    <div class="guide">{{ explode(" ", $widget1['title'], 2)[0] }}</div>
                </h2>
            </div>
            <div class="py-4">
                @foreach($widget1['children'] as $widget2)
                    @component('functions.parse-widget', ['widget' => $widget2])
                    @endcomponent
                @endforeach
            </div>
        @endforeach
    @endif
</div>
