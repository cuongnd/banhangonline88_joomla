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

class HelpdeskClientAdminHelper
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
			case "saveuser":
			case "deluser":
			case "savecontract":
			case "delcontract":
			case "publishcontract":
			case "savefile":
			case "delfile":
			case "saveinfo":
			case "delinfo":
			case "publishinfo":
			case "manager":
			case "edituser":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-client');
				JToolBarHelper::save('client_save');
				JToolBarHelper::cancel('client');
				break;

			default:
				JToolBarHelper::title(JText::_('clients_manager'), 'sc-client');
				JToolBarHelper::addNew('client_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('client_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'client_delete', 'JTOOLBAR_DELETE');
				JToolBarHelper::divider();
				JToolBarHelper::custom('show_help', 'help', 'help', JText::_('help'), false);
				break;
		}
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('clients_manager'));
	}
}