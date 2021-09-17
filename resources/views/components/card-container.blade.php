<?php
    if(!isset($items_per_row) || !is_int($items_per_row) || $items_per_row < 1 || $items_per_row > 4) $items_per_row = 3;
    $col_width = 12 / $items_per_row;
?>
<div class="row {{ $class_container ?? '' }}">
    @foreach($children as $widget)
        <div class="col-12 col-sm-{{$col_width}} {{ $class ?? '' }}">
            @component('functions.parse-widget', ['widget' => $widget])
            @endcomponent
        </div>
    @endforeach
</div>
