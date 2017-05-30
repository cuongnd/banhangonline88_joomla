<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

global $Itemid;
$url_itemid = '';
if(!empty($Itemid))
	$url_itemid = '&Itemid=' . $Itemid;

$userClass = hikashop_get('class.user');
$userClass->get(false);
$userInfos = $userClass->get($data->order->order_user_id);

$customer_name = '';
if(isset($userInfos->name))
	$customer_name = $userInfos->name;
if(isset($data->order->billing_address))
	$customer_name = $data->order->billing_address->address_firstname . ' ' . $data->order->billing_address->address_lastname;

$texts = array(
	'MAIL_TITLE' => JText::_('MAILTOCUSTOMFIELD_EMAIL_TITLE'),
	'MAIL_BEGIN_MESSAGE' => JText::sprintf('MAILTOCUSTOMFIELD_BEGIN_MESSAGE', $customer_name, '<a href="'.HIKASHOP_LIVE.'">'.HIKASHOP_LIVE.'</a>'),
	'USER_MESSAGE' => JText::sprintf('MAILTOCUSTOMFIELD_USER_MESSAGE', $customer_name),
	'HI_USER' => JText::sprintf('HI_CUSTOMER', $data->dest_email),
	'MAIL_END_MESSAGE' => JText::sprintf('BEST_REGARDS_CUSTOMER', $mail->from_name),
);

$vars = array(
	'LIVE_SITE' => HIKASHOP_LIVE,
	'URL' => HIKASHOP_LIVE,
	'order' => $data->order,
	'billing_address' => @$data->order->billing_address,
	'shipping_address' => @$data->order->shipping_address,
	'user' => $userInfos,
	'USER_MESSAGE' => false,
);

$templates = array(
	'SERIAL' => array()
);
foreach($data->serials as $serial) {
	$templates['SERIAL'][] = array(
		'DATA' => $serial->serial_data
	);
}

if(empty($mail->subject))
	$mail->subject = $texts['MAIL_TITLE'];

$mail->from_email = '';
$mail->from_name = '';

$mail->reply_email = '';
$mail->reply_name = '';

$mail->dst_email = $data->dest_email;
$mail->dst_name = '';
if(strpos($data->dest_email, '@') !== false)
	$mail->dst_name = substr($data->dest_email, 0, strpos($data->dest_email, '@'));
