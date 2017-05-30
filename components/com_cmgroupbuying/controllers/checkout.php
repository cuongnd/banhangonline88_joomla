<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingControllerCheckOut extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	function display($cachable = false, $urlparams = false)
	{
	}

	public function checkout()
	{
		$cartRedirectUrl	= CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
		$app				= JFactory::getApplication();
		$jinput				= $app->input;
		$return				= CMGroupBuyingHelperCart::update_cart();
		$profileSetting		= JModelLegacy::getInstance('Profile', 'CMGroupBuyingModel')->getProfileSettings();
		$session			= JFactory::getSession();
		$cart				= $session->get('cart', array(), 'CMGroupBuying');

		if(empty($cart) || empty($cart['items']))
		{
			$message = JText::_('COM_CMGROUPBUYING_CART_IS_EMPTY');
			$this->setRedirect($cartRedirectUrl, $message, 'error');
			$this->redirect();
		}

		if($return['error'])
		{
			$this->setRedirect($return['redirect_url'], $return['message'], $return['type']);
			$this->redirect();
		}

		// Prepare order's data
		$buyerInfo				= array();
		$buyerInfo['name']		= $jinput->get('name', '', 'string');
		$buyerInfo['first_name']= $jinput->get('firstname', '', 'string');
		$buyerInfo['last_name']	= $jinput->get('lastname', '', 'string');
		$buyerInfo['address']	= $jinput->get('address', '', 'string');
		$buyerInfo['city']		= $jinput->get('city', '', 'string');
		$buyerInfo['state']		= $jinput->get('state', '', 'string');
		$buyerInfo['zip_code']	= $jinput->get('zip', '', 'string');
		$buyerInfo['phone']		= $jinput->get('phone', '', 'string');
		$buyerInfo['email']		= $jinput->get('email', '', 'string');

		$friendInfo					= array();
		$friendInfo['full_name']	= $jinput->get('friend_full_name', '', 'string');
		$friendInfo['email']		= $jinput->get('friend_email', '', 'string');

		$formCache				= array();
		$formCache['buyer']		= $buyerInfo;
		$formCache['friend']	= $friendInfo;
		$session->set('form', $formCache, 'CMGroupBuying');

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('buy_as_guest, exchange_rate, point_system, pay_with_point, deal_referral,
				jomsocial_activity, jomsocial_activity_title, payment_method_type, direct_payment_method');
		$user = JFactory::getUser();

		if($user->guest && $configuration['buy_as_guest'] == 0)
		{
			$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
			$this->setRedirect($cartRedirectUrl, $message, 'error');
			$this->redirect();
		}

		// Form validation
		$error = false;

		// Terms of Service
		$tos = $jinput->get('tos', 0, 'int');

		if($profileSetting['profile_name_attribute'] == 'required' && $buyerInfo['name'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_NAME');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_firstname_attribute'] == 'required' && $buyerInfo['first_name'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_FIRSTNAME');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_lastname_attribute'] == 'required' && $buyerInfo['last_name'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_LASTNAME');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_address_attribute'] == 'required' && $buyerInfo['address'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_ADDRESS');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_city_attribute'] == 'required' && $buyerInfo['city'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_CITY');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_state_attribute'] == 'required' && $buyerInfo['state'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_STATE');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_zip_attribute'] == 'required' && $buyerInfo['zip_code'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_ZIP');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($profileSetting['profile_phone_attribute'] == 'required' && $buyerInfo['phone'] == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_PHONE');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if(CMGroupBuyingHelperMail::validEmail($buyerInfo['email']) == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_EMAIL');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($friendInfo['full_name'] == "" && $friendInfo['email'] != "")
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_FRIEND_FULL_NAME');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($friendInfo['full_name'] != "" && CMGroupBuyingHelperMail::validEmail($friendInfo['email']) == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_INVALID_FRIEND_EMAIL');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($tos != 1)
		{
			$message = JText::_('COM_CMGROUPBUYING_AGREE_TOS');
			$app->enqueueMessage($message, 'error');
			$error = true;
		}

		if($error)
		{
			$app->redirect($cartRedirectUrl);
		}

		$createdDate	= CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$cartValue		= $cart['total_value'];
		$cartPoints		= $cart['points'];
		$referrer		= $cart['referrer'];
		$exchangeRate	= $configuration['exchange_rate'];
		$pointToCurrency= $cartPoints * $exchangeRate;
		$db				= JFactory::getDBO();
		$query			= $db->getQuery(true);

		// New order
		$__data =					new stdClass();
		$__data->value				= $cart['total_value'];
		$__data->buyer_id			= JFactory::getUser()->id;
		$__data->buyer_info			= json_encode($buyerInfo);
		$__data->friend_info		= json_encode($friendInfo);
		$__data->payment_id			= '';
		$__data->payment_name		= '';
		$__data->transaction_info	= '';
		$__data->created_date		= $createdDate;
		$__data->expired_date		= '';
		$__data->paid_date			= '';
		$__data->status				= 0;
		$__data->referrer			= $referrer;
		$__data->points				= 0;
		$__data->transaction_id		= '';

		$db->insertObject('#__cmgroupbuying_orders', $__data, 'id');

		if($db->getErrorNum()) // Can't create new order
		{
			$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
			$this->setRedirect($cartRedirectUrl, $message, 'error');
			$this->redirect();
		}

		$orderId = $__data->id;

		// Free deal
		if($cartValue == 0)
		{
			// If this is a full payment by points
			if($pointToCurrency > 0)
			{
				foreach($cart['items'] as $item)
				{
					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

					$__data = new stdClass();
					$__data->order_id = $orderId;
					$__data->deal_id = $item['deal_id'];
					$__data->option_id = $item['option_id'];
					$__data->unit_price = $item['unit_price'];
					$__data->quantity = $item['quantity'];
					$__data->shipping_cost = $deal['shipping_cost'] * $item['quantity'];
					$__data->token = JFactory::getUser()->id;

					$db->insertObject('#__cmgroupbuying_order_items', $__data, 'id');

					if($db->getErrorNum()) // Can't create a new order
					{
						$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
						$this->setRedirect($cartRedirectUrl, $message, 'error');
						$this->redirect();
					}

					$itemId = $__data->id;
					$expiredDate = $createdDate;
					$token = CMGroupBuyingHelperOrder::generateOrderToken($item['deal_id'], $itemId, $createdDate);
					JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->updateToken($itemId, $token);
					CMGroupBuyingHelperCoupon::createOrderCoupon($orderId, $itemId, $deal['partner_id'], $item['deal_id'], $item['option_id'], JFactory::getUser()->id, $item['quantity'], $expiredDate, 1);

					if($deal['tipped'] == 0)
					{
						CMGroupBuyingHelperDeal::checkDealForTipping($deal);
					}
					elseif($deal['tipped'] == 1)
					{
						$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
						$item['token'] = $token;
						$order['paid_date'] = $createdDate;
						CMGroupBuyingHelperMail::sendCoupon($order, $item);
						// Change order status to Paid because the deal was tipped
						JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 1);
						JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByItemId($itemId, 1);
					}
				}

				// Update payment info for order
				$query->clear();
				$query->update('#__cmgroupbuying_orders');
				$query->set($db->quoteName('payment_id') . ' = ' . $db->quote('-1'));
				$query->set($db->quoteName('payment_name') . ' = ' . $db->quote('Point'));
				$query->set($db->quoteName('points') . ' = ' . $db->quote($cartPoints));
				$query->set($db->quoteName('status') . ' = ' . $db->quote('1'));
				$query->set($db->quoteName('paid_date') . ' = ' . $db->quote($createdDate));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($orderId));
				$db->setQuery($query);
				$db->execute();

				// Point system - Start
				if($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
				{
					if($configuration['pay_with_point'] == 1)
					{
						// Descrease user's points
						if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
						{
							$aupId = CMGroupBuyingHelperAlphauserpoints::getAUPId(JFactory::getUser()->id);
							CMGroupBuyingHelperAlphauserpoints::newPoints($aupId, $cartPoints * -1);
						}
						elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
						{
							CMGroupBuyingHelperJomsocial::newPoints($user->id, $cartPoints * -1);
						}
					}

					if($configuration['deal_referral'] == 1 && $referrer != '')
					{
						// Reward referrer if we have
						if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
						{
							CMGroupBuyingHelperAlphauserpoints::rewardReferral($referrer);
						}
						elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
						{
							CMGroupBuyingHelperJomsocial::rewardReferral($referrer);
						}
					}
				}
				// Point system - End

				// Empty cart
				JFactory::getSession()->clear('cart', 'CMGroupBuying');

				// Send mail
				$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
				CMGroupBuyingHelperMail::sendMailForPaidOrder($order);

				$message = JText::_('COM_CMGROUPBUYING_NEW_POINT_ORDER_SUCCESSFULLY');
				$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');

				$this->setRedirect($redirectUrl, $message);
				$this->redirect();
			}
			// This is a free deal.
			else
			{
				foreach($cart['items'] as $item)
				{
					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

					$__data = new stdClass();
					$__data->order_id = $orderId;
					$__data->deal_id = $item['deal_id'];
					$__data->option_id = $item['option_id'];
					$__data->unit_price = $item['unit_price'];
					$__data->quantity = $item['quantity'];
					$__data->shipping_cost = $item['shipping_cost'] * $item['quantity'];
					$__data->token = JFactory::getUser()->id;

					$db->insertObject('#__cmgroupbuying_order_items', $__data, 'id');

					if($db->getErrorNum()) // Can't create a new order
					{
						$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
						$this->setRedirect($cartRedirectUrl, $message, 'error');
						$this->redirect();
					}

					$itemId = $__data->id;
					$expiredDate = $createdDate;

					// Update payment info for order
					$query->clear();
					$query->update('#__cmgroupbuying_orders');
					$query->set($db->quoteName('payment_id') . ' = ' . $db->quote('-1'));
					$query->set($db->quoteName('payment_name') . ' = ' . $db->quote(''));
					$query->set($db->quoteName('points') . ' = ' . $db->quote($cartPoints));
					$query->set($db->quoteName('status') . ' = ' . $db->quote('1'));
					$query->set($db->quoteName('paid_date') . ' = ' . $db->quote($createdDate));
					$query->where($db->quoteName('id') . ' = ' . $db->quote($orderId));
					$db->setQuery($query);
					$db->execute();

					$token = CMGroupBuyingHelperOrder::generateOrderToken($item['deal_id'], $itemId, $createdDate);
					JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->updateToken($itemId, $token);
					CMGroupBuyingHelperCoupon::createOrderCoupon($orderId, $itemId, $deal['partner_id'], $item['deal_id'], $item['option_id'], JFactory::getUser()->id, $item['quantity'], $expiredDate, 1);

					// Send mail to buyer here
					$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
					$item['token'] = $token;
					$order['paid_date'] = $createdDate;
					CMGroupBuyingHelperMail::sendMailForPaidOrder($order);

					if($deal['tipped'] == 0)
					{
						CMGroupBuyingHelperDeal::checkDealForTipping($deal);
					}
					elseif($deal['tipped'] == 1)
					{
						$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
						$item['token'] = $token;
						$order['paid_date'] = $createdDate;
						CMGroupBuyingHelperMail::sendCoupon($order, $item);
						// Change order status to Paid because the deal was tipped
						JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 1);
						JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByItemId($itemId, 1);
					}
				}

				// Empty cart
				JFactory::getSession()->clear('cart', 'CMGroupBuying');

				$message = JText::_('COM_CMGROUPBUYING_NEW_FREE_ORDER_SUCCESSFULLY');

				if($user->guest && $configuration['buy_as_guest'] == 1)
				{
					$redirectUrl = JRoute::_('index.php');
				}
				else
				{
					$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');
				}

				$this->setRedirect($redirectUrl, $message);
				$this->redirect();
			}
		}
		elseif($cartValue > 0)
		{
			// Check what type of payment we are going to make
			$paymentType = $configuration['payment_method_type'];

			if($paymentType == 'direct')
				$paymentId = $configuration['direct_payment_method'];
			else
				// Get payment gateway information from database
				$paymentId = $jinput->get('payment_id', 0, 'string');

			$payment = CMGroupBuyingHelperPlugin::getPaymentPluginById($paymentId);

			if(empty($payment))
			{
				$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_SELECT_PAYMENT_METHOD');
				$this->setRedirect($cartRedirectUrl, $message, 'error');
				$this->redirect();
			}

			if($payment['type'] == 'direct')
			{
				// Direct payment should be made immediately, however we set expiration time in 30 mins
				$payment['lock_time'] = 30; 
			}
			else
			{
				$payment['lock_time'] = (int)$payment['lock_time'];

				if($payment['lock_time'] <= 0)
					$payment['lock_time'] = 10;
			}

			foreach($cart['items'] as $item)
			{
				$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

				$__data = new stdClass();
				$__data->order_id = $orderId;
				$__data->deal_id = $item['deal_id'];
				$__data->option_id = $item['option_id'];
				$__data->unit_price = $item['unit_price'];
				$__data->quantity = $item['quantity'];
				$__data->shipping_cost = $deal['shipping_cost'] * $item['quantity'];
				$__data->token = JFactory::getUser()->id;

				$db->insertObject('#__cmgroupbuying_order_items', $__data, 'id');

				if($db->getErrorNum()) // Can't create a new order
				{
					$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
					$this->setRedirect($cartRedirectUrl, $message, 'error');
					$this->redirect();
				}

				$itemId = $__data->id;
				$expiredDate = $createdDate;
				$token = CMGroupBuyingHelperOrder::generateOrderToken($item['deal_id'], $itemId, $createdDate);
				JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->updateToken($itemId, $token);
				CMGroupBuyingHelperCoupon::createOrderCoupon($orderId, $itemId, $deal['partner_id'], $item['deal_id'], $item['option_id'], JFactory::getUser()->id, $item['quantity'], $expiredDate, 0);

				// JomSocial activity
				if($configuration['jomsocial_activity'] == "1")
				{
					CMGroupBuyingHelperJomsocial::postActivity($deal, $configuration['jomsocial_activity_title']);
				}
			}

			// Point system - Start
			if($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
			{
				if($configuration['pay_with_point'] == 1 && $pointToCurrency > 0)
				{
					// Descrease user's points
					if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
					{
						$aupId = CMGroupBuyingHelperAlphauserpoints::getAUPId(JFactory::getUser()->id);
						CMGroupBuyingHelperAlphauserpoints::newPoints($aupId, $cartPoints * -1);
					}
					elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
					{
						CMGroupBuyingHelperJomsocial::newPoints($user->id, $cartPoints * -1);
					}
				}

				if($configuration['deal_referral'] == 1 && $referrer != '')
				{
					// Reward referrer if we have
					if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
					{
						CMGroupBuyingHelperAlphauserpoints::rewardReferral($referrer);
					}
					elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
					{
						CMGroupBuyingHelperJomsocial::rewardReferral($referrer);
					}
				}
			}
			// Point system - End

			$expiredDate = new JDate($createdDate . ' + ' . $payment['lock_time'] . ' minutes');
			$expiredDate = $expiredDate->__toString();

			// Update payment info for order
			$query->clear();
			$query->update('#__cmgroupbuying_orders');
			$query->set($db->quoteName('points') . ' = ' . $db->quote($cartPoints));
			$query->set($db->quoteName('payment_id') . ' = ' . $db->quote($payment['id']));
			$query->set($db->quoteName('payment_name') . ' = ' . $db->quote($payment['name']));
			$query->set($db->quoteName('expired_date') . ' = ' . $db->quote($expiredDate));
			$query->where('id = ' . $db->quote($orderId));
			$db->setQuery($query);
			$db->execute();

			// Store in the cart to reuse in the view
			$cart['order_id'] = $orderId;
			$cart['buyer_info'] = $buyerInfo;
			$cart['payment_method'] = $payment;
			$session->set('cart', $cart, 'CMGroupBuying');

			if($paymentType == 'hosted')
			{
				// Clear form cache and go to the View to check out.
				// Only applied for hosted payment.
				// We still need form cache for direct payment, in case there is any errors.
				$session->clear('form', 'CMGroupBuying');
				$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=checkout');
				$this->setRedirect($redirectUrl);
				$this->redirect();
			}
			else
			{
				$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
				jimport('joomla.plugin.helper');
				JPluginHelper::importPlugin('cmpayment');
				$result = JFactory::getApplication()->triggerEvent('onCMPaymentNew', array($cart, $order));

				foreach($result as $r)
				{
					if($r === false)
						continue;

					$result2 = $r;
				}

				if($result2['success'])
				{
					// Clear form cache.
					$session->clear('form', 'CMGroupBuying');
					// Empty the cart
					$session->clear('cart', 'CMGroupBuying');

					$plugin = JPluginHelper::getPlugin('cmpayment', $cart['payment_method']['id']);
					$params = new JRegistry;
					$params->loadString($plugin->params);
					$redirectUrl = $params->get('return_url', JURI::root());
					$message = JText::_('COM_CMGROUPBUYING_SUCCESSFUL_TRANSACTION');
					$this->setRedirect($redirectUrl, $message);
					$this->redirect();
				}
				else
				{
					$this->setRedirect($cartRedirectUrl, $result2['message'], 'error');
					$this->redirect();
				}
			}
		}
	}
}
