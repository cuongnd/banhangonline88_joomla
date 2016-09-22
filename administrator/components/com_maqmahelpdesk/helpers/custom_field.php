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

class HelpdeskCustomFieldAdminHelper
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
				JToolBarHelper::title(JText::_('cfield'), 'sc-customfield');
				JToolBarHelper::save('customfield_save');
				JToolBarHelper::cancel('customfield');
				break;

			default:
				JToolBarHelper::title(JText::_('cfields_manager'), 'sc-customfield');
				JToolBarHelper::addNew('customfield_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('customfield_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'customfield_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('cfields_manager'));
	}
}