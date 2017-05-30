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

class HelpdeskBugTracker
{
	static function GetMessageAttachments($id, $reply)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `id`, `id_file`, `filename`, `description`
				FROM `#__support_file`
				WHERE `id`='" . $id . "' AND `id_reply`='" . $reply . "' AND `source`='B'";
		$database->setQuery($sql);
		return $database->loadObjectList();
	}
}