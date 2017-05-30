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
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JLiveChatControllerTroubleshoot extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

		$this->checkPlugin();

		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

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
		WHERE element = 'jlivechat'
		AND type = 'plugin';";

		$db->setQuery($sql);

		return $db->query();
	}
}