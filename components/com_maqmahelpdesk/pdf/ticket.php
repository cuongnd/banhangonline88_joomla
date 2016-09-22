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

$database = JFactory::getDBO();
$user = JFactory::getUser();

$id = JRequest::getVar('id', 0, '', 'int');

// Get ticket details
$database->setQuery("SELECT t.id, t.id_user, t.an_name, t.date, t.message, t.id_workgroup, t.source, t.ticketmask, t.subject, t.id_priority, t.id_status, t.last_update, t.assign_to, t.id AS key1, w.id AS key2, c.id AS key3, s.id AS key4, p.id AS key5, u1.id AS key6, u2.id AS key7, w.wkdesc, c.name as catname, s.description as statusname, p.description as priorityname, u1.name as usercustomer, u2.name as userassign, cl.clientname, t.an_mail FROM #__support_ticket AS t INNER JOIN #__support_workgroup AS w ON w.id=t.id_workgroup LEFT JOIN #__support_category AS c ON c.id=t.id_category INNER JOIN #__support_status AS s ON s.id=t.id_status INNER JOIN #__support_priority AS p ON p.id=t.id_priority LEFT JOIN #__users AS u1 ON u1.id=t.id_user LEFT JOIN #__users AS u2 ON u2.id=t.assign_to LEFT JOIN #__support_client_users AS cu ON cu.id_user=t.id_user LEFT JOIN #__support_client AS cl ON cl.id=cu.id_client WHERE t.id='" . $id . "'");
$row = null;
$row = $database->loadObject();

// Set source description
$source_desc = '';
if ($row->source == "M") {
    $source_desc = (JText::_('email'));
} elseif ($row->source == "F") {
    $source_desc = (JText::_('fax'));
} elseif ($row->source == "O") {
    $source_desc = (JText::_('other'));
} elseif ($row->source == "W") {
    $source_desc = (JText::_('website'));
} elseif ($row->source == "P") {
    $source_desc = (JText::_('phone'));
}

// Get Ticket Messages
$ticketMsgs[0]->name = $row->an_name;
$ticketMsgs[0]->date = $row->date;
$ticketMsgs[0]->message = $row->message;
$ticketMsgs[0]->timeused = 0;
$ticketMsgs[0]->travel_time = 0;
$ticketMsgs[0]->tickettravel = 0;
$ticketMsgs[0]->acttype = '';
$ticketMsgs[0]->actrate = '';
$ticketMsgs[0]->multiplier = 0;
$ticketMsgs[0]->user_rate = 0;
$database->setQuery("SELECT u.name, r.date, r.message, r.timeused, r.travel_time, r.tickettravel, t.description as acttype, ra.description as actrate, ra.multiplier, r.user_rate FROM #__support_ticket_resp as r LEFT JOIN #__users as u ON r.id_user=u.id LEFT JOIN #__support_activity_type as t ON t.id=r.id_activity_type LEFT JOIN #__support_activity_rate as ra ON ra.id=r.id_activity_rate WHERE r.id_ticket='" . $row->id . "' ORDER BY r.`date` ASC ");
$ticketMsgs = array_merge($ticketMsgs, $database->loadObjectList());

