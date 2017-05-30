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
	'MAIL_TITLE' => JText::_('AUCTION_EMAIL_UPDATE_TITLE'),
	'MAIL_BEGIN_MESSAGE' => JText::_('AUCTION_EMAIL_UPDATED_BEGIN_MESSAGE'),
	'USER_MESSAGE' => JText::_('CONTACT_USER_MESSAGE'),
	'USER' => JText::_('HIKA_USER'),
	'PRODUCT' => JText::_('PRODUCT'),
	'HI_USER' => JText::sprintf('HI_CUSTOMER', ''),
	'FOR_PRODUCT' => JText::sprintf('CONTACT_REQUEST_FOR_PRODUCT', $data->product->product_name),
);

$front_product_url = hikashop_frontendLink('product&task=show&cid[]='.$data->product->product_id . $url_itemid);

$vars = array(
	'LIVE_SITE' => HIKASHOP_LIVE,
	'URL' => HIKASHOP_LIVE,
);
