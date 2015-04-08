<?php
//new
$path = realpath(dirname(__FILE__)."/../../../../");
$config_path = $path."/wa-config/SystemConfig.class.php";
if (!file_exists($config_path)) {
    die('5');
    header("Location: ../../../wa-apps/shop/img/image-not-found.png");
    exit;
}

require_once($config_path);
$config = new SystemConfig();
waSystem::getInstance(null, $config);

$app_config = wa('dislider')->getConfig();
$request_file = $app_config->getRequestUrl(true, true);

$protected_path = wa()->getDataPath('', false, 'dislider').'/';
$public_path = wa()->getDataPath('', true, 'dislider').'/';

$main_thumb_file = false;
$file = false;
$size = false;
if (preg_match('#(\d{2})/(\d{2})/(\d+)/(\d+)\.(\d+(?:x\d+)?)\.([a-z]{3,4})#i', $request_file, $matches)) {
    $match_1 = $matches[1]-1;
    if( strlen($match_1) == 1 ) $match_1 = '0' . $match_1;
    $match_2 = $matches[2];
    if( strlen($match_2) == 1 ) $match_1 = '0' . $match_1;

    $file = ($matches[1]-1) .'/' .($matches[2]) .'/' .($matches[3] -1).'.'.$matches[6];
    $size = $matches[5];
}

wa()->getStorage()->close();

$original_path = $protected_path.$file;
$thumb_path = $public_path.$request_file;
if ($file && file_exists($original_path) && !file_exists($thumb_path)) {
    $thumbs_dir = dirname($thumb_path);
    if (!file_exists($thumbs_dir)) {
        waFiles::create($thumbs_dir);
    }

    $image = generateThumb($original_path, $size);
    if ($image) {
        $image->save($thumb_path,90);
        clearstatcache();
    }
}

if ($file && file_exists($thumb_path)) {
    waFiles::readFile($thumb_path);
} else {
    header("HTTP/1.0 404 Not Found");
    exit;
}
function parseSize($size)
{
    $type = 'unknown';
    $ar_size = explode('x', $size);
    $width = !empty($ar_size[0]) ? $ar_size[0] : null;
    $height = !empty($ar_size[1]) ? $ar_size[1] : null;

    if (count($ar_size) == 1) {
        $type = 'max';
        $height = $width;
    } else {
        if ($width == $height) { // crop
            $type = 'crop';
        } else {
            if ($width && $height) { // rectange
                $type = 'rectangle';
            } else
                if (is_null($width)) {
                    $type = 'height';
                } else
                    if (is_null($height)) {
                        $type = 'width';
                    }
        }
    }
    return array(
        'type'   => $type,
        'width'  => $width,
        'height' => $height
    );
}
function generateThumb($original_path, $size) {
    $width = $height = null;
    $size_info = parseSize($size);
    $type = $size_info['type'];
    $width = $size_info['width'];
    $height = $size_info['height'];

    $image = waImage::factory($original_path);

    switch ($type) {
        case 'max':
            $image->resize($width, $height);
            break;
        case 'crop':
            $image->resize($width, $height, waImage::INVERSE)->crop($width, $height);
            break;
        case 'width':
            $image->resize($width, $height);
            break;
        case 'height':
            $image->resize($width, $height);
            break;
        case 'rectangle':
            if ($width > $height) {
                $w = $image->width;
                $h = $image->width * $height / $width;
            } else {
                $h = $image->height;
                $w = $image->height * $width / $height;
            }
            $image->crop($w, $h)->resize($width, $height, waImage::INVERSE);
            break;
        default:
            throw new waException("Unknown type");
            break;
    }
    return $image;
}