// Get Ticket Rating
$database->setQuery("SELECT * FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
$ticketRate = $database->loadObjectList();

// Get Ticket Attachments
$database->setQuery("SELECT * FROM #__support_file WHERE id='" . $row->id . "' AND source='T' ORDER BY `date` DESC ");
$ticketAttachs = $database->loadObjectList();

// Get Ticket Notes
$database->setQuery("SELECT n.id, n.date_time, n.note, n.show, u.name FROM #__support_note as n, #__users as u WHERE n.id_ticket='" . $row->id . "' AND n.id_user=u.id " . ($is_support ? '' : " AND n.show='1'") . " ORDER BY n.id");
$ticketNotes = $database->loadObjectList();

// Get Ticket Log
$database->setQuery("SELECT l.id, l.date_time, l.log, l.log_reserved, u.name FROM #__support_log as l LEFT JOIN #__users as u ON l.id_user=u.id WHERE l.id_ticket='" . $row->id . "' ORDER BY l.id DESC");
$ticketLogs = $database->loadObjectList();

// Get Ticket Tasks of all users because we are on administration
$database->setQuery("SELECT t.id, t.date_time, t.task, t.status, u.name, t.start_time, t.end_time, t.break_time, t.traveltime, t.timeused, t.start_time, t.end_time, t.break_time, t.id_activity_type, t.id_activity_rate, t.end_date FROM #__support_task as t INNER JOIN #__users AS u ON t.id_user = u.id LEFT JOIN #__support_activity_type as y ON y.id=t.id_activity_type LEFT JOIN #__support_activity_rate as ra ON ra.id=t.id_activity_rate WHERE t.id_ticket='" . $row->id . "' AND u.id='" . $user->id . "' ORDER BY t.id");
$ticketTasks = $database->loadObjectList();

// Get Custom Fields for the Workgroup
$database->setQuery("SELECT w.id_workgroup, w.id_field, w.required, c.caption, c.ftype, c.value, c.size, c.maxlength, v.newfield FROM #__support_wk_fields as w, #__support_custom_fields as c, #__support_field_value as v WHERE w.id_field=c.id AND v.id_field=c.id AND v.id_ticket='" . $id . "' and w.id_workgroup='" . $row->id_workgroup . "' ORDER BY w.id_workgroup, w.ordering");
$customfields = $database->loadObjectList();

// Get Ticket Rate
$rate = 0;
$database->setQuery("SELECT rate FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
$rate = $database->loadResult();
if ($rate == "") {
    $rate = 0;
}

// Get Ticket Replies and Tasks Travel Sum Value
$database->setQuery("SELECT SUM(k.traveltime) FROM #__support_task as k, #__support_ticket as t WHERE t.id=k.id_ticket AND t.id=" . $row->id);
$traveltime = $database->loadResult();
$database->setQuery("SELECT SUM(r.tickettravel) FROM #__support_ticket_resp as r, #__support_ticket as t WHERE t.id=r.id_ticket AND t.id=" . $row->id);
$traveltime = $traveltime + $database->loadResult();

// Get Travel Time on Tickets and Tasks Value
$database->setQuery("SELECT c.travel_time FROM #__support_client as c, #__support_client_users as u, #__support_ticket as t WHERE c.id=u.id_client AND t.id_user=u.id_user AND t.id=" . $row->id);
$clienttravel = $database->loadResult();

// Get Ticket Messages Values by Activity Type
$database->setQuery("SELECT SUM(r.timeused * r.user_rate * ra.multiplier) AS total, t.description AS acttype FROM #__support_ticket_resp as r, #__support_activity_type as t, #__support_activity_rate as ra WHERE t.id=r.id_activity_type AND ra.id=r.id_activity_rate AND r.id_ticket='" . $row->id . "' GROUP BY t.description ORDER BY t.description");
$ticketValues = $database->loadObjectList();

// Get Tasks Values by Activity Type
$database->setQuery("SELECT SUM(r.timeused * r.rate * ra.multiplier) AS total, t.description AS acttype FROM #__support_task as r, #__support_activity_type as t, #__support_activity_rate as ra WHERE t.id=r.id_activity_type AND ra.id=r.id_activity_rate AND r.id_ticket='" . $row->id . "' GROUP BY t.description ORDER BY t.description");
$taskValues = $database->loadObjectList();

$ticketValues = array_merge($ticketValues, $taskValues);
$ticketValues2 = array();
$prevact = '';
$prevind = 0;

for ($i = 0; $i < count($ticketValues); $i++) {
    $xpto = $ticketValues[$i];

    if ($xpto->acttype == $prevact) {
        $ticketValues2[$prevind]->acttype = $xpto->acttype;
        $ticketValues2[$prevind]->total = $ticketValues2[$prevind]->total + $xpto->total;
    } else {
        $prevact = $xpto->acttype;
        $prevind = $i;
        $ticketValues2[$i]->acttype = $xpto->acttype;
        $ticketValues2[$i]->total = $xpto->total;
    }
}

// Get Ticket Messages Times by Activity Type
$database->setQuery("SELECT SUM(r.timeused) AS total, t.description AS acttype FROM #__support_ticket_resp as r, #__support_activity_type as t, #__support_activity_rate as ra WHERE t.id=r.id_activity_type AND ra.id=r.id_activity_rate AND r.id_ticket='" . $row->id . "' GROUP BY t.description ORDER BY t.description");
$ticketTimes = $database->loadObjectList();

// Get Tasks Times by Activity Type
$database->setQuery("SELECT SUM(r.timeused) AS total, t.description AS acttype FROM #__support_task as r, #__support_activity_type as t, #__support_activity_rate as ra WHERE t.id=r.id_activity_type AND ra.id=r.id_activity_rate AND r.id_ticket='" . $row->id . "' GROUP BY t.description ORDER BY t.description");
$taskTimes = $database->loadObjectList();

$database->setQuery("SELECT name FROM #__support_category WHERE id='" . $row->key3 . "'");
$ticketcategory = ($database->loadResult() == '' ? (JText::_('uncategorized')) : $database->loadResult());

$ticketTimes = array_merge($ticketTimes, $taskTimes);
$ticketTimes2 = array();
$prevact = '';
$prevind = 0;

for ($i = 0; $i < count($ticketTimes); $i++) {
    $xpto = $ticketTimes[$i];

    if ($xpto->acttype == $prevact) {
        $ticketTimes2[$prevind]->acttype = $xpto->acttype;
        $ticketTimes2[$prevind]->total = $ticketTimes2[$prevind]->total + $xpto->total;
    } else {
        $prevact = $xpto->acttype;
        $prevind = $i;
        $ticketTimes2[$i]->acttype = $xpto->acttype;
        $ticketTimes2[$i]->total = $xpto->total;
    }
}

// PDF - CSS
$lang = JFactory::getLanguage();
if ($lang->isRTL()) {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf_rtl.css');
} else {
    $css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf.css');
}

// PDF - Header
$page1 = '<h1 style="font-family:DejaVuSans;">#' . $row->ticketmask . ' ' . $row->subject . '</h1>
<table width="100%" border="0">
<tr>
	<td class="header">' . JText::_('workgroup') . '</td>
	<td colspan="3">' . $row->wkdesc . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('client') . '</td>
	<td colspan="3">' . $row->clientname . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('user') . '</td>
	<td colspan="3">' . $row->an_name . ' (' . $row->an_mail . ')' . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('date') . '</td>
	<td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->date)) . '</td>
	<td class="header">' . JText::_('duedate') . '</td>
	<td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime(HelpdeskTicket::ReturnDueDate(substr($row->date, 0, 4), substr($row->date, 5, 2), substr($row->date, 8, 2), substr($row->date, 11, 2), substr($row->date, 14, 2), $row->id_priority) . ':00')) . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('priority') . '</td>
	<td>' . $row->priorityname . '</td>
	<td class="header">' . JText::_('source') . '</td>
	<td>' . $source_desc . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('status') . '</td>
	<td>' . $row->statusname . '</td>
	<td class="header">' . JText::_('last_update') . '</td>
	<td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->last_update)) . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('tpl_assignedto') . '</td>
	<td>' . (!$is_support && ($supportConfig->support_only_show_assign == "1") ? JText::_('assigned_hidden') : $row->userassign) . '</td>
	<td class="header">' . JText::_('category') . '</td>
	<td>' . $ticketcategory . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('rating') . '</td>
	<td colspan="3"><img src="media/com_maqmahelpdesk/images/rating/' . HelpdeskForm::GetRate($row->id, 'T', 2) . 'star_pdf.png" /></td>
