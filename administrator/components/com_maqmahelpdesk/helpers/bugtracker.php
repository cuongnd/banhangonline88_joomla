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

class HelpdeskBugTrackerAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function addToolbar()
	{
		JToolBarHelper::title(JText::_('bugtracker'), 'sc-bugtracker');
		JToolBarHelper::custom('bugtracker_pending', 'move.png', 'move_f2.png', JText::_('bug_status_p'), false);
		JToolBarHelper::custom('bugtracker_open', 'move.png', 'move_f2.png', JText::_('bug_status_o'), false);
		JToolBarHelper::custom('bugtracker_inprogress', 'move.png', 'move_f2.png', JText::_('bug_status_i'), false);
		JToolBarHelper::custom('bugtracker_resolved', 'move.png', 'move_f2.png', JText::_('bug_status_r'), false);
		JToolBarHelper::custom('bugtracker_closed', 'move.png', 'move_f2.png', JText::_('bug_status_c'), false);
		JToolBarHelper::custom('bugtracker_reopened', 'move.png', 'move_f2.png', JText::_('bug_status_d'), false);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'bugtracker_remove');
		JToolBarHelper::divider();
		JToolBarHelper::custom('show_help', 'help', 'help', JText::_('help'), false);
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('bugtracker'));
	}
}