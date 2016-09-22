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

class HelpdeskStatus
{
	static function GetName($id)
	{
		if (!$id)
			return;
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id, description FROM #__support_status WHERE id='$id'");
		$row = $database->loadObject();
		return $row->description;
	}

	static function GetDefault()
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id FROM #__support_status WHERE isdefault='1'");
		return $database->loadResult();
	}

	static function GetList()
	{
		$database = JFactory::getDBO();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();
		$id = JRequest::getInt('id', 0);
		$id_workgroup = JRequest::getInt('id_workgroup', 0);

		$sql = "SELECT `id_status`
				FROM `#__support_ticket`
				WHERE `id` = $id";
		$database->setQuery($sql);
		$id_status = $database->loadResult();

		$clause = ($is_client) ? " WHERE (user_access = '1' OR (user_access = '0' AND id = '" . $id_status . "')) " : "";

		if (!$is_support) {
			$sql = "SELECT `allow_old_status_back`
					FROM #__support_status
					WHERE id = '" . $id_status . "' ";
			$database->setQuery($sql);
			$allow_old_status_back = $database->loadResult();

			$sql = "SELECT id_status
					FROM #__support_log
					WHERE id_ticket='" . $id . "' AND id_status <> '" . $id_status . "' AND id_status <> '' AND id_status <> '0'
					ORDER BY date_time DESC
					LIMIT 1";
			$database->setQuery($sql);
			$old_id_status = $database->loadResult();

			// Get possible list os statuses
			$sql = "SELECT `status_workflow`
					FROM #__support_status
					WHERE `id` = '" . $id_status . "' AND `status_workflow` != '0' AND `status_workflow` != ''";
			$database->setQuery($sql);
			$status_workflow = $database->loadResult();
			$status_checked = explode("#", $status_workflow);

			if ($status_workflow != '') {
				$list = "";
				if ($allow_old_status_back == 1) {
					$list .= $old_id_status . ",";
				}

				for ($i = 0; $i < count($status_checked); $i++) {
					if (($allow_old_status_back == 1) && ($status_checked[$i] != $old_id_status)) {
						$list .= $status_checked[$i] . ",";
					} else {
						$list .= $status_checked[$i] . ",";
					}
				}

				if ($list != "") {
					$list = substr($list, 0, -1);
					$list_status_allowed = "(" . $id_status . $list . ")";
					$clause .= ($clause == "") ? " WHERE " : " AND ";
					$clause .= " `id` IN " . $list_status_allowed . " ";
				}
			}
		}

		$sql = "SELECT `id`, `description`
				FROM `#__support_status`
				$clause
				ORDER BY `ordering`, `description`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		$content = '<ul>';
		foreach ($rows as $row) {
			$content .= '<li>';
			$content .= '<a href="javascript:;" onclick="SetTicketStatus(' . $id . ',' . $row->id . ',' . $id_workgroup . ');">' . addslashes($row->description) . '</a>';
			$content .= '</li>';
		}
		$content .= '</ul>';

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: text/html');

		echo $content;
	}
}
