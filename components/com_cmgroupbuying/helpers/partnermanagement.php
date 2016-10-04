<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperPartnerManagement
{
	public static function buildMenu($permissions, $navigation)
	{
		$html = '<ul class="nav nav-list">';

		// Dashboard
		if($navigation == 'dashboard')
			$class = ' class="active"';
		else
			$class = '';
		$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
		$html .= '    <li' . $class .'><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_DASHBOARD') . '</a></li>';

		// Deal
		if($permissions['view_deal_list'] == true || $permissions['submit_new_deal'] == true)
			$html .= '    <li class="nav-header">' . JText::_('COM_CMGROUPBUYING_DEAL') . '</li>';

		if($permissions['view_deal_list'] == true):
			if($navigation == 'deal_list')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_list');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_DEAL_LIST') . '</a></li>';
		endif;

		if($permissions['submit_new_deal'] == true):
			if($navigation == 'deal_submission')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_submission');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_DEAL_SUBMISSION') . '</a></li>';
		endif;

		// Free coupon
		if($permissions['view_free_coupon_list'] == true || $permissions['submit_new_free_coupon'] == true)
			$html .= '    <li class="nav-header">' . JText::_('COM_CMGROUPBUYING_FREE_COUPON') . '</li>';

		if($permissions['view_free_coupon_list'] == true):
			if($navigation == 'free_coupon_list')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=free_coupon_list');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_FREE_COUPON_LIST') . '</a></li>';
		endif;

		if($permissions['submit_new_free_coupon'] == true):
			if($navigation == 'free_coupon_submission')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=free_coupon_submission');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_FREE_COUPON_SUBMISSION') . '</a></li>';
		endif;

		// Coupon
		if($permissions['check_coupon_status'] == true || $permissions['view_coupon_list'] == true)
			$html .= '    <li class="nav-header">' . JText::_('COM_CMGROUPBUYING_COUPON') . '</li>';

		if($permissions['check_coupon_status'] == true):
			if($navigation == 'coupon_status')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_status');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_COUPON_STATUS') . '</a></li>';
		endif;

		if($permissions['view_coupon_list'] == true):
			if($navigation == 'coupon_list')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_list');
			$html .= '    <li' . $class . '><a href="' . $link .'">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_COUPON_LIST') . '</a></li>';
		endif;

		// Report
		if($permissions['view_commission_report'] == true):
			$html .= '    <li class="nav-header">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_REPORT') . '</li>';
			if($navigation == 'commission_report')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=commission_report');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_COMMISSION') . '</a></li>';
		endif;

		// Profile
		if($permissions['edit_profile'] == true):
			$html .= '    <li class="nav-header">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_PROFILE') . '</li>';
			if($navigation == 'profile')
				$class = ' class="active"';
			else
				$class = '';
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=profile');
			$html .= '    <li' . $class . '><a href="' . $link . '">' . JText::_('COM_CMGROUPBUYING_MANAGEMENT_MENU_EDIT_PROFILE') . '</a></li>';
		endif;

		$html .= '</ul>';
		return $html;
	}

	public static function getPartnerStats($partnerId)
	{
		$result = array(
			'numOfDeals' => 0,
			'numOfCoupons' => 0,
			'earning' => 0
		 );
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Count deals
		$query->select('id, partner_id, advance_payment, commission_rate');
		$query->from('#__cmgroupbuying_deals');
		$query->where('partner_id = ' . $partnerId);
		$db->setQuery($query);
		$deals = $db->loadAssocList('id');
		$result['numOfDeals'] = count($deals);

		// Count coupons
		foreach($deals as $deal)
		{
			$query->clear();
			$query->select('deal_id, option_id');
			$query->from('#__cmgroupbuying_coupons');
			$query->where('deal_id = ' . $deal['id']);
			$query->where('coupon_status <> 0');
			$db->setQuery($query);
			$coupons = $db->loadAssocList();
			$result['numOfCoupons'] += count($coupons);

			if(!empty($coupons))
			{
				foreach($coupons as $coupon)
				{
					$earning = 0;
					$query->clear();
					$query->select('price, advance_price');
					$query->from('#__cmgroupbuying_deal_option');
					$query->where('deal_id = ' . $coupon['deal_id']);
					$query->where('option_id = ' . $coupon['option_id']);
					$db->setQuery($query);
					$option = $db->loadAssoc();

					if($deals[$coupon['deal_id']]['advance_payment'] == true)
					{
						$earning += $option['price'] - $option['advance_price'];
					}
					else
					{
						$earning += $option['price'];
					}

					$result['earning'] += $earning * (100 - $deal['commission_rate']) / 100;
				}
			}
		}

		return $result;
	}
}