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

class HelpdeskToolsAdminHelper
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
			case "db1":
				JToolBarHelper::title(JText::_('database_cleanup'), 'sc-db');
				JToolBarHelper::custom('tools_db2', 'move.png', 'move_f2.png', JText::_('execute'), false);
				JToolBarHelper::cancel('');
				break;

			case "deletetickets1":
				JToolBarHelper::title(JText::_('DELETE_TICKETS'), 'sc-db');
				JToolBarHelper::custom('tools_deletetickets2', 'move.png', 'move_f2.png', JText::_('execute'), false);
				JToolBarHelper::cancel('');
				break;

			case "ambrasubs1":
				JToolBarHelper::title(JText::_('import_ambrasubs'), 'sc-db');
				JToolBarHelper::custom('tools_ambrasubs2', 'move.png', 'move_f2.png', JText::_('execute'), false);
				JToolBarHelper::cancel('');
				break;

			case "billets1":
				JToolBarHelper::title(JText::_('import_billets'), 'sc-db');
				JToolBarHelper::custom('tools_billets2', 'move.png', 'move_f2.png', JText::_('execute'), false);
				JToolBarHelper::cancel('');
				break;

			case "rstickets1":
				JToolBarHelper::title(JText::_('import_rstickets'), 'sc-db');
				JToolBarHelper::custom('tools_rstickets2', 'move.png', 'move_f2.png', JText::_('execute'), false);
				JToolBarHelper::cancel('');
				break;

			default:
				JToolBarHelper::title(JText::_('trouble_manager'), 'sc-trouble');
				JToolBarHelper::addNew('troubleshooter_new', 'JTOOLBAR_NEW');
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
		$document->setTitle(JText::_('trouble_manager'));
	}
}