<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	30 September 2014
 * @file name	:	modules/mod_jblancescript/mod_jblancescript.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
// no direct access
defined('_JEXEC') or die('Restricted access'); 

$code = $params->get('code', '');

if($params->def('prepare_content', 1)){
	JPluginHelper::importPlugin('content');
	$code = JHtml::_('content.prepare', $code, '', 'mod_jblancescript.code');
}

require(JModuleHelper::getLayoutPath('mod_jblancescript'));