<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperPlugin
{
	public static function getPaymentPlugins()
	{
		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('cmpayment');
		$app = JFactory::getApplication();
		$payments = $app->triggerEvent('onCMPaymentGetIdentity');
		return $payments;
	}

	public static function getPaymentPluginById($paymentId)
	{
		$payments = CMGroupBuyingHelperPlugin::getPaymentPlugins();

		foreach($payments as $payment)
		{
			if($payment['id'] == $paymentId)
			{
				return $payment;
			}
		}

		return array();
	}

	public static function getPaymentsByType($type = 'hosted')
	{
		$allPayments = CMGroupBuyingHelperPlugin::getPaymentPlugins();
		$payments = array();

		foreach($allPayments as $payment)
		{
			if(isset($payment['type']) && $payment['type'] == $type)
			{
				$payments[] = $payment;
			}
		}

		return $payments;
	}
}