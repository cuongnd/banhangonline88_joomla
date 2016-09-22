<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$name = 'Technology';
$thumb = 'media/com_acymailing/templates/technology_resp/thumb.jpg';
$body = JFile::read(dirname(__FILE__).DS.'index.html');

$styles['tag_h1'] = 'font-size:20px; margin:0px; margin-bottom:15px; padding:0px; font-weight:bold; color:#01bbe5 !important;';
$styles['tag_h2'] = 'font-size:12px; font-weight:bold; color:#565656 !important; text-transform:uppercase; margin:10px 0px; padding:0px; padding-bottom:5px; border-bottom:1px solid #ddd;';
$styles['tag_h3'] = 'color:#565656 !important; font-weight:bold; font-size:12px; margin:0px; margin-bottom:10px; padding:0px;';
$styles['tag_h4'] = '';
$styles['color_bg'] = '#575757';
$styles['tag_a'] = 'cursor:pointer;color:#01bbe5;text-decoration:none;border:none;';
$styles['acymailing_online'] = 'color:#d2d1d1; cursor:pointer;';
$styles['acymailing_unsub'] = 'color:#d2d1d1; cursor:pointer;';
$styles['acymailing_readmore'] = 'cursor:pointer; font-weight:bold; color:#fff; background-color:#01bbe5; padding:2px 5px;';


$stylesheet = 'table, div, p {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
p{margin:0px; padding:0px}

.special h2{font-size:18px;
	margin:0px;
	margin-bottom:15px;
	padding:0px;
	font-weight:bold;
	color:#01bbe5 !important;
	text-transform:none;
	border:none}

.links a{color:#ababab}

@media (max-width:450px){
	table[class=w600], td[class=w600], table[class=w540], td[class=w540], img[class="w600"], img[class="w540"]{ width:100% !important;}
	td[class=w30] { width:20px !important;}
	.pict img {max-width:260px}
}

@media (min-width: 450px) and (max-width: 600px){
	table[class=w600], td[class=w600], table[class=w540], td[class=w540], img[class="w600"], img[class="w540"]{ width:100% !important;}
	td[class=w30] { width:20px !important;}
	.pict img {max-width:460px}
}

@media (min-width:600px){
	body {width:600px !important; margin:auto !important;}
	.pict img {max-width:540px !important;  height:auto !important;}
}
';




