<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingViewOrder extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$orderIdList = $jinput->get('cid', null, 'array');

		if(is_array($orderIdList))
			$orderId = $orderIdList[0];
		else
			$orderId = $orderIdList;

		$task = $jinput->get('task', '', 'word');
		$coupons = array();

		if(!is_numeric($orderId))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
			$app->enqueueMessage( $message, 'error');
			$app->redirect($redirectUrl);
		}

		$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($orderId);

		if(!empty($items))
		{
			foreach($items as $key=>$item)
			{
				$deal = CMGroupBuyingHelperDeal::getDealByItemId($item['id']);

				if(empty($deal))
				{
					$item['option_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					$item['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
				}
				else
				{
					$option = JModelLegacy::getInstance('DealOption', 'CMGroupBuyingModel')->getOption($item['deal_id'], $item['option_id']);

					if(empty($option))
					{
						$item['option_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					}
					else
					{
						$item['option_name'] = $option['name'];
					}

					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

					if(empty($deal))
					{
						$item['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					}
					else
					{
						$item['deal_name'] = $deal['name'];
					}
				}

				$item['coupons'] = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($item['id']);
				$coupons[] = $item['coupons'];
				$items[$key] = $item;
			}
		}

		$order['referrer_name'] = '';

		if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial") && $configuration['deal_referral'] == 1)
		{
			if($configuration['point_system'] == "aup")
			{
				$order['referrer_name'] = CMGroupBuyingHelperAlphauserpoints::getUserNameByReferrerID($order['referrer']);
			}
			elseif($configuration['point_system'] == "jomsocial" && $order['referrer'] != '')
			{
				$user = JFactory::getUser($order['referrer']);

				if(!empty($user))
				{
					$order['referrer_name'] = $user->username;
				}
			}
		}

		$this->assignRef('configuration', $configuration);
		$this->assignRef('order', $order);
		$this->assignRef('items', $items);
		$this->addToolbar($order, $coupons);

		if($task == 'edit')
		{
			$this->_layout = "edit";
			parent::display($tpl);
		}
		else
		{
			parent::display($tpl);
		}
	}

	protected function addToolbar($order, $coupons)
	{
		$task = JFactory::getApplication()->input->get('task', '', 'word');
		if($task == 'edit')
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_ORDER_EDIT_INFO_TITLE'), 'order.png');
			JToolBarHelper::back('COM_CMGROUPBUYING_BACK', 'index.php?option=com_cmgroupbuying&view=orders');
			JToolBarHelper::divider();
			JToolBarHelper::custom('order.save_user_info', 'apply.png', 'apply.png', 'JAPPLY', false);
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_ORDER_VIEW_INFO_TITLE'), 'order.png');
			JToolBarHelper::back('COM_CMGROUPBUYING_BACK', 'index.php?option=com_cmgroupbuying&view=orders');
			JToolBarHelper::divider();

			if($order['status'] != 0)
			{
				JToolBarHelper::custom('orders.set_unpaid', 'unpublish.png', 'unpublish.png', 'COM_CMGROUPBUYING_ORDER_SET_UNPAID', false);
			}

			if($order['status'] != 1)
			{
				JToolBarHelper::custom('orders.set_paid', 'publish.png', 'publish.png', 'COM_CMGROUPBUYING_ORDER_SET_PAID', false);
			}

			if($order['status'] != 4)
			{
				JToolBarHelper::custom('orders.set_refunded', 'restore.png', 'restore.png', 'COM_CMGROUPBUYING_ORDER_SET_REFUNDED', false);
			}

			JToolBarHelper::divider();
			JToolBarHelper::custom('order.edit_user_info', 'edit.png', 'edit.png', 'COM_CMGROUPBUYING_ORDER_EDIT_USER_INFO', false);

			if($order['status'] == 1)
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('order.send_coupon', 'send.png', 'send.png', 'COM_CMGROUPBUYING_ORDER_SEND_COUPON', false);
				
			}
		}
	}
}