<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CMGroupBuyingControllerPesapal extends JControllerLegacy
{
	public function cron()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_orders'))
			->where($db->quoteName('payment_id') . ' = ' . $db->quote('pesapal'))
			->where($db->quoteName('transaction_id') . ' = ' . $db->quote('pesapal_pending'))
			->where($db->quoteName('transaction_info') . ' != ' . $db->quote(''));

		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if(!empty($orders))
		{
			jimport('joomla.plugin.helper');
			JPluginHelper::importPlugin('cmpayment');

			foreach($orders as $order)
			{
				JFactory::getApplication()->triggerEvent('onCMPaymentCron', array($order));
			}
		}

		jexit();
	}
}