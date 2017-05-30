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

class HelpdeskDepartmentsFieldsAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-customfield');
				JToolBarHelper::save('wkfields_save');
				JToolBarHelper::cancel('wkfields');
				break;

			case "copy":
			default:
				JToolBarHelper::title(JText::_('cfield_assign'), 'sc-customfield');
				JToolBarHelper::addNew('wkfields_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('wkfields_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'wkfields_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('cfield_assign'));
	}
}