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

class HelpdeskKBAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-kb');
				JToolBarHelper::apply('kb_apply');
				JToolBarHelper::save('kb_save');
				JToolBarHelper::cancel('kb_search');
				break;

			case "moderate":
				JToolBarHelper::title(JText::_('kb_comments_manager'), 'sc-kb');
				JToolBarHelper::publishList('kb_publishcomments');
				JToolBarHelper::deleteList('', 'kb_delcomments', 'JTOOLBAR_DELETE');
				break;

			default:
				JToolBarHelper::title(JText::_('KNOWLEDGE_BASE'), 'sc-kb');
				JToolBarHelper::publishList('kb_publish');
				JToolBarHelper::unpublishList('kb_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('kb_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('kb_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'kb_remove', 'JTOOLBAR_DELETE');
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
			case 'moderate':
				$document->setTitle(JText::_('kb_comments_manager'));
				break;

			default:
				$document->setTitle(JText::_('kb_manager'));
				break;
		}
	}
}