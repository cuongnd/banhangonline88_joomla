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

class HelpdeskDownloadAdminHelper
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
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', '', 'array');

		switch ($task)
		{
			case "new":
			case "edit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-dl_products');
				if (is_array($cid) || $cid > 0)
				{
					JToolBarHelper::custom('product_editversion', 'upload_f2.png', 'upload_f2.png', JText::_('new_version'), false);
					JToolBarHelper::divider();
				}
				JToolBarHelper::apply('product_apply');
				JToolBarHelper::save('product_save');
				JToolBarHelper::cancel('product');
				break;

			case "editversion":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-dl_products');
				JToolBarHelper::custom('product_edit', 'back_f2.png', 'back_f2.png', JText::_('back'), false);
				JToolBarHelper::divider();
				JToolBarHelper::save('product_saveversion');
				break;

			default:
				JToolBarHelper::title(JText::_('downloads'), 'sc-dl_products');
				JToolBarHelper::publishList('product_publish');
				JToolBarHelper::unpublishList('product_unpublish');
				JToolBarHelper::divider();
				JToolBarHelper::custom('product_copy', 'copy', 'copy', JText::_('duplicate'), false);
				JToolBarHelper::divider();
				JToolBarHelper::addNew('product_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('product_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'product_delete', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('downloads'));
	}
}