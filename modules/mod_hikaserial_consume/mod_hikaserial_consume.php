<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php')) {
	echo 'This module can not work without the HikaSerial Component';
	return;
}

hikaserial::initModule();
$params->set('from_module', $module->id);

$serialConfig = hikaserial::config();
$module_options = $serialConfig->get('params_'.$module->id);
if(empty($module_options)) {
	$shopConfig = hikaserial::config(false);
	$module_options = $shopConfig->get('default_params');
}

foreach($module_options as $key => $option) {
	if($key != 'moduleclass_sfx')
		$params->set($key,$option);
}

foreach(get_object_vars($module) as $k => $v) {
	if(!is_object($v))
		$params->set($k,$v);
}

$js = '';
$html = trim(hikaserial::getLayout('serial', 'consume', $params, $js));
require(JModuleHelper::getLayoutPath('mod_hikaserial_consume'));
