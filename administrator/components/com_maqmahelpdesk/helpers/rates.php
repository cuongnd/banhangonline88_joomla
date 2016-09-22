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

class HelpdeskRatesAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-rates');
				JToolBarHelper::save('rates_save');
				JToolBarHelper::cancel('rates');
				break;

			default:
				JToolBarHelper::title(JText::_('actrates_manager'), 'sc-rates');
				JToolBarHelper::publishList('rates_publish');
				JToolBarHelper::unpublishList('rates_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('rates_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('rates_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'rates_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('actrates_manager'));
	}
}