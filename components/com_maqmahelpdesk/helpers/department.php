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

class HelpdeskDepartment
{
	static function GetSettings()
	{
		$database = JFactory::getDBO();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$database->setQuery("SELECT * FROM #__support_workgroup WHERE id=" . $id_workgroup);
		return $database->loadObject();
	}

	static function Get()
	{
		$session = JFactory::getSession();
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();

		// Get workgroups
		$sql = "SELECT id, wkdesc, wkabout, logo, shortdesc
				FROM #__support_workgroup
				WHERE `show`='1'
				ORDER BY ordering, wkdesc";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		// Verify permissions to the workgroups
		$wkids = '';
		$wkids_not_support = '';

		if (!$supportConfig->support_workgroup_only && $is_support)
		{
			// Get workgroups where the user is as support staff
			$sql = "SELECT w.id, w.wkdesc, w.wkabout, w.logo, w.shortdesc
					FROM #__support_workgroup AS w
					WHERE w.id IN (SELECT p.id_workgroup
								   FROM `#__support_permission` AS p
								   WHERE p.`id_user`=" . $user->id . " AND p.`id_workgroup`=w.`id`)
					  AND w.`show`='1'
					ORDER BY w.ordering, w.wkdesc";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();

			// Get workgroups where the user is not as support staff (if any)
			$sql = "SELECT w.id, w.wkdesc, w.wkabout, w.logo, w.shortdesc
					FROM #__support_workgroup AS w
					WHERE w.id NOT IN (SELECT p.id_workgroup
									   FROM `#__support_permission` AS p
									   WHERE p.`id_user`=" . $user->id . " AND p.`id_workgroup`=w.`id`)
					  AND w.`show`='1'
					ORDER BY w.ordering, w.wkdesc";
			$database->setQuery($sql);
			$rows2 = $database->loadObjectList();

			// Rebuild access IDs
			$wkids = '';
			for ($i = 0; $i < count($rows); $i++)
			{
				$row = $rows[$i];
				$wkids .= $row->id . ',';
			}
			$wkids = substr($wkids, 0, strlen($wkids) - 1);

			// Rebuild access IDs where it's not support
			if (is_array($rows2))
			{
				for ($i = 0; $i < count($rows2); $i++)
				{
					$row = $rows2[$i];
					$wkids_not_support .= $row->id . ',';
				}
				$wkids_not_support = substr($wkids_not_support, 0, strlen($wkids_not_support) - 1);
			}
		}

		if ($is_client)
		{
			for ($i = 0; $i < count($rows); $i++)
			{
				$row = $rows[$i];
				$sql = "SELECT COUNT(*)
						FROM #__support_client_wk
						WHERE (id_workgroup=" . (int) $row->id . " OR id_workgroup='0')
						  AND id_client=" . (int) $is_client;
				$database->setQuery($sql);
				if ($database->loadResult() > 0)
				{
					// Set session access permissions
					if (HelpdeskDepartment::UserGroupRestricted($row->id))
					{
						$wkids .= $row->id . ',';
					}
				}
			}
			$wkids = substr($wkids, 0, strlen($wkids) - 1);
		}

		if ($wkids == '')
		{
			for ($i = 0; $i < count($rows); $i++)
			{
				$row = $rows[$i];
				if (HelpdeskDepartment::UserGroupRestricted($row->id))
				{
					$wkids .= $row->id . ',';
				}
			}
			$wkids = substr($wkids, 0, strlen($wkids) - 1);
		}

		$session->set('wkids', $wkids, 'maqmahelpdesk');
		$session->set('wkids_notsupport', $wkids_not_support, 'maqmahelpdesk');
	}

	static function Stats($id, $type)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();

		switch ($type)
		{
			// Get number of tickets
			case 'ticket':
				$sql = "SELECT COUNT(*) AS TOTAL
						FROM #__support_ticket AS t 
							 INNER JOIN #__support_status AS s ON s.id=t.id_status 
						WHERE s.status_group='O' 
						  AND t.id_workgroup=" . $id . " " .
					(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
				break;


			// Get discussions without answers
			case 'discussion':
				$sql = "SELECT COUNT(*)
						FROM `#__support_discussions`
						WHERE `status`=0 AND `id_workgroup`=" . $id . " " . (!$is_support ? "AND id_user=" . $user->id : "");
				break;

			// Get bug trackers not RESOLVED or CLOSED if support agent or the open bugs opened by logged user
			case 'bugtracker':
				$sql = "SELECT COUNT(*)
						FROM `#__support_bugtracker`
						WHERE `status`<>'C' AND `status`<>'R' AND `id_workgroup`=" . $id . " " . ($is_support ? "AND (`id_assign`=0 OR `id_assign`=" . $user->id . ")" : "AND `id_user`=" . $user->id);
				break;
		}
		$database->setQuery($sql);
		return (int)$database->loadResult();
	}

	static function GetName($id)
	{
		if (!$id)
			return;

		$database = JFactory::getDBO();
		$sql = "SELECT w.`id`, w.`wkdesc`
				FROM `#__support_workgroup` AS w
				WHERE w.`id`=" . (int) $id;
		$database->setQuery($sql);
		$row = $database->loadObject();
		return $row->wkdesc;
	}

	static function UserGroupRestricted($department)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$restriction = 1;

		// Get groups from department
		$sql = "SELECT `id_group`
				FROM `#__support_department_groups`
				WHERE `id_department`=" . (int) $department;
		$database->setQuery($sql);
		$groups = $database->loadObjectList();

		// Get user groups
		$usergroups = JUserHelper::getUserGroups($user->id);

		// Validate access
		if (count($groups) && !count($usergroups))
		{
			$restriction = 0;
		}
		elseif (!count($groups))
		{
			$restriction = 1;
		}
		elseif (count($groups) && count($usergroups))
		{
			$sql = "SELECT COUNT(*)
					FROM `#__support_department_groups`
					WHERE `id_department`=" . (int) $department . "
					  AND `id_group` IN (" . implode(',', $usergroups) . ")";
			$database->setQuery($sql);
			$restriction = $database->loadResult();
		}

		return $restriction;
	}
}
