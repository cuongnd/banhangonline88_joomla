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

class CMGroupBuyingControllerFreeCoupon extends JControllerLegacy
{
	public function download()
	{

		$couponId = JFactory::getApplication()->input->get('coupon_id', 0, 'int');
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecoupons');
		$message = JText::_('COM_CMGROUPBUYING_FREE_COUPON_NOT_FOUND_MESSAGE');

		if(!is_numeric($couponId) || $couponId <= 0)
		{
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$coupon = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')-> getCouponById($couponId);

		if(empty($coupon))
		{
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$this->update_view($couponId, false);
		echo '<img src="' . JURI::root() . $coupon['coupon_path'] . '" />';
		echo '<br/><input type="button" onclick="document.getElementById(\'print_button\').style.display = \'none\'; window.print(); window.document.getElementById(\'print_button\').style.display = \'inline\';" value="' . JText::_('COM_CMGROUPBUYING_COUPON_PRINT_BUTTON') . '" id="print_button">';
		jexit();
	}

	public function update_view($couponId = null, $ajax = true)
	{
		if($couponId == null)
			$couponId = JFactory::getApplication()->input->get('coupon_id', 0, 'int');

		if(!is_numeric($couponId) || $couponId <= 0)
		{
			if($ajax) jexit();
		}

		$coupon  = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')-> getCouponById($couponId);

		if(empty($coupon))
		{
			if($ajax) jexit();
		}

		JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')-> updateView($couponId);
		if($ajax) jexit();
	}
}