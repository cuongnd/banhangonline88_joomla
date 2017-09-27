<?php
define('PATH_ROOT', __DIR__);
define('URL_ROOT', 'http://banhangonline88.com/nasia/');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once PATH_ROOT . DS . 'config.php';

require_once PATH_ROOT.DS.'libraries/core.php';
$core=core::getInstance();
$key=$_GET['key'];
$data=$core->getDataChuanHoaByKey($key);
echo json_encode($data);
?>