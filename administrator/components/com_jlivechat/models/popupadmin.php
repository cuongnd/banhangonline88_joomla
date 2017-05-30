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

jimport('joomla.application.component.model');

class JLiveChatModelPopupAdmin extends CMSModel
{
	var $_settings = null;

	function __construct()
	{
		$this->JLiveChatModelPopupAdmin();
	}

	function JLiveChatModelPopupAdmin()
	{
		parent::__construct();

		$this->_settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');
	}

	function getWindowTitle($language)
	{
		$titles = $this->_settings->getSetting('window_titles');

		if(isset($titles->$language)) return $titles->$language;

		return JText::_('WINDOW_TITLE_DEFAULT');
	}

	function getWindowIntro($language)
	{
		$intros = $this->_settings->getSetting('window_intros');

		if(isset($intros->$language)) return $intros->$language;

		return '<p>'.JText::_('WINDOW_INTRO_MSG').'</p>';
	}

	function getWindowOffline($language)
	{
		$offlineMessages = $this->_settings->getSetting('offline_messages');

		if(isset($offlineMessages->$language)) return $offlineMessages->$language;

		return '<p>'.JText::_('WINDOW_OFFLINE_MSG').'</p>';
	}
}