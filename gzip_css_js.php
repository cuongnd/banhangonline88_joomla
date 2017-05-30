<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('_JEXEC', 1);
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
        echo "<pre>";
        print_r($app->input->getArray(),true);
        echo "</pre>";
        die;
        $allowed = array('css', 'js'); //set array of allowed file types to prevent abuse
//check for request variable existence and that file type is allowed
        if (isset($_GET['file']) && isset($_GET['type']) && in_array(substr($_GET['file'], strrpos($_GET['file'], '.') + 1), $allowed)) {
            $file_path=dirname(__FILE__) . '/' . $_GET['file'];

            $data = file_get_contents($file_path); // grab the file contents
            $etag = '"' . md5($data) . '"'; // generate a file Etag
            header('Etag: ' . $etag); // output the Etag in the header
            // output the content-type header for each file type
            switch ($_GET['type']) {
                case 'css':
                    header("Content-Type: text/css; charset: UTF-8");
                    break;
                case 'js':
                    header("Content-Type: text/javascript; charset: UTF-8");
                    break;
            }
            header('Cache-Control: max-age=300000000, must-revalidate'); //output the cache-control header
            $offset = 60 * 60;
            $expires = 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT'; // set the expires header to be 1 hour in the future
            header($expires); // output the expires header
            // check the Etag the browser already has for the file and only serve the file if it is different
            echo $data;
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