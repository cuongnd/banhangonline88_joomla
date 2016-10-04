<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelPayment extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getPayments()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_payments'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->order($query->quoteName('ordering') . ' ASC');

		$db->setQuery($query);
		$payments=  $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $payments;
	}

	public function getPaymentById($paymentId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_payments'))
			->where($db->quoteName('id') . ' = ' . $db->quote($paymentId));

		$db->setQuery($query);
		$payment = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $payment;
	}
}