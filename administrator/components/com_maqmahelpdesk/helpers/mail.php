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

class HelpdeskMailAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-mail');
				JToolBarHelper::save('mail_save');
				JToolBarHelper::cancel('mail');
				break;

			case "mails":
				JToolBarHelper::title(JText::_('fetch_log'), 'sc-mail');
				break;

			case "newignore":
			case "editignore":
				JToolBarHelper::title(JText::_('mail_ignore_rule'), 'sc-mail');
				JToolBarHelper::save('mail_saveignore');
				JToolBarHelper::cancel('mail_mailignore');
				break;

			case "mailignore":
				JToolBarHelper::title(JText::_('mail_ignore_rules'), 'sc-mail');
				JToolBarHelper::addNew('mail_newignore', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('mail_editignore', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'mailignore_remove', 'JTOOLBAR_DELETE');
				break;

			default:
				JToolBarHelper::title(JText::_('mail_fetch_manager'), 'sc-mail');
				JToolBarHelper::publishList('mail_publish');
				JToolBarHelper::unpublishList('mail_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::custom('mail_copy', 'copy', 'copy', JText::_('duplicate'), false);
				JToolBarHelper::divider();
				JToolBarHelper::addNew('mail_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('mail_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'mail_remove', 'JTOOLBAR_DELETE');
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
	static public function setDocument($task)
	{
		$document = JFactory::getDocument();
		switch ($task)
		{
			case 'mails':
				$document->setTitle(JText::_('fetch_log'));
				break;

			case 'mailignore':
				$document->setTitle(JText::_('mail_ignore_rules'));
				break;

			default:
				$document->setTitle(JText::_('mail_fetch_manager'));
				break;
		}
	}
}