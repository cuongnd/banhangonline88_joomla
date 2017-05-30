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

class HelpdeskEmail
{
	static function CheckEmailID($id)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT COUNT(*)
				FROM `#__support_mail_log` 
				WHERE `emailid`=" . $database->quote($id);
		$database->setQuery($sql);
		$check = $database->loadResult();

		return $check;
	}
}
