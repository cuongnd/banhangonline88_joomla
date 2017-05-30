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

class CMGroupBuyingViewOrder extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if($user->guest)
		{
			$message = JText::_('COM_CMGROUPBUYING_LOGIN_FIRST');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');
			$redirectUrl = base64_encode($redirectUrl);
			$redirectUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=".$redirectUrl, false);
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$orderId = JFactory::getApplication()->input->get('id', -1, 'int');
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();

		if($user->id != $order['buyer_id'])
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NOT_OWNER');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');
			$app->enqueueMessage($message, 'error');
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

					if(empty($deal))
					{
						$item['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					}
					else
					{
						$item['deal_name'] = $deal['name'];

						// If the deal is tipped and the order is paid, user is able to view the coupons
						if($deal['tipped'] == 1 && $order['status'] == 1)
						{
							$item['coupons'] = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($item['id']);
						}
					}
				}

				$items[$key] = $item;
			}
		}

		$this->assignRef('configuration', $configuration);
		$this->assignRef('order', $order);
		$this->assignRef('items', $items);
		$pageTitle = JText::_('COM_CMGROUPBUYING_ORDER_INFO_PAGE_TITLE');
		$this->assignRef('pageTitle', $pageTitle);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "order";
		parent::display($tpl);
	}
}