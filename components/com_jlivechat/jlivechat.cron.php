#!/usr/bin/env php
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
// Set flag that this is a parent file
if(isset($_SERVER['REQUEST_METHOD']) || isset($_SERVER['HTTP_HOST'])) die('Access Denied');

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('memory_limit', -1);

set_time_limit(0);

define('_JEXEC', 1);

define('DIRECTORY_SEPARATOR', DIRECTORY_SEPARATOR);

define('JPATH_BASE', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR));

require_once JPATH_BASE.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php';
require_once JPATH_BASE.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe = & JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

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

CMSModel::addIncludePath(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'models');

$syncObj = & CMSModel::getInstance('Sync', 'JLiveChatModel');
$syncObj->startSyncPusher();