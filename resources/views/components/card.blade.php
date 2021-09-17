<div class="card my-3">
    @if(isset($banner) && is_array($banner))
        <?php
            if(!isset($banner['class'])) $banner['class'] = "";
            $banner['class'] = "card-img-top " . $banner['class'];
        ?>
        <div class="{{ $banner['class'] ?? '' }} p-3 text-center" style="background: {{ $banner['background'] ?? 'none' }}; color: {{ $banner['foreground'] ?? '#000000' }};">
            <p class="h4 m-0 fw-bold">{{ $banner['text'] ?? '' }}</p>
        </div>
    @endif
    @if(isset($cover_image) && is_array($cover_image))
        <?php
            if(!isset($cover_image['class'])) $cover_image['class'] = "";
            if(!isset($banner) || !is_array($banner)) $cover_image['class'] = "card-img-top " . $cover_image['class'];
        ?>
        @component('functions.parse-widget', ['widget' => $cover_image])
        @endcomponent
    @endif
    <div class="card-body">
        @if(isset($title))
            <h5 class="card-title">{{ $title }}</h5>
        @endif
        @if(isset($body))
            @component('functions.parse-widget', ['widget' => $body])
            @endcomponent
        @endif
    </div>
</div>
