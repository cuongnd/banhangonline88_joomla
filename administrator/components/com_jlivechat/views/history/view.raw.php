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

jimport('joomla.application.component.view');

class JLiveChatViewHistory extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$history = & CMSModel::getInstance('HistoryAdmin', 'JLiveChatModel');
		$operators = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

		$this->assign('language_code', $settings->getDefaultLanguage());
		$this->assign('departments', $operators->getDepartments());
		$this->assign('operators', $operators->getAllInitializedOperators());

		parent::display($tpl);
	}

	function displayChatContents($chatSessionId)
	{
		$model = & $this->getModel('HistoryAdmin', 'JLiveChatModel');

		$content = $model->getSessionContent($chatSessionId);

		$this->assignRef('content', $content);

		$tpl = 'session_contents';

		parent::display($tpl);
	}
}