<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerPayment extends JControllerForm
{
	public function getPaymentById($paymentId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_payments WHERE id = " . $paymentId;
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