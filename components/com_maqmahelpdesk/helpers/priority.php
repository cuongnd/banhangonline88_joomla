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

class HelpdeskPriority
{
	static function GetName($id)
	{
		if (!$id)
			return;
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id, description FROM #__support_priority WHERE id='$id'");
		$row = $database->loadObject();
		return $row->description;
	}

	static function GetDefault()
	{
		$database = JFactory::getDBO();
		$is_client = HelpdeskUser::IsClient();
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$dfpriority = 0;

		// Contract priority
		if ($is_client && $workgroupSettings->contract)
		{
			$sql = "SELECT t.`id_priority`
					FROM `#__support_contract` AS c
						 INNER JOIN `#__support_contract_template` AS t ON t.`id`=c.`id_contract`
					WHERE c.`status`='A'
					  AND c.`id_client`=" . (int) $is_client . "
					  AND c.`date_end`>='" . HelpdeskDate::DateOffset("%Y-%m-%d") . "'
					  AND c.`date_start`<='" . HelpdeskDate::DateOffset("%Y-%m-%d") . "'";
			$database->setQuery($sql);
			$dfpriority = $database->loadResult();
		}

		// Department priority
		if ($workgroupSettings->id_priority && !$dfpriority)
		{
			$dfpriority = $workgroupSettings->id_priority;
		}
		// Default priority
		elseif (!$dfpriority)
		{
			$sql = "SELECT `id`
					FROM `#__support_priority`
					WHERE `isdefault`='1'";
			$database->setQuery($sql);
			$dfpriority = $database->loadResult();
		}

		return $dfpriority;
	}
}
