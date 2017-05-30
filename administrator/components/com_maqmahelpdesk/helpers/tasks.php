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

class HelpdeskTasksAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function addToolbar()
	{
		JToolBarHelper::title(JText::_('JTOOLBAR_NEW'), 'sc-calendars');
		JToolBarHelper::save('calendar_save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('calendar', 'JTOOLBAR_CANCEL');
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JTOOLBAR_NEW'));
	}
}