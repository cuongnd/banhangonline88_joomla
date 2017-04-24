<?php
define('_JEXEC', 1);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}
require_once JPATH_BASE . '/includes/framework.php';
// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->setStart($startTime, $startMem)->mark('afterLoad') : null;
// Instantiate the application.
$app = JFactory::getApplication('site');

$key_vtlai_firewall_redirect="vtlai_firewall_redirect";
$post_vtlai_firewall_redirect=$app->input->getString($key_vtlai_firewall_redirect,'');

$session=JFactory::getSession();
if($post_vtlai_firewall_redirect){
    $session->set($key_vtlai_firewall_redirect,$post_vtlai_firewall_redirect);
}
$vtlai_firewall_redirect=$session->get($key_vtlai_firewall_redirect,'');

if($vtlai_firewall_redirect=="home"){
    $user_return=$app->get('user_return','');
    if($user_return!=""){
        $user_return=base64_decode($user_return);
        $app->redirect($user_return);
    }else{

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
    }

}else{
    $current_link=JURI::current();
    $root_link=JURI::root();
    if($current_link!=$root_link){
        $current_link=base64_encode($current_link);
    }else{
        $current_link=false;
    }
    $confirm_access='/confirm_access.php'.($current_link?"?user_return=$current_link":"");
    $app->redirect($confirm_access);
}
?>