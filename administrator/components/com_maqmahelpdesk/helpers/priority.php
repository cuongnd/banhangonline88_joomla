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

class HelpdeskPriorityAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-priority');
				JToolBarHelper::save('priority_save');
				JToolBarHelper::cancel('priority');
				break;

			default:
				JToolBarHelper::title(JText::_('priorities_manager'), 'sc-category');
				JToolBarHelper::publishList('priority_publish');
				JToolBarHelper::unpublishList('priority_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('priority_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('priority_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'priority_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('priorities_manager'));
	}
}