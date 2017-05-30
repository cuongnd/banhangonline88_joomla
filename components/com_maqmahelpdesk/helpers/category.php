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

class HelpdeskCategory
{
	static function GetName($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name FROM #__support_category WHERE id='$id'");
		return ($database->loadResult() == '' ? JText::_('uncategorized') : $database->loadResult());
	}
}
