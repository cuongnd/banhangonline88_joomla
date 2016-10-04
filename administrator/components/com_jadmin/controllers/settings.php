<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
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
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JAdminControllerSettings extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

		header('Content-type: text/html; charset=utf-8'); // utf-8 encoding
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$this->checkPlugin();

		$viewName = JRequest::getCmd('view', 'settings');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Get/Create the model
		$model = & $this->getModel('SettingAdmin', 'JAdminModel');

		if($model)
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout('default');

		// Display the view
		parent::display(false);
	}

	function checkPlugin()
	{
		$db = & JFactory::getDBO();

		$sql = "UPDATE #__extensions SET
		    enabled = 1
		WHERE element = 'jadmin'
		AND type = 'plugin';";
		$db->setQuery($sql);

		return $db->query();
	}

	function save()
	{
		JRequest::checkToken() or die('Invalid Token');

		$mainframe = & JFactory::getApplication();

		$siteName = JRequest::getVar('site_name', '', 'method');
		$activityMonitor = JRequest::getInt('activity_monitor_value', 1, 'method');
		$activityMonitorExpiration = JRequest::getInt('activity_monitor_expiration', 180, 'method');
		$useProxy = JRequest::getInt('use_proxy', 0, 'method');
		$useSocks = JRequest::getInt('use_socks', 0, 'method');
		$proxyUri = JRequest::getVar('proxy_uri', null, 'method');
		$proxyPort = JRequest::getInt('proxy_port', null, 'method');
		$proxyAuth = JRequest::getVar('proxy_auth', null, 'method');
		$useGZip = JRequest::getInt('use_gzip', 0, 'method');

		$lang = & JFactory::getLanguage();
		$settings = & CMSModel::getInstance('SettingAdmin', 'JAdminModel');

		$settings->setSetting('site_name', $siteName);
		$settings->setSetting('activity_monitor', $activityMonitor);
		$settings->setSetting('activity_monitor_expiration', $activityMonitorExpiration);
		$settings->setSetting('use_proxy', $useProxy);
		$settings->setSetting('use_socks', $useSocks);
		$settings->setSetting('proxy_uri', $proxyUri);
		$settings->setSetting('proxy_port', $proxyPort);
		$settings->setSetting('proxy_auth', $proxyAuth);
		$settings->setSetting('use_gzip', $useGZip);

		$isError = false;

		if($settings->saveSettings() && !$isError)
		{
			return $mainframe->redirect('index.php?option=com_jadmin&view=settings', JText::_('SETTINGS_SAVED_SUCCESS'));
		}
		else
		{
			return $mainframe->redirect('index.php?option=com_jadmin&view=settings', JText::_('SETTINGS_SAVED_FAIL'), 'error');
		}
	}
}