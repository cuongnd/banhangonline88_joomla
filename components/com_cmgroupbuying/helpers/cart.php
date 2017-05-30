<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperCart
{
	public static function update_cart()
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('exchange_rate, point_system, pay_with_point');
		$return = array();
		$return['redirect_url'] = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
		$return['type'] = 'error';
		$session = JFactory::getSession();
		$cart = $session->get('cart', array(), 'CMGroupBuying');

		if(empty($cart) || empty($cart['items']))
		{
			$return['error'] = true;
			$return['message'] = JText::_('COM_CMGROUPBUYING_CART_IS_EMPTY');
			return $return;
		}

		$cartItems = $cart['items'];
		$data = $_POST;
		$points = JFactory::getApplication()->input->get('points', 0, 'int');
		$items = array();
		$cartValue = 0;
		$cartShippingCost = 0;

		// Just to ensure
		if(!is_numeric($points) || $points < 0) 
			$points = 0;

		$exchangeRate = $configuration['exchange_rate'];
		$pointToCurrency = $points * $exchangeRate;

		foreach($data as $key=>$quantity)
		{
			if(strpos($key, 'quantity_') === 0)
			{
				$temp = explode('_', $key);

				if(isset($temp[1]) && isset($temp[2]))
				{
					$dealId = $temp[1];
					$optionId = $temp[2];
					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
					$options = JModelLegacy::getInstance('DealOption','CMGroupBuyingModel')->getOptionsForCart($dealId);
					$today = CMGroupBuyingHelperDateTime::getCurrentDateTime();

					// If deal or option is not found,
					// or deal is unpublished or pending
					// or deal is voided or not active
					// or this item's quantity is 0
					// this deal is not added to new array for items
					// It will be removed in the next foreach
					if(empty($deal) || $deal['published'] == 0 || $deal['approved'] == 0)
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_CMGROUPBUYING_ADD_TO_CART_NO_DEAL_FOUND_MESSAGE'), 'error');
					}
					elseif($deal['voided'] == 1)
					{
						$message = JText::sprintf('COM_CMGROUPBUYING_CART_VOIDED_DEAL_MESSAGE', $deal['name']);
						JFactory::getApplication()->enqueueMessage($message, 'error');
					}
					elseif($deal['start_date'] > $today)
					{
						$message = JText::sprintf('COM_CMGROUPBUYING_CART_UPCOMING_DEAL_MESSAGE', $deal['name']);
						JFactory::getApplication()->enqueueMessage($message, 'error');
					}
					elseif($deal['end_date'] < $today)
					{
						$message = JText::sprintf('COM_CMGROUPBUYING_CART_EXPIRED_DEAL_MESSAGE', $deal['name']);
						JFactory::getApplication()->enqueueMessage($message, 'error');
					}
					elseif(!isset($options[$temp[2]]))
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_CMGROUPBUYING_CART_OPTION_NOT_FOUND'), 'error');
					}
					elseif($quantity <= 0)
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_CMGROUPBUYING_ORDER_QUANTITY_INVALID'), 'error');
					}
					elseif(isset($data['remove_' . $temp[1] . '_' . $temp[2]])
							&& $data['remove_' . $temp[1] . '_' . $temp[2]] == 'on')
					{
						// If this item is checked for removal
						// It will be removed in the next foreach
					}
					else
					{
						// Validation and calculate total value of cart

						// Check limit purchasing for each user
						if($deal['max_bought'] != -1)
						{
							// Only check for registered users
							$user = JFactory::getUser();

							if(!$user->guest)
							{
								$pastCoupons = CMGroupBuyingHelperDeal::getCouponsOfUserForDeal($user->get('id'), $deal['id']);
								$coupons = $pastCoupons + $quantity;

								if($deal['max_bought'] < $coupons)
								{
									$return['error'] = true;
									$return['message'] = JText::_('COM_CMGROUPBUYING_UPDATE_CART_LIMIT_BOUGHT_MESSAGE');
									$return['type'] = 'error';
									return $return;
								}
							}
						}

						// Check limit coupon
						if($deal['max_coupon'] != -1)
						{
							$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);

							if($paidCoupons >= $deal['max_coupon'])
							{
								$return['error'] = true;
								$return['message'] = JText::_('COM_CMGROUPBUYING_CHECK_OUT_NOT_AVAILABLE_DEAL_MESSAGE');
								$return['type'] = 'error';
								return $return;
							}

							$coupons = $paidCoupons + $quantity;
							$availableCoupons = $deal['max_coupon'] - $paidCoupons;

							if($deal['max_coupon'] < $coupons)
							{
								if($availableCoupons == 1)
								{
									$message = JText::sprintf('COM_CMGROUPBUYING_CHECK_OUT_LIMIT_COUPON_SINGULAR_MESSAGE', $availableCoupons);
									JFactory::getApplication()->enqueueMessage($message, 'error');
								}
								elseif($availableCoupons > 1)
								{
									$message = JText::sprintf('COM_CMGROUPBUYING_CHECK_OUT_LIMIT_COUPON_PLURAL_MESSAGE', $availableCoupons);
									JFactory::getApplication()->enqueueMessage($message, 'error');
								}
							}
						}

						$option = JModelLegacy::getInstance('DealOption','CMGroupBuyingModel')->getOption($deal['id'], $optionId);

						if($deal['advance_payment'] == 1)
						{
							$unitPrice = $option['advance_price'];
							$remainPrice = $option['price'] - $option['advance_price'];
						}
						else
						{
							$unitPrice = $option['price'];
							$remainPrice = 0;
						}

						// Everything is fine, add this deal to new array of items
						$item = array();
						$item['deal_id'] = $dealId;
						$item['deal_name'] = $deal['name'];
						$item['option_id'] = $optionId;
						$item['option_name'] = $option['name'];
						$item['shipping_cost'] = $deal['shipping_cost'];
						$item['unit_price'] = $unitPrice;
						$item['quantity'] = $quantity;
						$item['remain_price'] = $remainPrice;
						$cart['deals'][$dealId] = $deal;
						$items[] = $item;

						$dealValue = $quantity * $unitPrice;

						if($deal['shipping_cost'] > 0)
						{
							$shippingValue = $quantity * $deal['shipping_cost'];
						}
						else
						{
							$shippingValue = 0;
						}

						$cartValue = $cartValue + $dealValue + $shippingValue;
						$cartShippingCost = $cartShippingCost + $shippingValue;
					}
				}
			}
		}

		foreach($cartItems as $key=>$cartItem)
		{
			$dealId = $cartItem['deal_id'];
			$optionId = $cartItem['option_id'];
			$found = false;

			foreach($items as $item)
			{
				if($item['deal_id'] == $dealId && $item['option_id'] == $optionId)
				{
					$found = true;
					$cartItems[$key]['quantity'] = $item['quantity'];
					$cartItems[$key]['remain_price'] = $item['remain_price'];
				}
			}

			if(!$found)
			{
				// Remove deal from cart
				unset($cartItems[$key]);
			}
		}

		// If we use point system
		if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
				&& $configuration['pay_with_point'] == 1)
		{
			$userPoints = 0;
			$userId = JFactory::getUser()->id;

			if($pointToCurrency < 0)
			{
				$return['error'] = true;
				$return['message'] = JText::_('COM_CMGROUPBUYING_INVALID_POINT');
				return $return;
			}

			if($configuration['point_system'] == "aup")
			{
				$userPoints = CMGroupBuyingHelperAlphauserpoints::getUserPoints($userId);
			}
			elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
			{
				$userPoints = CMGroupBuyingHelperJomsocial::getUserPoints($userId);
			}

			if($points > $userPoints)
			{
				$return['error'] = true;
				$return['message'] = JText::_('COM_CMGROUPBUYING_NOT_ENOUGH_POINT');
				return $return;
			}

			if($pointToCurrency > $cartValue)
			{
				$return['error'] = true;
				$return['message'] = JText::_('COM_CMGROUPBUYING_SO_MANY_POINT');
				return $return;
			}
		}

		$cart['items'] = $cartItems;
		$cart['total_value'] = $cartValue - $pointToCurrency;
		$cart['total_shipping_cost'] = $cartShippingCost;
		$cart['points'] = $points;
		$session->set('cart', $cart, 'CMGroupBuying');

		$return['error'] = false;
		$return['message'] = JText::_('COM_CMGROUPBUYING_UPDATE_CART_SUCCESSFULLY');
		$return['type'] = '';
		return $return;
	}
}
