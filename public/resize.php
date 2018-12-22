<?php

require_once('../config/bootstrap.php');

$size = Tools::getValue('size');
$height = Tools::getValue('height');
$file = Tools::getValue('file');
$width = Tools::getValue('width');



$original = MEDIA_DIR . "/$file";



// Check the size is valid
if (!empty($size) &&  empty($width) && empty(!$height)) {
    switch ($size) {
        case 'thumbnail':
            $thumbWidth = 398;
            $thumbHeight = 510;
            break;
        default:
            throw new Exception('Invalid image size');
    }
    $key_file =$size;
} else {
    $thumbWidth = (int)$width;
    $thumbHeight = (int)$height;
    $key_file =$width."_".$height;
}
$target = MEDIA_CACHE_DIR . "/$key_file/$file";
// Double the size for retina devices
if (isset($retina)) {
    if ($thumbWidth) $thumbWidth *= 2;
    if ($thumbHeight) $thumbHeight *= 2;
    $original = str_replace('@2x', '', $original);
}
// Check the original file exists
if (!is_file($original)) {
    throw new Exception('File doesn\'t exist');
}
// Make sure the directory exists
if (!is_dir(MEDIA_CACHE_DIR . '/' . $key_file)) {
    mkdir(MEDIA_CACHE_DIR . '/' . $key_file);
    if (!is_dir(MEDIA_CACHE_DIR . '/' . $key_file)) {
        throw new Exception('Cannot create directory');
    }
    chmod(MEDIA_CACHE_DIR . '/' . $key_file, 0775);
}
// Make sure the file doesn't exist already
if (!file_exists($target) ) {
    // Make sure we have enough memory
    ini_set('memory_limit', 128 * 1024 * 1024);
    // Get the current size & file type
    list($width, $height, $type) = getimagesize($original);
    // Load the image
    switch ($type) {
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($original);
            break;
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($original);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($original);
            break;
        default:
            throw new Exception("Invalid image type (#{$type} = " . image_type_to_extension($type) . ")");
    }
    // Calculate height automatically if not given
    if ($thumbHeight === null) {
       // $thumbHeight = round($height * $thumbWidth / $width);
    }

    $imageEngine = new GdThumb($original);
    $imageEngine->resize($thumbWidth, $thumbHeight);
    $imageEngine->save($target);
}
$data = getimagesize($original);
//

if (!$data) {
    throw new Exception("Cannot get mime type");
} else {
    header('Content-Type: ' . $data['mime']);
}
//// Send the file to the browser
readfile($target);
