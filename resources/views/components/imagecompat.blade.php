<?php
    $ext = strtolower(substr(strrchr($src, '.'), 1));
    $mime_type = 'image/' . $ext;
    $src_base = preg_replace('/\.' . $ext . '$/i', '', $src);
?>
<picture id="{{$id_container ?? ''}}" class="{{$class_container ?? ''}}">
    <source srcset="{{$src_base}}.webp" type="image/webp">
    @if($ext == 'jpe' || $ext == 'jpeg' || $ext == 'jpg')
        <?php $mime_type = 'image/jpeg' ?>
        <source srcset="{{$src_base}}.jp2" type="image/jp2">
    @endif
    <source srcset="{{$src}}" type="{{$mime_type}}">
    <img id="{{$id ?? ''}}" class="{{$class ?? ''}}" src="{{$src}}" alt="{{$alt ?? ''}}" type="{{$mime_type}}">
</picture>
