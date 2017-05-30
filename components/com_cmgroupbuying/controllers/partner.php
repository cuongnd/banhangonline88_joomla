<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingControllerPartner extends JControllerLegacy
{
	public function change_coupon_status()
	{
		$jinput = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		$navigation = $jinput->get('navigation', '', 'word');

		if($navigation == 'coupon_status')
		{
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_list');
		}
		else
		{
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partner');
		}

		if($user->guest)
		{
			$message = JText::_('COM_CMGROUPBUYING_LOGIN_FIRST');
			$redirectUrl = base64_encode($redirectUrl);
			$redirectUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=".$redirectUrl, false);
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$getUnpublished = false;
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id, $getUnpublished);
	
		if(empty($partner))
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$couponCode = $jinput->get('coupon_code', 'word');

		if($couponCode != '')
		{
			$coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponOfPartner($couponCode, $partner['id']);

			if($coupon['coupon_status'] == 1)
			{
				$result = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->setCouponStatusByCouponCode($couponCode, 2);
			}
			elseif($coupon['coupon_status'] == 2)
			{
				$result = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->setCouponStatusByCouponCode($couponCode, 1);
			}

			if($result)
			{
				$message = JText::_('COM_CMGROUPBUYING_PARTNER_CHANGE_STATUS_SUCCESSFULLY');
				$type = '';
			}
			else
			{
				$message = JText::_('COM_CMGROUPBUYING_PARTNER_CHANGE_STATUS_FAILED');
				$type = 'error';
			}

			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}
	}
}