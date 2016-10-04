<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingControllerOrder extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function view_info()
	{
		$jinput = JFactory::getApplication()->input;
		$orderIdList = $jinput->post->get('cid', array(), 'array');

		if(count($orderIdList) > 1)
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_SELECT_ONE_MESSAGE');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
			$type = 'error';
			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}

		$jinput->set('view', 'order');
		parent::display();
	}

	public function edit_user_info()
	{
		$jinput = JFactory::getApplication()->input;
		$orderIdList = $jinput->post->get('cid', array(), 'array');

		if(count($orderIdList) > 1)
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_SELECT_ONE_MESSAGE');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
			$type = 'error';
			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}

		$jinput->set('view', 'order');
		$jinput->set('task', 'edit');
		parent::display();
	}
	
	public function save_user_info()
	{
		$jinput = JFactory::getApplication()->input;
		$orderId = $jinput->get('id', 0, 'int');

		$buyerInfo = array();
		$buyerInfo['name'] = $jinput->get('buyer_name', '', 'string');
		$buyerInfo['first_name'] = $jinput->get('buyer_first_name', '', 'string');
		$buyerInfo['last_name'] = $jinput->get('buyer_last_name', '', 'string');
		$buyerInfo['address'] = $jinput->get('buyer_address', '', 'string');
		$buyerInfo['city'] = $jinput->get('buyer_city', '', 'string');
		$buyerInfo['state'] = $jinput->get('buyer_state', '', 'string');
		$buyerInfo['zip_code'] = $jinput->get('buyer_zip_code', '', 'string');
		$buyerInfo['phone'] = $jinput->get('buyer_phone', '', 'string');
		$buyerInfo['email'] = $jinput->get('buyer_email', '', 'string');

		$receiverInfo = array();
		$receiverInfo['full_name'] = $jinput->get('receiver_full_name', '', 'string');
		$receiverInfo['email'] = $jinput->get('receiver_email', '', 'string');

		$jsonBuyerInfo = json_encode($buyerInfo);
		$jsonReceiverInfo = json_encode($receiverInfo);

		$result = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->updateUserInfo($jsonBuyerInfo, $jsonReceiverInfo, $orderId);

		if($result)
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_UPDATE_INFO_SUCCESSFULLY');
			$type = 'message';
		}
		else
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_UPDATE_INFO_FAILED');
			$type = 'error';
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
		$this->setRedirect($redirectUrl, $message, $type);
		$this->redirect();
	}

	public function send_coupon()
	{
		$orderIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';

		if(count($orderIdList) > 1)
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_SELECT_ONE_MESSAGE');
			$type = 'error';
			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}

		$orderId = $orderIdList[0];
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$type = 'error';
			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}

		$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

		if(empty($items))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ITEM_FOUND');
			$type = 'error';
			$this->setRedirect($redirectUrl, $message, $type);
			$this->redirect();
		}

		foreach($items as $item)
		{
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($item['deal_id']);

			if(empty($deal))
			{
				$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_ID_NOT_FOUND_MESSAGE', $item['deal_id']);
				$type = 'error';
				$this->setRedirect($redirectUrl, $message, $type);
				$this->redirect();
			}
		}

		foreach($items as $item)
		{
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($item['deal_id']);

			if(empty($deal))
			{
				$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_ID_NOT_FOUND_MESSAGE', $item['deal_id']);
				$type = 'error';
				$this->setRedirect($redirectUrl, $message, $type);
				$this->redirect();
			}
		}

		foreach($items as $item)
		{
			CMGroupBuyingHelperMail::sendCoupon($order, $item);
		}

		$this->setRedirect($redirectUrl);
		$this->redirect();
	}
}