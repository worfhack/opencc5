<?php

require_once('../config/bootstrap.php');
$size = $_GET['size'];
$file = $_GET['file'];
$original = MEDIA_DIR."/$file";


$target = MEDIA_CACHE_DIR."/$size/$file";
// Check the size is valid
switch ($size) {
    case 'thumbnail':
        $thumbWidth = 398;
        $thumbHeight = 510;
        break;
    default:
        die('Invalid image size');
}

// Double the size for retina devices
if (isset($retina)) {
    if ($thumbWidth) $thumbWidth *= 2;
    if ($thumbHeight) $thumbHeight *= 2;
    $original = str_replace('@2x', '', $original);
}
// Check the original file exists
if (!is_file($original)) {
    die('File doesn\'t exist');
}
// Make sure the directory exists
if (!is_dir(MEDIA_CACHE_DIR.'/'.$size)) {
    mkdir(MEDIA_CACHE_DIR.'/'.$size);
    if (!is_dir(MEDIA_CACHE_DIR.'/'.$size)) {
        die('Cannot create directory');
    }
    chmod(MEDIA_CACHE_DIR.'/'.$size, 0775);
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
            die("Invalid image type (#{$type} = " . image_type_to_extension($type) . ")");
    }
    // Calculate height automatically if not given
    if ($thumbHeight === null) {
        $thumbHeight = round($height * $thumbWidth / $width);
    }
    // Ratio to resize by
    $widthProportion = $thumbWidth / $width;
    $heightProportion = $thumbHeight / $height;
    $proportion = max($widthProportion, $heightProportion);
    // Area of original image that will be used
    $origWidth = floor($thumbWidth / $proportion);
    $origHeight = floor($thumbHeight / $proportion);

    $imageEngine = new GdThumb($original);
    $imageEngine->resize($thumbWidth, $thumbHeight);
    $imageEngine->save($target);
}
    // Co-ordinates of original image to use
//    $x1 = floor($width - $origWidth) / 2;
//    $y1 = floor($height - $origHeight) / 2;
//    // Resize the image
//    $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
//    imagecopyresampled($thumbImage, $image, 0, 0, $x1, $y1, $thumbWidth, $thumbHeight, $origWidth, $origHeight);
//    // Save the new image
//    switch ($type)
//    {
//        case IMAGETYPE_GIF:
//            imagegif($thumbImage, $target);
//            break;
//        case IMAGETYPE_JPEG:
//            imagejpeg($thumbImage, $target, 90);
//            break;
//        case IMAGETYPE_PNG:
//            imagepng($thumbImage, $target);
//            break;
//        default:
//            throw new LogicException;
//    }
//    // Make sure it's writable
//    chmod($target, 0666);
//    // Close the files
//    imagedestroy($image);
//    imagedestroy($thumbImage);
//}
//// Send the file header
    $data = getimagesize($original);
//
    if (!$data) {
        die("Cannot get mime type");
    } else {
        header('Content-Type: ' . $data['mime']);
    }
//// Send the file to the browser
    readfile($target);
