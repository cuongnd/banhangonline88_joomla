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

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/report.php');

$year = JRequest::getVar('year', 0, 'GET', 'int');
$month = JRequest::getVar('month', 0, 'GET', 'int');
$month = ((int)$month < 10 && $month != '00' ? '0' . (int)$month : $month);
$id_workgroup = JRequest::getVar('id_workgroup', 0, 'GET', 'int');
$id_client = JRequest::getVar('client', 0, 'GET', 'int');
$f_status = JRequest::getVar('f_status', 0, 'GET', 'int');
$custom_fields = JRequest::getVar('custom_fields', 0, 'GET', 'int');
$detail = JRequest::getVar('detail', 0, 'GET', 'int');
$f_customfields = JRequest::getVar('f_customfields', 1, 'GET', 'int');
$report = JRequest::getVar('report', 'clientm', 'GET', 'string');
$data = "";

$supportConfig = HelpdeskUtility::GetConfig();
$database = JFactory::getDBO();

$sql = "SELECT t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name, (
					(SUM(
						TIME_TO_SEC( REPLACE(r.timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':') )
					))
				) AS time_spent, count(r.id) as num_msg, s.description as status, c.name AS category, t.duedate, p.description AS priority
		FROM #__support_ticket t
			 INNER JOIN #__support_status s ON s.id=t.id_status
			 LEFT JOIN #__support_ticket_resp r ON r.id_ticket=t.id
			 LEFT JOIN #__support_category AS c ON c.id=t.id_category
			 LEFT JOIN #__support_priority AS p ON p.id=t.id_priority
			 INNER JOIN #__users u ON u.id=t.id_user
		WHERE YEAR(t.`date`)=" . $database->quote($year) . " " .
	($month != '00' ? "AND MONTH(t.`date`)=" . $database->quote($month) : "") . " " .
	($id_workgroup > 0 ? "AND t.id_workgroup=" . $database->quote($id_workgroup) : "") . " " .
	($id_client > 0 ? "AND t.id_client=" . $database->quote($id_client) : "") . " " .
	($f_status > 0 ? "AND t.id_status=" . $database->quote($f_status) : "") . "
		GROUP BY t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name, c.name , t.duedate, p.description
		ORDER BY t.`date`";
$database->setQuery($sql);
$rows = $database->loadObjectList();

// Get custom fields
$sql = "SELECT `id`, `caption` FROM #__support_custom_fields WHERE cftype='W' ORDER BY `caption`";
$database->setQuery($sql);
$custom_fields = $database->loadObjectList();

# TABLE HEADER
$data .= '"' . JText::_('id') . '";"' . JText::_('subject') . '";"' . JText::_('user') . '";"' . JText::_('priority') . '";"' . JText::_('category') . '"';
if ($f_customfields == 1) {
	for ($i = 0; $i < count($custom_fields); $i++) {
		$custom_field = $custom_fields[$i];
		$data .= ';"' . $custom_field->caption . '"';
	}
}
$data .= ';"' . JText::_('created_date') . '";"' . JText::_('last_update') . '";"' . JText::_('duedate') . '";"' . JText::_('status') . '";"' . JText::_('messages') . '";"' . JText::_('time') . '";' . "\r\n";

# TABLE BODY
for ($x = 0; $x < count($rows); $x++) {
	$row = $rows[$x];
	$data .= $row->ticketmask . ";" . ($row->subject) . ";" . ($row->name) . ";" . ($row->priority) . ";" . ($row->category);
	if ($f_customfields == 1) {
		for ($i = 0; $i < count($custom_fields); $i++) {
			$custom_field = $custom_fields[$i];
			$sql = "SELECT `newfield` AS `value` FROM #__support_field_value WHERE id_field='" . $custom_field->id . "' AND id_ticket='" . $row->id . "'";
			$database->setQuery($sql);
			$custom_field_value = $database->loadObject();
			if (!isset($custom_field_value)) {
				$data .= ';""';
			} else {
				$v = (trim($custom_field_value->value) != '') ? ($custom_field_value->value) : '';
				$data .= ';"' . $v . '"';
			}
		}
	}
	$data .= ';"' . $row->date . '";"' . $row->last_update . '";"' . $row->duedate . '";"' . ($row->status) . '";"' . $row->num_msg . '";"';
	$value = HelpdeskDate::SecondsToHours($row->time_spent,true,false,false);
	$data .= ($value!='00:00' ? $value : '') . '";' . "\r\n";
}

$filename = 'clients_csv_' . HelpdeskDate::DateOffset("%Y%m%d_%H%M%S");

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;