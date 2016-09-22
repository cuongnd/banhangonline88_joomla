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

class HelpdeskAnnouncementsAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-announces');
				JToolBarHelper::save('announce_save');
				JToolBarHelper::cancel('announce');
				break;

			case "preparesend":
				JToolBarHelper::title(JText::_('prepare_send'), 'sc-send');
				JToolBarHelper::custom('announce_send', 'move.png', 'move_f2.png', JText::_('send'), false);
				JToolBarHelper::divider();
				JToolBarHelper::cancel('announce');
				break;

			default:
				JToolBarHelper::title(JText::_('announcements_manager'), 'sc-announces');
				JToolBarHelper::custom('announce_preparesend', 'move.png', 'move_f2.png', JText::_('send'), false);
				JToolBarHelper::divider();
				JToolBarHelper::addNew('announce_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('announce_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'announce_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('announcements_manager'));
	}
}