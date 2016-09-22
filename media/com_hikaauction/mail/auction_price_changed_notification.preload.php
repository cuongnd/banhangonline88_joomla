<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

global $Itemid;
$url_itemid = '';
if(!empty($Itemid))
	$url_itemid = '&Itemid=' . $Itemid;

$texts = array(
	'MAIL_HEADER' => JText::_('HIKASHOP_MAIL_HEADER'),
	'MAIL_TITLE' => JText::_('AUCTION_NEW_BID_EMAIL_TITLE'),
	'MAIL_MESSAGE' => JText::sprintf('AUCTION_NEW_BID_EMAIL_MESSAGE', $data->product->product_name,$data->price),
	'HI_USER' => JText::sprintf('HI_CUSTOMER',@$data->user->username),
	'ORDER_END_MESSAGE' => JText::sprintf('BEST_REGARDS_CUSTOMER',$mail->from_name)
);

$front_product_url = hikashop_frontendLink('product&task=show&cid[]='.$data->product->product_id . $url_itemid);

$vars = array(
	'LIVE_SITE' => HIKASHOP_LIVE,
	'URL' => HIKASHOP_LIVE,
);
