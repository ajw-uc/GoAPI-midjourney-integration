<?php

include_once('../core.php');
function get_random_image() {
    $files = glob(ROOT.'/data/*');
    if (count($files) > 0) {
        $file = array_rand($files);
        $file = json_decode(file_get_contents($files[$file]));
        return isset($file->imagine->image_url) ? $file->imagine->image_url : get_random_image();
    }
    return '';
}

echo get_random_image();