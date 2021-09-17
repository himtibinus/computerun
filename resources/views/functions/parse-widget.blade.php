@if(file_exists(base_path() . '/resources/views/components/' . $widget['type'] . '.blade.php'))
    @component('components.' . $widget['type'], $widget)
    @endcomponent
@endif
