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

class HelpdeskPublicDiscussionsAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function addToolbar()
	{
		JToolBarHelper::title(JText::_('discussions'), 'sc-discussions');
		JToolBarHelper::publishList('discussions_publish');
		JToolBarHelper::unpublishList('discussions_unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'discussions_remove');
		JToolBarHelper::divider();
		JToolBarHelper::custom('show_help', 'help', 'help', JText::_('help'), false);
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('discussions'));
	}
}