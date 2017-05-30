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

$vars = array(
	'LIVE_SITE' => HIKASHOP_LIVE,
	'PACK_URL'=> HIKASERIAL_LIVE.'administrator/index.php?option=com_hikaserial&ctrl=pack&task=edit&cid='.(int)$data->pack_id,
	'current_quantity' => $data->current_quantity,
	'pack_name' => $data->pack_name,
);
$texts = array(
	'MAIL_TITLE' => JText::sprintf('PACK_STOCK_LEVEL_LOW_EMAIL_SUBJECT', $data->pack_name),
	'MAIL_HEADER' => JText::_('HIKASERIAL_MAIL_HEADER'),
);

if(empty($mail->subject))
	$mail->subject = $texts['MAIL_TITLE'];