</tr>
</table>';

// PDF - Custom fields
if (count($customfields) > 0) {
    $page1 .= '<h2>' . JText::_('tpl_other_details') . '</h2>
			  <table width="100%" border="0">';
    for ($z = 0; $z < count($customfields); $z++) {
        $rowCF = $customfields[$z];
        $page1 .= '<tr>
					<td class="header">' . $rowCF->caption . '</td>
					<td colspan="3">' . $rowCF->newfield . '</td>
				</tr>';
    }
    $page1 .= '</table>';
}

// PDF - Activities history
$page2 = '<h2>' . JText::_('activity_history') . '</h2>
<table width="100%" border="0">
<tbody>';
for ($z = 0; $z < count($ticketMsgs); $z++) {
    $rowActivity = $ticketMsgs[$z];

    if (!$is_support && ($supportConfig->support_only_show_assign == "1")) {
        if ($rowActivity->name == $row->userassign) {
            $hidden_name = (JText::_('support_user'));
        } else if ($rowActivity->name == 'Administrator') {
            $hidden_name = (JText::_('administrator'));
        } else if ($rowActivity->name == $row->userassign) {
            $hidden_name = (JText::_('user'));
        } else {
            $hidden_name = (JText::_('other_user'));
        }
        if ($hidden_name == '') {
            $hidden_name = $row->an_name;
        }
        $user = $hidden_name;
    } else {
        if ($rowActivity->name == '') {
            $rowActivity->name = $row->an_name;
        }
        $user = $rowActivity->name;
    }

    $page2 .= '<tr>
				<td class="header bb">' . $user . '</td>
				<td class="header bb">' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($rowActivity->date)) . '</td>
				<td class="header bb">' . JText::_('time') . ': ' . $rowActivity->timeused . '</td>
			</tr>
			<tr>
				<td colspan="3">' . $rowActivity->message . '</td>
			</tr>';
}
$page2 .= '</tbody>
</table>';

// PDF - Timesheet
$page3 = '<h2>' . JText::_('timesheet') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('activity') . '</td>
	<td class="header bb">' . JText::_('times') . '</td>
</tr>
</thead>
<tbody>';
for ($Logi = 0; $Logi < count($ticketTimes2); $Logi++)
{
    $rowLog = $ticketTimes2[$Logi];
	$time = explode('.', $rowLog->total);
	if ($time[1]>='60') {
		$time[1] = $time[1] - 60;
		$time[0] = $time[0] + 1;
	}
    $page3 .= '<tr>
				<td>' . $rowLog->acttype . '</td>
				<td>' . str_pad($time[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($time[1], 2, '0', STR_PAD_LEFT) . '</td>
			</tr>';
}
$page3 .= '</tbody>
</table>';

