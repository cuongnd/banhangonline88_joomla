<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperStaffManagement
{
	public static function buildMenu($permissions, $navigation)
	{
		$html = '<ul class="nav nav-list">';

		// Dashboard
		if($navigation == 'dashboard')
			$class = ' class="active"';
		else
			$class = '';
		$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement');
		$html .= '    <li' . $class .'><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_DASHBOARD') . '</a></li>';

		// Order
		if($navigation == 'order_list')
			$class = ' class="active"';
		else
			$class = '';
		$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list');
		$html .= '    <li' . $class .'><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_ORDER_LIST') . '</a></li>';

		// Order
		if($navigation == 'coupon_list')
			$class = ' class="active"';
		else
			$class = '';
		$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_list');
		$html .= '    <li' . $class .'><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_COUPON_LIST') . '</a></li>';

		$html .= '</ul>';
		return $html;
	}
}