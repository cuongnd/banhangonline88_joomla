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
$total = 0;
$total_msgs = 0;

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

// Get workgroup name
if ($id_workgroup > 0) {
    $database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wk_desc = ($database->loadResult());
} else {
    $wk_desc = JText::_('all_workgroups');
}

// Get clients name
if ($id_client > 0) {
    $database->setQuery("SELECT clientname FROM #__support_client WHERE id='" . $id_client . "'");
    $client_desc = $database->loadResult();
} else {
    $client_desc = JText::_('all_clients');
}

// get status id
if ($f_status > 0) {
    $sql = "SELECT `description` AS text FROM #__support_status WHERE id='" . $f_status . "'";
    $database->setQuery($sql);
    $status_desc = ($database->loadResult());
} else {
    $status_desc = JText::_('all_status');
}

// Get custom fields
$sql = "SELECT `id`, `caption` FROM #__support_custom_fields WHERE cftype='W' ORDER BY `caption`";
$database->setQuery($sql);
$custom_fields = $database->loadObjectList();

// PDF - CSS
$lang = JFactory::getLanguage();
if ($lang->isRTL()) {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf_rtl.css');
} else {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf.css');
}

# TITLE
$pagetitle = HelpdeskDate::GetMonthName($month) . " " . $year . " / " . ($wk_desc) . " / " . ($client_desc) . " / " . ($status_desc);

// PDF - CSS
$css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf.css');

// PDF - Content
$page1 = '<h1 style="font-family:DejaVuSans;">' . $pagetitle . '</h1>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header">' . JText::_('id') . '</td>
	<td class="header">' . JText::_('subject') . '</td>
	<td class="header">' . JText::_('user') . '</td>';
if ($f_customfields == 1) {
    for ($i = 0; $i < count($custom_fields); $i++) {
        $custom_field = $custom_fields[$i];
        $page1 .= '<td class="header">' . $custom_field->caption . '</td>';
    }
}
$page1 .= '	<td class="header">' . JText::_('created_date') . '</td>
	<td class="header">' . JText::_('last_update') . '</td>
	<td class="header">' . JText::_('status') . '</td>
	<td class="header">' . JText::_('messages') . '</td>
	<td class="header">' . JText::_('time') . '</td>
</tr>
</thead>
<tbody>';
for ($i = 0; $i < count($rows); $i++)
{
    $row = $rows[$i];
	$total_msgs = $total_msgs + (int) $row->num_msg;

    // Get tasks times
    $sql = "SELECT (
					(SUM(
						TIME_TO_SEC( REPLACE(timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(tickettravel,'.', ':') )
					))
				) AS taskstime FROM `#__support_task` WHERE `id_ticket`=" . $row->id;
    $database->setQuery($sql);
    $taskstimes = $database->loadResult();
    $time = $row->time_spent + $taskstimes;
	$total = $total + $time;
	$time = HelpdeskDate::SecondsToHours($time,true,false,false);

    $page1 .= '<tr>
		<td>' . $row->ticketmask . '</td>
		<td>' . $row->subject . '</td>
		<td>' . $row->name . '</td>';
    if ($f_customfields == 1)
    {
        for ($x = 0; $x < count($custom_fields); $x++)
        {
            $custom_field = $custom_fields[$x];
            $sql = "SELECT `newfield` AS `value` FROM #__support_field_value WHERE id_field='" . $custom_field->id . "' AND id_ticket='" . $row->id . "'";
            $database->setQuery($sql);
            $custom_field_value = $database->loadObject();
            if (!isset($custom_field_value)) {
                $page1 .= '<td></td>';
            } else {
                $v = (trim($custom_field_value->value) != '') ? ($custom_field_value->value) : '';
                $page1 .= '<td class="header">' . $v . '</td>';
            }
        }
    }
    $page1 .= '<td>' . $row->date . '</td>
		<td>' . $row->last_update . '</td>
		<td>' . $row->status . '</td>
		<td align="center">' . $row->num_msg . '</td>
		<td align="right">' . ($time!='00:00' ? $time : '&nbsp;') . '</td>';
    $page1 .= '</tr>';
}
$page1 .= '</tbody>
<tfoot>
<tr>
	<td class="header" colspan="' . (6+($f_customfields ? count($custom_fields) : 0)) . '"></td>
	<td class="header" align="center">' . $total_msgs . '</td>
	<td class="header" align="center">' . HelpdeskDate::SecondsToHours($total,true,false,false) . '</td>
</tr>
</tfoot>
</table>';

// RTL Check
$lg = ($lang->isRTL() ? 'ar' : 'UTF-8');
$mpdf = new mPDF($lg, 'A4-L');

$mpdf->SetAutoFont(AUTOFONT_ALL);

if ($lang->isRTL()) {
    $mpdf->SetDirectionality('rtl');
}

$mpdf->SetDisplayMode('fullpage', 'two');

// Add stylesheet
$mpdf->WriteHTML($css, 1);

// Add content
$mpdf->WriteHTML($page1);

// Outputs PDF
$mpdf->Output(null, 'D');
exit;
