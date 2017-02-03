<?php
//check that zlib compression is enabled
if (!ini_get('zlib.output_compression')) {
    die();
}
function setCompressionQuality($imagePath, $quality) {

    $backgroundImagick = new \Imagick(realpath($imagePath));
    $imagick = new \Imagick();

    $imagick->setCompressionQuality($quality);
    $imagick->newPseudoImage(
        $backgroundImagick->getImageWidth(),
        $backgroundImagick->getImageHeight(),
        'canvas:white'
    );

    $imagick->compositeImage(
        $backgroundImagick,
        \Imagick::COMPOSITE_ATOP,
        0,
        0
    );

    $imagick->setFormat("jpg");
    header("Content-Type: image/jpg");
    echo $imagick->getImageBlob();
}


$allowed = array('jpg', 'png','gif'); //set array of allowed file types to prevent abuse
//check for request variable existence and that file type is allowed
$imagePath=dirname(__FILE__) . '/' . $_GET['file'];




if (isset($_GET['file']) && isset($_GET['type']) && in_array(substr($_GET['file'], strrpos($_GET['file'], '.') + 1), $allowed)) {
    switch ($_GET['type']) {
        case 'jpg':
            $backgroundImagick = new \Imagick(realpath($imagePath));
            $imagick = new \Imagick();
            $imagick->setCompressionQuality(40);
            $imagick->newPseudoImage(
                $backgroundImagick->getImageWidth(),
                $backgroundImagick->getImageHeight(),
                'canvas:white'
            );

            $imagick->compositeImage(
                $backgroundImagick,
                \Imagick::COMPOSITE_ATOP,
                0,
                0
            );

            $imagick->setFormat("jpg");
            header("Content-Type: image/jpg");
            break;
        case 'png':
            $image = new Imagick($imagePath);
            $imagick->resizeImage(40,40, Imagick::FILTER_LANCZOS, 1);
            $imagick->roundCorners(40, 40);
            $imagick->setImageFormat("png");
            $imagick->setImageCompression(\Imagick::COMPRESSION_UNDEFINED);
            $imagick->setImageCompressionQuality(0);
            $imagick->stripImage();
            header("Content-Type: image/png; charset: UTF-8");
            break;
        case 'gif':
            $backgroundImagick = new \Imagick(realpath($imagePath));
            $imagick = new \Imagick();
            $imagick->setCompressionQuality(40);
            $imagick->newPseudoImage(
                $backgroundImagick->getImageWidth(),
                $backgroundImagick->getImageHeight(),
                'canvas:white'
            );

            $imagick->compositeImage(
                $backgroundImagick,
                \Imagick::COMPOSITE_ATOP,
                0,
                0
            );

            $imagick->setFormat("gif");
            header("Content-Type: image/gif; charset: UTF-8");
            break;
    }
    header('Cache-Control: max-age=300000000, must-revalidate'); //output the cache-control header
    $offset = 60 * 60;
    $expires = 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT'; // set the expires header to be 1 hour in the future
    header($expires); // output the expires header
    // check the Etag the browser already has for the file and only serve the file if it is different

    echo $imagick->getImageBlob();
}
?>