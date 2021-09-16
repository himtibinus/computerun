<?php $slug = str_replace(' ', '-', strtolower($name)); ?>
<div class="d-flex flex-column col mr-2 text-center">
    <a href="{{$href}}" target="_blank" class="discreet">
        @component('components.imagecompat', ['src' => '/img/icons/2021/' . $slug . '.png', 'class' => 'footer-img img-fluid mx-auto mb-3', 'alt' => $name])@endcomponent
        <p>{{ $handle ?? '@computerun' }}</p>
    </a>
</div>
