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

$year = JRequest::getVar('year', 0, 'GET', 'int');
$month = JRequest::getVar('month', 0, 'GET', 'int');
$id_workgroup = JRequest::getVar('id_workgroup', 0, 'GET', 'int');
$f_status = JRequest::getVar('f_status', 0, 'GET', 'int');
$id_client = JRequest::getVar('id_client', 0, 'GET', 'int');
$f_staff = JRequest::getVar('f_staff', 0, 'GET', 'int');

$database = JFactory::getDBO();

$sql = "
SELECT 
  DISTINCT(s.`description`) as status,
  COUNT(l.`id_ticket`) as tickets,
  SUM(IF(
			l.`time_elapse`='0',
			(TIMEDIFF(NOW(), l.`date_time`)),
			l.`time_elapse`
	    )
	  ) AS lenght
FROM 
  #__support_log l
    LEFT JOIN #__support_ticket t  
      ON  l.id_ticket=t.id
	LEFT JOIN #__support_status s
	  ON s.`id` = l.`value`
WHERE 
  YEAR(l.`date_time`)='" . $year . "' AND
  MONTH(l.`date_time`)='" . $month . "'  AND
  l.`field` = 'status'
  " . ($f_staff > 0 ? "AND t.assign_to='" . $f_staff . "'" : "") . "
  " . ($f_status > 0 ? "AND l.value='" . $f_status . "'" : "") . "
  " . ($id_client > 0 ? "AND l.id_user='" . $id_client . "'" : "") . "
  " . ($id_workgroup > 0 ? "AND t.id_workgroup='" . $id_workgroup . "'" : "") . "
GROUP BY 
  l.`value` 
ORDER BY 
  l.`value`, 
  l.`date_time` 
";
$database->setQuery($sql);
$rows = $database->loadObjectList();

// Get workgroup name
if ($id_workgroup > 0) {
    $database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wk_desc = ($database->loadResult());
} else {
    $wk_desc = JText::_('all_workgroups');
}

// get status id
if ($f_status > 0) {
    $sql = "SELECT `description` AS text FROM #__support_status WHERE id='" . $f_status . "'";
    $database->setQuery($sql);
    $status_desc = ($database->loadResult());
} else {
    $status_desc = JText::_('all_status');
}

// get staff name
if ($f_staff > 0) {
    $database->setQuery("SELECT name FROM #__users WHERE id='$f_staff'");
    $staff_desc = $database->loadResult();
} else {
    $staff_desc = JText::_('all_staff');
}

# TITLE
$pagetitle = HelpdeskDate::GetMonthName($month) . " " . $year . " / " . ($wk_desc) . " / " . ($status_desc) . " / " . ($staff_desc);

// PDF - CSS
$lang = JFactory::getLanguage();
if ($lang->isRTL()) {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf_rtl.css');
} else {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf.css');
}

// PDF - Content
$page1 = '<h1 style="font-family:DejaVuSans;">' . $pagetitle . '</h1>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header">' . JText::_('status') . '</td>
	<td class="header">' . JText::_('ticket_report') . '</td>
	<td class="header">' . JText::_('duedate_explain_length_time') . ' ' . JText::_('time') . '</td>
	<td class="header">' . JText::_('average') . '</td>
</tr>
<tbody>';
for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $page1 .= '<tr>
		<td>' . $row->status . '</td>
		<td>' . $row->tickets . '</td>
		<td>' . HelpdeskDate::FormatDuration($row->lenght) . '</td>
		<td>' . HelpdeskDate::FormatDuration($avg) . '</td>
	</tr>';
}
$page1 .= '</tbody>
</thead>
</table>';

// RTL Check
$lg = ($lang->isRTL() ? 'ar' : 'UTF-8');
$mpdf = new mPDF($lg, 'A4-L');

$mpdf->SetAutoFont(AUTOFONT_ALL);

if ($lang->isRTL()) {
    $mpdf->SetDirectionality('rtl');
}

// Add stylesheet
$mpdf->WriteHTML($css, 1);

// Add content
$mpdf->WriteHTML($page1);

// Outputs PDF
$mpdf->Output(null, 'D');
exit;