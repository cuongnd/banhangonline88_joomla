<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskUsersAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @param   string  $task  Action to execute.
	 *
	 * @return  void
	 */
	static public function addToolbar($task)
	{
		switch ($task)
		{
			case "new":
			case "edit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-users');
				JToolBarHelper::save('users_save');
				JToolBarHelper::cancel('users');
				break;

			case "fieldsnew":
			case "fieldsedit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-users');
				JToolBarHelper::save('users_fieldssave');
				JToolBarHelper::cancel('users_fields');
				break;

			case "fields":
				JToolBarHelper::title(JText::_('users_cfield'), 'sc-customfield');
				JToolBarHelper::addNew('users_fieldsnew', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('users_fieldsedit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'users_fieldsremove', 'JTOOLBAR_DELETE');
				break;

			default:
				JToolBarHelper::title(JText::_('act_types_manager'), 'sc-users');
				JToolBarHelper::editList('users_edit', 'JTOOLBAR_EDIT');
				break;
		}
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument($task)
	{
		$document = JFactory::getDocument();
		switch ($task)
		{
			case "new":
			case "edit":
				$document->setTitle(JText::_('EDIT'));
				break;

			case "fieldsnew":
			case "fieldsedit":
				$document->setTitle(JText::_('EDIT'));
				break;

			case "fields":
				$document->setTitle(JText::_('users_cfield'));
				break;

			default:
				$document->setTitle(JText::_('USERS_MANAGER'));
				break;
		}
	}
}