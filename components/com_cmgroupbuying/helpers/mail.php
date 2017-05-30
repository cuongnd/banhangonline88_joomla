<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperMail
{

	public static function validEmail($email)
	{
		if(empty($email) || !is_string($email))
		{
			return false;
		}

		if(!preg_match('/^([a-z0-9_\'&\.\-\+])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i',$email))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function prepareVariableArray($order = array(), $deal = array(), $templateName = '')
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration('datetime_format');
		$variableArray = array();

		if($templateName == 'pay_buyer'):
			$buyerInfo							= json_decode($order['buyer_info']);
			$variableArray['buyer_name']		= $buyerInfo->name;
			$variableArray['buyer_first_name']	= $buyerInfo->first_name;
			$variableArray['buyer_last_name']	= $buyerInfo->last_name;
			$variableArray['buyer_email']		= $buyerInfo->email;
			$variableArray['order_value']		= CMGroupBuyingHelperDeal::displayDealPrice($order['value']);
			$variableArray['order_paid_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['order_id']			= $order['id'];
			$variableArray['order_link']		= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id'], 'index.php?option=com_cmgroupbuying&view=order');

		elseif($templateName == 'pay_partner'):
			$variableArray['partner_name']		= $deal['partner_name'];
			$variableArray['deal_name']			= $deal['name'];
			$variableArray['order_paid_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['order_id']			= $order['id'];

		elseif($templateName == 'coupon_for_buyer'):
			$buyerInfo							= json_decode($order['buyer_info']);
			$friendInfo							= json_decode($order['friend_info']);
			$partner							= JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
			$variableArray['buyer_name']		= $buyerInfo->name;
			$variableArray['buyer_first_name']	= $buyerInfo->first_name;
			$variableArray['buyer_last_name']	= $buyerInfo->last_name;
			$variableArray['buyer_email']		= $buyerInfo->email;
			$variableArray['friend_full_name']	= $friendInfo->full_name;
			$variableArray['friend_email']		= $friendInfo->email;
			$variableArray['deal_name']			= $deal['name'];
			$variableArray['deal_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], 'index.php?option=com_cmgroupbuying&view=todaydeal');
			$variableArray['partner_name']		= $partner['name'];
			$variableArray['order_paid_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['order_id']			= $order['id'];
			$variableArray['coupon_link']		= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=coupon&token=' . $order['item']['token'], 'index.php?option=com_cmgroupbuying&view=todaydeal');
			$variableArray['order_link']		= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id'], 'index.php?option=com_cmgroupbuying&view=order');

		elseif($templateName == 'coupon_for_friend'):
			$buyerInfo							= json_decode($order['buyer_info']);
			$friendInfo							= json_decode($order['friend_info']);
			$partner							= JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
			$variableArray['buyer_name']		= $buyerInfo->name;
			$variableArray['buyer_first_name']	= $buyerInfo->first_name;
			$variableArray['buyer_last_name']	= $buyerInfo->last_name;
			$variableArray['buyer_email']		= $buyerInfo->email;
			$variableArray['friend_full_name']	= $friendInfo->full_name;
			$variableArray['friend_email']		= $friendInfo->email;
			$variableArray['deal_name']			= $deal['name'];
			$variableArray['deal_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], 'index.php?option=com_cmgroupbuying&view=todaydeal');
			$variableArray['partner_name']		= $partner['name'];
			$variableArray['order_paid_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['coupon_link']		= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=coupon&token=' . $order['item']['token'], 'index.php?option=com_cmgroupbuying&view=todaydeal');

		elseif($templateName == 'void_buyer'):
			$buyerInfo							= json_decode($order['buyer_info']);
			$variableArray['buyer_name']		= $buyerInfo->name;
			$variableArray['buyer_first_name']	= $buyerInfo->first_name;
			$variableArray['buyer_last_name']	= $buyerInfo->last_name;
			$variableArray['buyer_email']		= $buyerInfo->email;
			$variableArray['deal_name']			= $deal['name'];
			$variableArray['deal_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], 'index.php?option=com_cmgroupbuying&view=todaydeal');
			$variableArray['order_paid_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['order_id']			= $order['id'];
			$variableArray['order_link']		= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id'], 'index.php?option=com_cmgroupbuying&view=order');

		elseif($templateName == 'void_partner'):
			$variableArray['partner_name']		= $deal['partner_name'];
			$variableArray['deal_name']			= $deal['name'];
			$variableArray['deal_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], 'index.php?option=com_cmgroupbuying&view=todaydeal');

		elseif($templateName == 'late_pay_buyer'):
			$buyerInfo								= json_decode($order['buyer_info']);
			$variableArray['buyer_name']			= $buyerInfo->name;
			$variableArray['buyer_first_name']		= $buyerInfo->first_name;
			$variableArray['buyer_last_name']		= $buyerInfo->last_name;
			$variableArray['buyer_email']			= $buyerInfo->email;
			$variableArray['order_created_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['created_date'], $configuration['datetime_format']);
			$variableArray['order_expired_date']	= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['expired_date'], $configuration['datetime_format']);
			$variableArray['order_paid_date']		= CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']);
			$variableArray['order_id']				= $order['id'];
			$variableArray['order_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id'], 'index.php?option=com_cmgroupbuying&view=order');

		elseif($templateName == 'tip_partner'):
			$variableArray['partner_name']			= $deal['partner_name'];
			$variableArray['deal_name']				= $deal['name'];
			$variableArray['deal_link']				= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], 'index.php?option=com_cmgroupbuying&view=todaydeal');
			$variableArray['deal_tipped_date']		= CMGroupBuyingHelperDateTime::changeDateTimeFormat($deal['tipped_date'], $configuration['datetime_format']);

		elseif($templateName == 'cash_buyer'):
			$buyerInfo								= json_decode($order['buyer_info']);
			$variableArray['buyer_name']			= $buyerInfo->name;
			$variableArray['buyer_first_name']		= $buyerInfo->first_name;
			$variableArray['buyer_last_name']		= $buyerInfo->last_name;
			$variableArray['buyer_email']			= $buyerInfo->email;
			$variableArray['order_id']				= $order['id'];
			$variableArray['order_link']			= CMGroupBuyingHelperCommon::prepareRedirectBackend('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id'], 'index.php?option=com_cmgroupbuying&view=order');
			$variableArray['order_value']			= CMGroupBuyingHelperDeal::displayDealPrice($order['value']);
			$variableArray['order_expired_date']	= $order['expired_date'];

		elseif($templateName == 'cash_admin'):
			$buyerInfo							= json_decode($order['buyer_info']);
			$variableArray['buyer_name']		= $buyerInfo->name;
			$variableArray['buyer_first_name']	= $buyerInfo->first_name;
			$variableArray['buyer_last_name']	= $buyerInfo->last_name;
			$variableArray['buyer_email']		= $buyerInfo->email;
			$variableArray['order_id']			= $order['id'];
			$variableArray['order_value']		= $order['value'];

		elseif($templateName == 'approve_partner'):
			$variableArray['deal_name']		= $deal['name'];
			$variableArray['partner_name']	= $deal['partner_name'];
		endif;

		return $variableArray;
	}

	public static function sendMailTemplate($to, $templateName, $variableArray)
	{
		if(!empty($variableArray))
		{
			$template = JModelLegacy::getInstance('MailTemplate', 'CMGroupBuyingModel')->getMailTemplateByName($templateName);
			$subject = $template['subject'];
			$body = $template['body'];

			foreach($variableArray as $key=>$value)
			{
				$subject = str_replace("{" . $key . "}", $value, $subject);
				$body = str_replace("{" . $key . "}", $value, $body);
			}

			CMGroupBuyingHelperMail::sendMail($to, $subject, $body);
		}
	}

	public static function sendMail($recipient, $subject, $body)
	{
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get('mailfrom'),
			$config->get('fromname')
		);
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->setBody($body);
		$mailer->addRecipient($recipient);
		$mailer->setSender($sender);
		$mailer->Send();
	}

	public static function sendMailForTippedDeal($dealId)
	{
		$paidOrders = CMGroupBuyingHelperOrder::getPaidOrdersByDealId($dealId);

		foreach($paidOrders as $order)
		{
			$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

			foreach($items as $item)
			{
				CMGroupBuyingHelperMail::sendCoupon($order, $item);
				// Change order status to delivered
				// JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($order['id'] , 3);
			}
		}

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_tip_partner');

		if($configuration['mail_tip_partner'] == 1)
		{
			//Send to partner
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
			$deal['partner_name'] = JFactory::getUser($partner['user_id'])->name;
			$to = JFactory::getUser($partner['user_id'])->email;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray(array(), $deal, 'tip_partner');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'tip_partner', $variableArray);
		}
	}

	public static function sendMailForVoidedDeal($dealId)
	{
		$paidOrders = CMGroupBuyingHelperOrder::getPaidOrdersByDealId($dealId);

		foreach($paidOrders as $order)
		{
			CMGroupBuyingHelperMail::sendVoidDealNotification($order);
		}

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_void_partner');

		if($configuration['mail_void_partner'] == 1)
		{
			//Send to partner
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
			$deal['partner_name'] = JFactory::getUser($partner['user_id'])->name;
			$to = JFactory::getUser($partner['user_id'])->email;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray(null, $deal, 'void_partner');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'void_partner', $variableArray);
		}
	}

	public static function sendMailForCashOrder($order)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_cash_buyer, mail_cash_admin, admin_email');

		if($configuration['mail_cash_buyer'] == 1)
		{
			$buyerInfo = json_decode($order['buyer_info']);
			$to = $buyerInfo->email;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, array(), 'cash_buyer');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'cash_buyer', $variableArray);
		}

		if($configuration['mail_cash_admin'] == 1)
		{
			$buyerInfo = json_decode($order['buyer_info']);
			$to = $configuration['admin_email'];
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, array(), 'cash_admin');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'cash_admin', $variableArray);
		}
	}

	public static function sendMailForPaidOrder($order)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_pay_buyer, mail_pay_partner');

		if($configuration['mail_pay_buyer'] == 1)
		{
			// Send to buyer
			$buyerInfo = json_decode($order['buyer_info']);
			$to = $buyerInfo->email;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, array(), 'pay_buyer');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'pay_buyer', $variableArray);
		}

		if($configuration['mail_pay_partner'] == 1)
		{
			//Send to partner
			$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

			foreach($items as $item)
			{
				$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
				$to = JFactory::getUser($partner['user_id'])->email;
				$deal['partner_name'] = JFactory::getUser($partner['user_id'])->name;
				$variableArray  = CMGroupBuyingHelperMail::prepareVariableArray($order, $deal, 'pay_partner');
				CMGroupBuyingHelperMail::sendMailTemplate($to, 'pay_partner', $variableArray);
			}
		}
	}

	public static function sendMailForLatePaidOrder($order)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_pay_buyer');

		if($configuration['mail_pay_buyer'] == 1)
		{
			// Send to buyer
			$buyerInfo = json_decode($order['buyer_info']);
			$to = $buyerInfo->email;
			$deal = CMGroupBuyingHelperDeal::getDealsByOrderId($order['id']);
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, $deal, 'late_pay_buyer');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'late_pay_buyer', $variableArray);
		}
	}

	public static function sendMailForApprovedDeal($dealId)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_approve_partner');

		if($configuration['mail_approve_partner'] == 1)
		{
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
			$to = JFactory::getUser($partner['user_id'])->email;
			$deal['partner_name'] = JFactory::getUser($partner['user_id'])->name;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray(array(), $deal, 'approve_partner');
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'approve_partner', $variableArray);
		}
	}

	public static function sendMailForApprovedFreeCoupon($couponId)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_approve_coupon_partner');

		if($configuration['mail_approve_coupon_partner'] == 1)
		{
			$coupon = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getFreeCouponById($couponId);
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($coupon['partner_id']);
			$userId = $partner['user_id'];
			$to = JFactory::getUser($userId)->email;
			$variableArray['coupon_name'] = $coupon['name'];
			$variableArray['partner_name'] = $partner['name'];
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'approve_coupon_partner', $variableArray);
		}
	}

	public static function sendMailForNewDeal($dealName, $partnerName)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_pending_admin, admin_email');

		if($configuration['mail_pending_admin'] == 1)
		{
			$to = $configuration['admin_email'];
			$variableArray['deal_name'] = $dealName;
			$variableArray['partner_name'] = $partnerName;
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'pending_admin', $variableArray);
		}
	}

	public static function sendMailForNewFreeCoupon($couponName, $partnerName)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_pending_coupon_admin, admin_email');

		if($configuration['mail_pending_coupon_admin'] == 1)
		{
			$to = $configuration['admin_email'];
			$variableArray['coupon_name'] = $couponName;
			$variableArray['partner_name'] = $partnerName;
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'pending_coupon_admin', $variableArray);
		}
	}

	public static function sendCoupon($order, $item)
	{
		$buyerInfo = json_decode($order['buyer_info']);
		$friendInfo = json_decode($order['friend_info']);

		if($friendInfo->email != '' && $friendInfo->full_name != '')
		{
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);
			$order['item'] = $item;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, $deal, 'coupon_for_friend');
			$to = $friendInfo->email;
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'coupon_for_friend', $variableArray);
		}
		else
		{
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);
			$order['item'] = $item;
			$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, $deal, 'coupon_for_buyer');
			$to = $buyerInfo->email;
			CMGroupBuyingHelperMail::sendMailTemplate($to, 'coupon_for_buyer', $variableArray);
		}
	}

	public static function sendVoidDealNotification($order)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('mail_void_buyer');

		if($configuration['mail_void_buyer'] == 1)
		{
			$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

			foreach($items as $item)
			{
				// Send mail to buyer
				$buyerInfo = json_decode($order['buyer_info']);
				$to = $buyerInfo->email;
				$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);
				$variableArray = CMGroupBuyingHelperMail::prepareVariableArray($order, $deal, 'void_buyer');
				CMGroupBuyingHelperMail::sendMailTemplate($to, 'void_buyer', $variableArray);
			}
		}
	}
}