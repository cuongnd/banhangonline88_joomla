<?php
/**
 * @package Sj Minicart Pro for Hikashop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */

defined('_JEXEC') or die;

if(!class_exists('plgSystemPlg_Sj_Hk_Ajax_MiniCart_Pro')){
	echo '<p ><b>'.JText::_('WARNING_NOT_INSTALL_PLUGIN').'</b></p>';
	return ;
}
global $Itemid;
$url_itemid='';
if(!empty($Itemid)){
	$url_itemid='&Itemid='.$Itemid;
}
$hkshop_helper = rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS . 'helper.php';

if (file_exists($hkshop_helper)) {
	!class_exists('hikashop') && require_once $hkshop_helper;
} else {
	echo JText::_('WARNING_LABEL');
	return;
}
require_once dirname(__FILE__).'/core/helper.php';

$layout = $params->get('layout', 'default');
$cart = HKMinicartproHelper::getList ($params);

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$is_ajax_from_minicart_pro = (int)JRequest::getVar('minicart_ajax', 0);
if ($is_ajax && $is_ajax_from_minicart_pro){
	if( JRequest::getCmd('minicart_task')=='refresh' ){
		require JModuleHelper::getLayoutPath($module->module,$layout.'_list');
	}
}else{
	if($cart){
		$billTotal = $cart->total;
		$_price = '';
		$currencyHelper = hikashop_get('class.currency');
		if(empty($billTotal->prices)){
			$_price .=  JText::_('FREE_PRICE');
		}else{
			foreach($billTotal->prices as $price){
				$_price .=  $currencyHelper->format($price->price_value,$price->price_currency_id);
			}
		}
		$cart->billTotal = $_price;
		$lang = JFactory::getLanguage();
		$extension = 'com_hikashop';
		$lang->load($extension);
//		if ($cart->_dataValidated == true) {
//			$taskRoute = '&task=confirm';
//			$linkName = JText::_('GO_TO_CART');
//		} else {
		$taskRoute = '';
		$linkName = JText::_('GO_TO_CART');
//		}

		$linkshopingcart = JRoute::_("index.php?option=com_hikashop&view=cart" . $taskRoute);
		$cart->ajaxurl = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';
		$cart->ajaxurl .= $_SERVER['HTTP_HOST'].$linkshopingcart;
		$cart->cart_show = '<a class="mc-gotocart" href="' .$linkshopingcart. '">' . $linkName . '</a>';

		$cart->billTotal = $lang->_('COM_HIKASHOP_CART_TOTAL').' : <strong>'. $cart->billTotal .'</strong>';
		$cart->checkout = hikashop_completeLink('checkout'.$url_itemid);
		require JModuleHelper::getLayoutPath($module->module,$layout);
		require JModuleHelper::getLayoutPath($module->module, $layout.'_js');
	}
}
?>