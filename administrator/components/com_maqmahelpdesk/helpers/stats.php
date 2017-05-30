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

class HelpdeskStatsAdminHelper
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
			case "hits":
				JToolBarHelper::title(JText::_('download_statistics') . ' - ' . JText::_('pagehits'), 'sc-dl_stats');
				MaQmaToolBarHelper::Preview('index.php?option=com_maqmahelpdesk&task=stats_hits&print=1&tmpl=component');
				break;

			default:
				JToolBarHelper::title(JText::_('download_statistics') . ' - ' . JText::_('downloads'), 'sc-dl_stats');
				MaQmaToolBarHelper::Preview('index.php?option=com_maqmahelpdesk&task=stats&print=1&tmpl=component');
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
		$document->setTitle(JText::_('download_statistics'));
	}
}