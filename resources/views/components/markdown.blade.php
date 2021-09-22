<?php
    if (isset($file) && strlen($file) > 0){
        try {
            echo (new Parsedown())->text(file_get_contents(base_path() . '/public/' . $file));
        } catch (Exception $ignored) {
            echo "Markdown Parser Error";
        }
    } else if(isset($text) && strlen($text) > 0) echo (new Parsedown())->text($text);
?>