// PDF - Logs
$page4 = '<h2>' . JText::_('activity_logs') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('date') . '</td>
	<td class="header bb">' . JText::_('user') . '</td>
	<td class="header bb">' . JText::_('log') . '</td>
</tr>
</thead>
<tbody>';
for ($Logi = 0; $Logi < count($ticketLogs); $Logi++) {
    $rowLog = $ticketLogs[$Logi];

    if (!$is_support && ($supportConfig->support_only_show_assign == "1")) {
        if ($rowLog->name == $row->userassign) {
            $name = (JText::_('support_user'));
        } else if ($rowLog->name == 'Administrator') {
            $name = (JText::_('administrator'));
        } else if ($rowLog->name == $row->userassign) {
            $name = (JText::_('user'));
        } else {
            $name = (JText::_('other_user'));
        }
        if ($name == '') {
            $name = $row->an_name;
        }
        $author = $name;
        $log = $rowLog->log_reserved;
    } else {
        if ($rowLog->name == '') {
            $rowLog->name = $row->an_name;
        }
        $author = $rowLog->name;
        $log = $rowLog->log;
    }

    $page4 .= '<tr>
				<td>' . $rowLog->date_time . '</td>
				<td>' . $author . '</td>
				<td>' . $log . '</td>
			</tr>';
}
$page4 .= '</tbody>
</table>';

// PDF - Attachments
$page5 = '<h2>' . JText::_('attachments') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('filename') . '</td>
	<td class="header bb">' . JText::_('description') . '</td>
</tr>
</thead>
<tbody>';
for ($Logi = 0; $Logi < count($ticketAttachs); $Logi++) {
    $rowLog = $ticketAttachs[$Logi];
    $page5 .= '<tr>
				<td>' . $rowLog->filename . '</td>
				<td>' . $rowLog->description . '</td>
			</tr>';
}
$page5 .= '</tbody>
</table>';

// PDF - Notes
$page6 = '<h2>' . JText::_('notes') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('date') . '</td>
	<td class="header bb">' . JText::_('user') . '</td>
</tr>
</thead>
<tbody>';
for ($Logi = 0; $Logi < count($ticketNotes); $Logi++) {
    $rowLog = $ticketNotes[$Logi];

    if (!$is_support && ($supportConfig->support_only_show_assign == "1")) {
        if ($rowLog->name == $row->userassign) {
            $hidden_name = (JText::_('support_user'));
        } else if ($rowLog->name == 'Administrator') {
            $hidden_name = (JText::_('administrator'));
        } else if ($rowLog->name == $row->userassign) {
            $hidden_name = (JText::_('user'));
        } else {
            $hidden_name = (JText::_('other_user'));
        }
        $log = $hidden_name;
    } else {
        $log = $rowLog->name;
    }

    $page6 .= '<tr>
				<td>' . $rowLog->date_time . '</td>
				<td>' . $log . '</td>
			</tr>';
}
$page6 .= '</tbody>
</table>';

// PDF - Tasks
$page7 = '<h2>' . JText::_('tasks') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('filename') . '</td>
	<td class="header bb">' . JText::_('description') . '</td>
</tr>
</thead>
<tbody>';
for ($Logi = 0; $Logi < count($ticketAttachs); $Logi++) {
    $rowLog = $ticketAttachs[$Logi];
    $page7 .= '<tr>
				<td>' . $rowLog->filename . '</td>
				<td>' . $rowLog->description . '</td>
			</tr>';
}
$page7 .= '</tbody>
</table>';

// RTL Check
$lg = ($lang->isRTL() ? 'ar' : 'UTF-8');
$mpdf = new mPDF($lg);

$mpdf->SetAutoFont(AUTOFONT_ALL);

if ($lang->isRTL()) {
    $mpdf->SetDirectionality('rtl');
}

// Add stylesheet
$mpdf->WriteHTML($css, 1);

// Add general details
$mpdf->WriteHTML($page1);

// Add activities history
$mpdf->WriteHTML($page2);

// Add log
$mpdf->AddPage();
$mpdf->WriteHTML($page4);

// Add timesheet
if (count($ticketTimes2)) {
    $mpdf->WriteHTML($page3);
}

// Add attachments
if (count($ticketAttachs)) {
    $mpdf->AddPage();
    $mpdf->WriteHTML($page5);
}

// Add notes
if (count($ticketNotes)) {
    $mpdf->AddPage();
    $mpdf->WriteHTML($page6);
}

// Add tasks
if (count($ticketNotes)) {
    $mpdf->AddPage();
    $mpdf->WriteHTML($page7);
}

// Outputs PDF
$mpdf->Output(null, 'D');
exit;
