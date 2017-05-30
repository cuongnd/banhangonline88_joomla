<?php

/**
 * @package SJ Slideshow for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
define('SMART_JQUERY', 1);
define('JQUERY_CYLE2', 1);
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);

$hkshop_helper = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';

if(file_exists($hkshop_helper))
{
	!class_exists('hikashop') && require_once $hkshop_helper;
}
else
{
	echo JText::_('WARNING_LABEL');
	return;
}

require_once dirname( __FILE__ ).'/core/helper.php';

$layout = $params->get('layout', 'default');
$cacheid = md5(serialize(array ($layout, $module)));
$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'HKSlideshowHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
require JModuleHelper::getLayoutPath($module->module, $layout);