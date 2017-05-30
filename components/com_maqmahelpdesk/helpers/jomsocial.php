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

class HelpdeskJomSocial
{
	static function Points($id_user, $action)
	{
		if (file_exists(JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php')) {
			include_once(JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php');
			CuserPoints::assignPoint($action, $id_user);
		}
	}

	static function Post($id_user, $title, $message)
	{
		$database = JFactory::getDBO();

		// Get privacy param
		$sql = "SELECT `params`
				FROM `#__community_users`
				WHERE `userid`='" . $id_user . "'";
		$database->setQuery($sql);
		$userParams = $database->loadResult();
		$params = new JParameter($userParams);

		// Insert message
		$sql = "INSERT INTO `#__community_activities` (`actor`, `target`, `title`, `content`, `app`, `cid`, `created`, `access`, `params`, `points`, `archived`)
				VALUES(" . $id_user . ", 0, " . $database->quote($title) . ", " . $database->quote($message) . ", 'helpdesk', 1, '" . date("Y-m-d H:i:s") . "', " . $params->get('privacyProfileView', 0) . ", '\r\n\r\n', 0, 0);";
		$database->setQuery($sql);
		$database->query();
	}
}
