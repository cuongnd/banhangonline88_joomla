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
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'toolbar.jlivechat.html.php';

$pageTitle = 'JLive! Chat';

$view = JRequest::getCmd('view');
$task = JRequest::getCmd('task');

switch($view)
{
	case 'operators':
		$pageTitle .= ' - ';

		if($task == 'new_operator')
		{
			$pageTitle .= JText::_('NEW_OPERATOR');

			TOOLBAR_livechat::_newOperator();
		}
		elseif($task == 'edit')
		{
			$pageTitle .= JText::_('EDIT_OPERATOR');

			TOOLBAR_livechat::_newOperator();
		}
		else
		{
			$pageTitle .= JText::_('OPERATORS');

			TOOLBAR_livechat::_listOperators();
		}

		break;
	case 'routing':
		if($task == 'new_route')
		{
			TOOLBAR_livechat::_newRoute();

			$pageTitle .= ' - '.JText::_('NEW_ROUTING_RULE');
		}
		elseif($task == 'edit')
		{
			TOOLBAR_livechat::_editRoute();

			$pageTitle .= ' - '.JText::_('EDIT_ROUTING_RULE');
		}
		else
		{
			TOOLBAR_livechat::_listRoutes();

			$pageTitle .= ' - '.JText::_('ROUTING');
		}

		break;
	case 'responses':
		if($task == 'new_response' || $task == 'create_response')
		{
			TOOLBAR_livechat::_newResponse();

			$pageTitle .= ' - '.JText::_('NEW_RESPONSE');
		}
		elseif($task == 'edit' || $task == 'update_response')
		{
			TOOLBAR_livechat::_editResponse();

			$pageTitle .= ' - '.JText::_('EDIT_RESPONSE');
		}
		else
		{
			TOOLBAR_livechat::_listResponses();

			$pageTitle .= ' - '.JText::_('GLOBAL_RESPONSES');
		}

		break;
	case 'history':
		$pageTitle .= ' - '.JText::_('HISTORY');

		TOOLBAR_livechat::_listHistory();
		break;
	case 'settings':
		$pageTitle .= ' - '.JText::_('SETTINGS');

		TOOLBAR_livechat::_listSettings();
		break;
	case 'troubleshoot':
		$pageTitle .= ' - '.JText::_('INFORMATION');

		TOOLBAR_livechat::_troubleshoot();
		break;
	default:
		TOOLBAR_livechat::_DEFAULT();
		break;
}

JToolBarHelper::title($pageTitle, 'livechat');