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

class HelpdeskStaffAdminHelper
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
				JToolBarHelper::title(JText::_('support_staff_permission'), 'sc-staff');
				JToolBarHelper::save('staff_save');
				JToolBarHelper::cancel('staff');
				break;

			case "edit2":
				JToolBarHelper::title(JText::_('support_staff_view'), 'sc-staff');
				JToolBarHelper::addNew('staff_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('staff_edit');
				JToolBarHelper::deleteList('', 'staff_remove');
				JToolBarHelper::custom('staff', 'restore.png', 'restore_f2.png', JText::_('back'), false);
				break;

			default:
				JToolBarHelper::title(JText::_('support_staff_manager'), 'sc-staff');
				JToolBarHelper::addNew('staff_new', 'JTOOLBAR_NEW');
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
				$document->setTitle(JText::_('support_staff_permission'));
				break;

			case "edit2":
				$document->setTitle(JText::_('support_staff_view'));
				break;

			default:
				$document->setTitle(JText::_('support_staff_manager'));
				break;
		}
	}
}