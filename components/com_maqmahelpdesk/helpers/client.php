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

class HelpdeskClient
{
	static function isNotifyClientMgr($client_id)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT manager from #__support_client WHERE id=" . (int)$client_id;
		$database->setQuery($sql);
		$notify_mgr = $database->loadResult();
		return $notify_mgr;
	}

	static function GetName($id)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT c.clientname
				FROM #__support_client c, #__support_client_users u
				WHERE c.id=u.id_client AND u.id_user=" . (int) $id;
		$database->setQuery($sql);
		return ($database->loadResult() != '' ? $database->loadResult() : JText::_('no_customer'));
	}

	static function GetIDByUser($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT c.id FROM #__support_client c, #__support_client_users u WHERE u.id_user='$id'");
		return $database->loadResult();
	}

	static function AccessApplication($department, $client, $application)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `app_$application`
				FROM `#__support_client_wk` AS c
				WHERE c.`id_client`=" . (int) $client . "
				  AND (c.`id_workgroup`=" . (int) $department . " OR c.`id_workgroup`=0)";
		$database->setQuery($sql);
		return $database->loadResult();
	}
}
