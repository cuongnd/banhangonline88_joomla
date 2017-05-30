<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.helper');
jimport('joomla.application.component.model');

$settingsPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'setting.php';

if(file_exists($settingsPath)) require_once $settingsPath;

class modJLiveChatHelper
{
	function isHostedMode()
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if(!is_object($settings)) return false;

		return $settings->isHostedMode();
	}

	function isOnline($specificOperators = null, $specificDepartment = null, $specificRouteId = null)
	{
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');

		if(!is_object($routing)) return false;

		return $routing->isOnline($specificOperators, $specificDepartment, $specificRouteId);
	}

	function getPopupUri($popupMode = null, $specificOperators = null, $specificDepartment = null, $specificRouteId = null)
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if(!is_object($settings)) return false;

		if(empty($popupMode)) $popupMode = $settings->getSetting('popup_mode');

		$hostedMode = modJLiveChatHelper::isHostedMode();

		if($hostedMode)
		{
			// In Hosted Mode
			$popupUri = $hostedMode['hosted_uri'];
		}
		else
		{
			$popupUri = JURI::root(true);
		}

		$popupUri .= '/index.php?option=com_jlivechat&amp;view=popup&amp;tmpl=component&amp;popup_mode='.$popupMode;

		$activeLanguage = $settings->getCurrentLangCode();

		if(!empty($activeLanguage)) $popupUri .= '&amp;lang='.$activeLanguage;

		if(!empty($specificOperators)) $popupUri .= '&amp;operators='.$specificOperators;

		if(!empty($specificDepartment)) $popupUri .= '&amp;department='.urlencode($specificDepartment);

		if(!empty($specificRouteId)) $popupUri .= '&amp;routeid='.(int)$specificRouteId;

		return $popupUri;
	}

	function getDynamicImageUri($imgSize = null, $specificOperators = null, $specificDepartment = null, $specificRouteId = null)
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if(!is_object($settings)) return false;

		$uri = & JFactory::getURI();
		$dateObj = JFactory::getDate();

		if(empty($imgSize)) $imgSize = 'large';

		$hostedMode = modJLiveChatHelper::isHostedMode();

		if($hostedMode)
		{
			// In Hosted Mode
			$imgUri = $hostedMode['hosted_uri'];
		}
		else
		{
			// Not in Hosted Mode
			$imgUri = JURI::root(true);
		}

		$imgUri .= '/index.php?option=com_jlivechat&amp;view=popup&amp;task=display_status_img';
		$imgUri .= '&amp;no_html=1&amp;do_not_log=true&amp;size='.$imgSize;
		$imgUri .= '&amp;t='.$dateObj->toUnix(); // Prevent caching

		if($specificOperators) $imgUri .= '&amp;operators='.$specificOperators;
		if($specificDepartment) $imgUri .= '&amp;department='.urlencode($specificDepartment);
		if($specificRouteId) $imgUri .= '&amp;routeid='.(int)$specificRouteId;

		return $imgUri;
	}

	function getPopupMode($popupMode = null)
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if(!is_object($settings)) return false;

		if(empty($popupMode)) $popupMode = $settings->getSetting('popup_mode');

		return $popupMode;
	}
}