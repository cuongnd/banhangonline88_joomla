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

class HelpdeskDownloadCategoryAdminHelper
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
				JToolBarHelper::title(JText::_('EDIT'), 'sc-dl_category');
				JToolBarHelper::save('dlcategory_save');
				JToolBarHelper::cancel('dlcategory');
				break;

			default:
				JToolBarHelper::title(JText::_('categories_manager'), 'sc-dl_category');
				JToolBarHelper::publishList('dlcategory_publish');
				JToolBarHelper::unpublishList('dlcategory_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('dlcategory_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('dlcategory_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'dlcategory_remove', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('categories_manager'));
	}
}