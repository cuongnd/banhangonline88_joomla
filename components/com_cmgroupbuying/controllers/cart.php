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

class CMGroupBuyingControllerCart extends JControllerLegacy
{
	public function empty_cart()
	{
		$session = JFactory::getSession();
		$session->clear('cart', 'CMGroupBuying');
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
		$this->setRedirect($redirectUrl, $message, $type);
		$this->redirect();
	}

	public function update_cart()
	{
		$return = CMGroupBuyingHelperCart::update_cart();
		$message = $return['message'];
		$redirectUrl = $return['redirect_url'];
		$type = $return['type'];
		$this->setRedirect($redirectUrl, $message, $type);
		$this->redirect();
	}

	function add_to_cart()
	{
		$jinput = JFactory::getApplication()->input;
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('point_system, deal_referral');
		$dealId = $jinput->get('id', 0, 'int');
		$optionId = $jinput->get('option_id', 0, 'int');
		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
		$referrer = $jinput->cookie->get($dealId, 0, 'string');
		$session = JFactory::getSession();
		$cart = $session->get('cart', array(), 'CMGroupBuying');

		// If cart doesn't exist
		if(empty($cart))
		{
			$cart['items'] = array();
			$cart['points'] = 0;
			$cart['total_value'] = 0;
			$cart['total_shipping_cost'] = 0;
			$cart['order_id'] = 0;
			$cart['buyer_info'] = array();
			$cart['payment_method'] = array();
			$cart['referrer'] = '';
		}

		// If there is no deal having the provided id
		if(empty($deal) || !is_numeric($optionId) || $optionId < 0)
		{
			// Take buyer to Active Deals page
			$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_NO_DEAL_FOUND_MESSAGE');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=alldeals');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}
		else
		{
			// If deal is not published or is pending
			if($deal['published'] == 0 || $deal['approved'] == 0)
			{
				// Take buyer to Active Deals page
				$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_NO_DEAL_FOUND_MESSAGE');
				$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=alldeals');
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();
			}

			// Check for valid deal
			$today = CMGroupBuyingHelperDateTime::getCurrentDateTime();
			$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);

			if($deal['voided'] == 1)
			{
				$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_VOIDED_DEAL_MESSAGE');
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();

			} 
			elseif($deal['max_coupon'] != -1 && $paidCoupons >= $deal['max_coupon'])
			{
				$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_NOT_AVAIL_DEAL_MESSAGE');
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();
			}
			elseif($deal['start_date'] > $today)
			{
				$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_UPCOMING_DEAL_MESSAGE');
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();
			}
			elseif($deal['end_date'] < $today)
			{
				$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_EXPIRED_DEAL_MESSAGE');
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();
			}

			if($deal['max_bought'] != -1)
			{
				// Check limit purchasing for each user
				// Only check for registered users
				$user = JFactory::getUser();

				if(!$user->guest)
				{
					$pastCoupons = CMGroupBuyingHelperDeal::getCouponsOfUserForDeal($user->get('id'), $deal['id']);

					if($deal['max_bought'] <= $pastCoupons)
					{
						$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_LIMIT_BOUGHT_MESSAGE');
						$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
						$this->setRedirect($redirectUrl, $message, 'error');
						$this->redirect();
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

			$new = true;
			$k = -1; // The key of existed item in cart

			if(empty($cart['items']))
			{
				$new = true;
				$cart['total_shipping_cost'] = 0;
			}
			else
			{
				foreach($cart['items'] as $key=>$item)
				{
					// This item exists in cart
					if($item['deal_id'] == $dealId && $item['option_id'] == $optionId)
					{
						$new = false;
						$k = $key;
					}
				}
			}

			if($new)
			{
				// Add the deal to cart
				$item = array();
				$item['deal_id'] = $dealId;
				$item['deal_name'] = $deal['name'];
				$item['option_id'] = $optionId;
				$item['option_name'] = $option['name'];
				$item['shipping_cost'] = $deal['shipping_cost'];
				$item['unit_price'] = $unitPrice;
				$item['remain_price'] = $remainPrice;
				$item['quantity'] = 1;

				if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial") && $configuration['deal_referral'] == 1)
				{
					$loggedInUserId = '';

					// Check if referrer and buyer are only 1 person
					if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
					{
						$loggedInUserId = CMGroupBuyingHelperAlphauserpoints::getAUPId(JFactory::getUser()->id);
					}
					elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
					{
						$loggedInUserId = JFactory::getUser()->id;
					}

					if($loggedInUserId != $referrer)
					{
						$cart['referrer'] = $referrer;
					}
				}

				$cart['items'][] = $item;
			}
			else
			{
				$cart['items'][$k]['quantity']  += 1;
			}

			$cart['total_shipping_cost'] += $item['shipping_cost'];
			$cart['order_id'] = 0;
			$cart['buyer_info'] = array();
			$cart['payment_method'] = array();

			$session->set('cart', $cart, 'CMGroupBuying');

			// Take user to shopping cart page
			$message = JText::_('COM_CMGROUPBUYING_ADD_TO_CART_DEAL_ADDED_MESSAGE');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
		}

		$this->setRedirect($redirectUrl, $message, $type);
		$this->redirect();
	}
}