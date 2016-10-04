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

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'toolbar.jadmin.html.php';

$pageTitle = 'JAdmin!';

$view = JRequest::getCmd('view');
$task = JRequest::getCmd('task');

switch($view)
{
    case 'users':
	$pageTitle .= ' - ';

	if($task == 'new_user')
	{
	    $pageTitle .= JText::_('NEW_USER');
	    
	    TOOLBAR_jadmin::_newUser();
	}
	elseif($task == 'edit')
	{
	    $pageTitle .= JText::_('EDIT_USER');

	    TOOLBAR_jadmin::_newUser();
	}
	else
	{
	    $pageTitle .= JText::_('USERS');

	    TOOLBAR_jadmin::_listUsers();
	}
	
	break;
    
    case 'settings':
	$pageTitle .= ' - '.JText::_('SETTINGS');
	
	TOOLBAR_jadmin::_listSettings();
	break;
    
    case 'information':
	$pageTitle .= ' - '.JText::_('INFORMATION');

	TOOLBAR_jadmin::_troubleshoot();
	break;
    
    default:
	TOOLBAR_jadmin::_DEFAULT();
	break;
}

JToolBarHelper::title($pageTitle, 'jadmin');