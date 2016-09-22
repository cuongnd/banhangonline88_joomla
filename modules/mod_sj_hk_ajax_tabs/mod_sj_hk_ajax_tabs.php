<?php

/**
 * @package SJ Ajax Tabs for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

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

require_once dirname(__FILE__) . '/core/helper.php';

$layout = $params->get('layout', 'default');

$list = HKAjaxtabsHelper::getList($params, $module);
if (!empty($list)) {
	$is_ajax_request = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	$is_ajax_request = $is_ajax_request || JRequest::getInt('sj_module_ajax_request', 0);
	if ($is_ajax_request) {
		$category_id = JRequest::getVar('sj_category_id', null);
		$sj_module_id = JRequest::getVar('sj_module_id', null);
		$sj_module = JRequest::getVar('sj_module', null);

		if ($sj_module == $module->module && $sj_module_id == $module->id) {
			$category_items = HKAjaxtabsHelper::_getProductInfor($category_id, $params);
			ob_start();
			include JModuleHelper::getLayoutPath($module->module, $layout . '_items');
			$ajax_respond = ob_get_contents();
			ob_end_clean();
			die($ajax_respond);
		}
	} else {
		require JModuleHelper::getLayoutPath($module->module, $layout);
	}
} else {
	echo JText::_('Has no content to show!');
}	


