<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingViewCart extends JViewLegacy
{
	function sort_payments($a, $b)
	{
		if($a['extension_id'] == $b['extension_id'])
		{
			return 0;
		}

		return ($a['extension_id'] < $b['extension_id']) ? -1 : 1;
	}

	function display($tpl = null)
	{
		$session = JFactory::getSession();
		$cart = $session->get('cart', array(), 'CMGroupBuying');
		$formCache = $session->get('form', array(), 'CMGroupBuying');
		$items = isset($cart['items']) ? $cart['items'] : array();
		$referrer = isset($cart['referrer']) ? $cart['referrer'] : '';
		$points = isset($cart['points']) ? $cart['referrer'] : 0;

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('max_displayed_quantity, exchange_rate, jquery_loading, tos, point_system,
				pay_with_point, currency_decimals, currency_thousands_sep, currency_dec_point,
				currency_prefix, currency_postfix, buy_as_guest,
				payment_method_type, direct_payment_method, payment_method_pretext, payment_method_posttext');
		$this->assignRef('configuration', $configuration);

		$profileSetting = JModelLegacy::getInstance('Profile', 'CMGroupBuyingModel')->getProfileSettings();
		$this->assignRef('profileSetting', $profileSetting);

		if($configuration['payment_method_type'] == 'hosted')
		{
			$payments = CMGroupBuyingHelperPlugin::getPaymentsByType('hosted');
			uasort($payments, array($this, 'sort_payments'));
			$this->assignRef('payments', $payments);
		}

		$userPoints = 0;

		if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial") && $configuration['pay_with_point'] == 1)
		{
			$userId = JFactory::getUser()->id;

			if($configuration['point_system'] == "aup")
			{
				$userPoints = CMGroupBuyingHelperAlphauserpoints::getUserPoints($userId);
			}
			elseif($configuration['point_system'] == "jomsocial")
			{
				$userPoints = CMGroupBuyingHelperJomsocial::getUserPoints($userId);
			}
		}

		$this->assignRef('userPoints', $userPoints);
		$this->assignRef('cart', $cart);
		$this->assignRef('formCache', $formCache);

		$pageTitle = JText::_('COM_CMGROUPBUYING_CART_PAGE_TITLE');
		$this->assignRef('pageTitle', $pageTitle);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "cart";
		parent::display($tpl);
	}
}
?>