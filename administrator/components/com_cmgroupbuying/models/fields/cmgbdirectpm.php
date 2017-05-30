<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/plugin.php");

class JFormFieldCmgbdirectpm extends JFormField
{
	protected $type = 'Cmgbdirectpm';

	protected function getInput()
	{
		$configuration = JModelLegacy::getInstance('Configuration','CMGroupBuyingModel')
			->getConfiguration('direct_payment_method');

		$payments = CMGroupBuyingHelperPlugin::getPaymentsByType('direct');

		$html = array();
		$paymentOption = array();
		$paymentOption[] = JHTML::_('select.option', '',
			JText::_('COM_CMGROUPBUYING_CONFIGURATION_SELECT_DIRECT_PAYMENT_METHOD'));

		foreach($payments as $payment)
		{
			$paymentOption[] = JHTML::_('select.option', $payment['id'], $payment['name']);
		}

		$html[] = JHTML::_('select.genericlist', $paymentOption,
			$this->name, null, 'value', 'text', $configuration['direct_payment_method']);

		return implode($html);
	}
}