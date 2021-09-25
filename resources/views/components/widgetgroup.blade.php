<?php if (isset($tag)) echo '<' . $tag . ' class="' . (isset($class) ? $class : '') . '" id="' . (isset($id) ? $id : '') . '">'; ?>
@foreach($children as $widget)
    @component('functions.parse-widget', ['widget' => $widget])
    @endcomponent
@endforeach
<?php if (isset($tag)) echo '</' . $tag . '>'; ?>