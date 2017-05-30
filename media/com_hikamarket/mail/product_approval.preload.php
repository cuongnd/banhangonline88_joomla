<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$vendor_name = $data->vendor->vendor_name;
$product_url = hikashop_frontendLink('index.php?option=com_hikashop&ctrl=product&task=show&cid='.$data->product->product_id);

$vars = array(
	'LIVE_SITE' => HIKASHOP_LIVE,
	'PRODUCT_URL' => $product_url,
	'product' => $data->product,
	'vendor' => $data->vendor
);
$texts = array(
	'MAIL_TITLE' => JText::_('HIKAM_EMAIL_PRODUCT_APPROVAL'),
	'MAIL_HEADER' => JText::_('HIKAMARKET_MAIL_HEADER'),
	'HI_VENDOR' => JText::sprintf('HI_VENDOR', $vendor_name),
);
