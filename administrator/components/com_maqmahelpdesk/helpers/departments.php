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

class HelpdeskDepartmentsAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @param   string  $task  Action to execute.
	 *
	 * @return void
	 */
	static public function addToolbar($task)
	{
		switch ($task)
		{
			case "new":
			case "edit":
				JToolBarHelper::title(JText::_('EDIT') . '<br /><div id="loading" name="loading" style="display:none;"><img src="../components/com_maqmahelpdesk/images/progressbar.gif" border="0" alt="' . JText::_('loading') . '" /></div>', 'sc-workgroup');
				JToolBarHelper::save('workgroup_save');
				JToolBarHelper::cancel('workgroup');
				break;

			case "copy":
			default:
				JToolBarHelper::title(JText::_('workgroups'), 'sc-workgroup');
				JToolBarHelper::addNew('workgroup_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('workgroup_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'workgroup_remove', 'JTOOLBAR_DELETE');
				JToolBarHelper::divider();
				JToolBarHelper::publishList('workgroup_publish');
				JToolBarHelper::unpublishList('workgroup_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::custom('workgroup_copy', 'copy', 'copy', JText::_('duplicate'), false);
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
		$document->setTitle(JText::_('workgroups'));
	}
}