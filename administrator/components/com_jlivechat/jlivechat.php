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

if(JRequest::getVar('debug', null, 'get'))
{
	ini_set('display_errors', 'On');
	ini_set('error_reporting', E_ALL);
}

$mainframe = & JFactory::getApplication();

$view = JRequest::getCmd('view');

JSubMenuHelper::addEntry(JText::_('OPERATORS'), 'index.php?option=com_jlivechat&view=operators', JRequest::getCmd('view') == 'operators' || !JRequest::getCmd('view'));
JSubMenuHelper::addEntry(JText::_('ROUTING'), 'index.php?option=com_jlivechat&view=routing', JRequest::getCmd('view') == 'routing');
JSubMenuHelper::addEntry(JText::_('RESPONSES'), 'index.php?option=com_jlivechat&view=responses', JRequest::getCmd('view') == 'responses');
JSubMenuHelper::addEntry(JText::_('HISTORY'), 'index.php?option=com_jlivechat&view=history', JRequest::getCmd('view') == 'history');
JSubMenuHelper::addEntry(JText::_('SETTINGS'), 'index.php?option=com_jlivechat&view=settings', JRequest::getCmd('view') == 'settings');
JSubMenuHelper::addEntry(JText::_('INFORMATION'), 'index.php?option=com_jlivechat&view=troubleshoot', JRequest::getCmd('view') == 'troubleshoot');

//    Default View
if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$view.'.php')) $mainframe->redirect('index.php?option=com_jlivechat&view=operators');

// Joomla 3.0+ compatibility
jimport('joomla.application.component.model');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

if(class_exists('JModelLegacy') && !class_exists('CMSModel'))
{
	class CMSModel extends JModelLegacy  
	{
		function __construct()
		{
			$this->CMSModel();
		}

		function CMSModel()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSModel'))
{
	class CMSModel extends JModel  
	{
		function __construct()
		{
			$this->CMSModel();
		}

		function CMSModel()
		{
			parent::__construct();
		}
	}
}

if(class_exists('JControllerLegacy') && !class_exists('CMSController'))
{
	class CMSController extends JControllerLegacy  
	{
		function __construct()
		{
			$this->CMSController();
		}

		function CMSController()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSController'))
{
	class CMSController extends JController 
	{
		function __construct()
		{
			$this->CMSController();
		}

		function CMSController()
		{
			parent::__construct();
		}
	}
}

if(class_exists('JViewLegacy') && !class_exists('CMSView'))
{
	class CMSView extends JViewLegacy  
	{
		function __construct()
		{
			$this->CMSView();
		}

		function CMSView()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSView'))
{
	class CMSView extends JView 
	{
		function __construct()
		{
			$this->CMSView();
		}

		function CMSView()
		{
			parent::__construct();
		}
	}
}

require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$view.'.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'toolbar.jlivechat.php';

$classname = 'JLiveChatController'.$view;

$controller = new $classname();
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();