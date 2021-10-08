<div class="margin-2 text-center content-divider pt-3 pb-4" id="{{ $id ?? '' }}">
    @if(isset($kicker))<span class="h4 font-700">{{ $kicker }}</span><br>@endif
    <p class="h1 font-800 gradient-text font-airstrike my-large">{{ $title }}</p>
</div>
@if(isset($show_down_arrow) && $show_down_arrow == true)
    <div class="row py-4">
        <div class="col-md-2 mx-auto text-center">
            @component("components.imagecompat", ["src" => "/img/accessories/2021/chevron-down.png", "class" => "img-fluid mx-auto"])
            @endcomponent
        </div>
    </div>
@endif
