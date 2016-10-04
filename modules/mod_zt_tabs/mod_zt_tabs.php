<?php
/**
 * @package ZT Tabs module
 * @author DucNA
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');// no direct access
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}
require_once (dirname(__FILE__).DS.'helper.php');



$helper 	= new moZTTabsHelper($params);

$helper->parseData();
$helper->renderLayout();

