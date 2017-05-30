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

class HelpdeskFormsAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-forms');
				JToolBarHelper::custom('show_action', 'new.png', 'new_f2.png', JText::_('newaction'), false);
				JToolBarHelper::custom('show_field', 'new.png', 'new_f2.png', JText::_('newfield'), false);
				JToolBarHelper::divider();
				JToolBarHelper::apply('forms_apply');
				JToolBarHelper::save('forms_save');
				JToolBarHelper::cancel('forms');
				break;

			default:
				JToolBarHelper::title(JText::_('forms_manager'), 'sc-forms');
				JToolBarHelper::addNew('forms_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('forms_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'forms_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('forms_manager'));
	}
}