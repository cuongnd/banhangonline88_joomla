<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingControllerCoupons extends JControllerAdmin
{
	protected $text_prefix = 'COM_CMGROUPBUYING_COUPON';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Coupon', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function set_exchanged()
	{
		$jinput = JFactory::getApplication()->input;
		$couponList = $jinput->post->get('cid', array(), 'array');

		foreach($couponList as $coupon)
		{
			JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByCode($coupon , 2);
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=coupon&cid=' . $coupon;
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function set_waiting()
	{
		$jinput = JFactory::getApplication()->input;
		$couponList = $jinput->post->get('cid', array(), 'array');

		foreach($couponList as $coupon)
		{
			JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByCode($coupon , 1);
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=coupon&cid=' . $coupon;
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function set_unpaid()
	{
		$jinput = JFactory::getApplication()->input;
		$couponList = $jinput->post->get('cid', array(), 'array');

		foreach($couponList as $coupon)
		{
			JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByCode($coupon , 0);
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=coupon&cid=' . $coupon;
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}
}