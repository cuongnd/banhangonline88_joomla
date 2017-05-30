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
$is_support = HelpdeskUser::IsSupport();

// Settings
$data = '';
$columns = 0;
$cols_letters = array(
    1 => '0',
    2 => '1',
    3 => '2',
    4 => '3',
    5 => '4',
    6 => '5',
    7 => '6',
    8 => '7',
    9 => '8',
    10 => '9',
    11 => '10',
    12 => '11'
);

// Filters
$filter_workgroup = intval(JRequest::getVar('filter_workgroup', 0, 'REQUEST', 'int'));
$filter_status = intval(JRequest::getVar('filter_status', 0, 'REQUEST', 'int'));
$filter_status_group = JRequest::getVar('filter_status_group', 'O', 'REQUEST', 'string');
$filter_category = intval(JRequest::getVar('filter_category', 0, 'REQUEST', 'int'));
$filter_client = intval(JRequest::getVar('filter_client', 0, 'REQUEST', 'int'));
$filter_priority = intval(JRequest::getVar('filter_priority', 0, 'REQUEST', 'int'));
$filter_assign = intval(JRequest::getVar('filter_assign', 0, 'REQUEST', 'int'));
$filter_year = intval(JRequest::getVar('filter_year', 0, 'REQUEST', 'int'));
$filter_month = JRequest::getVar('filter_month', '0', 'REQUEST', 'string');
$execute = intval(JRequest::getVar('execute', 0, 'REQUEST', 'int'));

// Columns
$col_ticketid = intval(JRequest::getVar('col_ticketid', 0, 'REQUEST', 'int'));
$col_workgroup = intval(JRequest::getVar('col_workgroup', 0, 'REQUEST', 'int'));
$col_subject = intval(JRequest::getVar('col_subject', 0, 'REQUEST', 'int'));
$col_category = intval(JRequest::getVar('col_category', 0, 'REQUEST', 'int'));
$col_client = intval(JRequest::getVar('col_client', 0, 'REQUEST', 'int'));
$col_user = intval(JRequest::getVar('col_user', 0, 'REQUEST', 'int'));
$col_duedate = intval(JRequest::getVar('col_duedate', 0, 'REQUEST', 'int'));
$col_status = intval(JRequest::getVar('col_status', 0, 'REQUEST', 'int'));
$col_assign = intval(JRequest::getVar('col_assign', 0, 'REQUEST', 'int'));
$col_date_created = intval(JRequest::getVar('col_date_created', 0, 'REQUEST', 'int'));
$col_message = intval(JRequest::getVar('col_message', 0, 'REQUEST', 'int'));
$col_last_message = intval(JRequest::getVar('col_last_message', 0, 'REQUEST', 'int'));

// Count number of columns
if ($col_ticketid) {
    $columns++;
    $ticketid_letter = $cols_letters[$columns];
}
if ($col_workgroup) {
    $columns++;
    $workgroup_letter = $cols_letters[$columns];
}
if ($col_subject) {
    $columns++;
    $subject_letter = $cols_letters[$columns];
}
if ($col_category) {
    $columns++;
    $category_letter = $cols_letters[$columns];
}
if ($col_client) {
    $columns++;
    $client_letter = $cols_letters[$columns];
}
if ($col_user) {
    $columns++;
    $user_letter = $cols_letters[$columns];
}
if ($col_duedate) {
    $columns++;
    $duedate_letter = $cols_letters[$columns];
}
if ($col_status) {
    $columns++;
    $status_letter = $cols_letters[$columns];
}
if ($col_assign) {
    $columns++;
    $assign_letter = $cols_letters[$columns];
}
if ($col_date_created) {
    $columns++;
    $date_created_letter = $cols_letters[$columns];
}
if ($col_message) {
    $columns++;
    $message_letter = $cols_letters[$columns];
}
if ($col_last_message) {
    $columns++;
    $last_message_letter = $cols_letters[$columns];
}

// Titles
if ($col_ticketid) {
    $data .= utf8_decode(JText::_('ticketid')) . ';';
}
if ($col_workgroup) {
    $data .= utf8_decode(JText::_('workgroup')) . ';';
}
if ($col_subject) {
    $data .= utf8_decode(JText::_('subject')) . ';';
}
if ($col_category) {
    $data .= utf8_decode(JText::_('category')) . ';';
}
if ($col_client) {
    $data .= utf8_decode(JText::_('client_name')) . ';';
}
if ($col_user) {
    $data .= utf8_decode(JText::_('user')) . ';';
}
if ($col_duedate) {
    $data .= utf8_decode(JText::_('duedate')) . ';';
}
if ($col_status) {
    $data .= utf8_decode(JText::_('status')) . ';';
}
if ($col_assign) {
    $data .= utf8_decode(JText::_('tpl_assignedto')) . ';';
}
if ($col_date_created) {
    $data .= utf8_decode(JText::_('date_created')) . ';';
}
if ($col_message) {
    $data .= utf8_decode(str_replace(chr(10), " ", str_replace(chr(13), " ", JText::_('message')))) . ';';
}
if ($col_last_message) {
    $data .= utf8_decode(str_replace(chr(10), " ", str_replace(chr(13), " ", JText::_('last_message')))) . ';';
}
$data .= "\n";

// Where clause
$where = '';
if ($filter_workgroup) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_workgroup='" . $filter_workgroup . "'";
}
if ($filter_client) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_client='" . $filter_client . "'";
}
if ($filter_status) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_status='" . $filter_status . "'";
}
if ($filter_assign) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.assign_to='" . $filter_assign . "'";
}
if ($filter_priority) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_priority='" . $filter_priority . "'";
}
if ($filter_category) {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_category='" . $filter_category . "'";
}
if ($filter_year!='') {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "YEAR(t.date)=" . $database->quote($filter_year);
}
if ($filter_month!='' && $filter_month!='0') {
    $where .= ($where == '' ? 'WHERE ' : ' AND ') . "MONTH(t.date)=" . $database->quote($filter_month);
}
if ($filter_status_group!='') {
	$where .= ($where == '' ? 'WHERE ' : ' AND ') . "s.status_group='" . $filter_status_group . "'";
}
if ($is_support)
{
	$where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_user=" . $user->id;
}

// Rows
$sql = "SELECT t.id, t.ticketmask, t.subject, w.wkdesc, s.description AS status, ca.name AS category, u2.name AS supportuser, t.date, t.id_priority, t.duedate, c.clientname, u1.name, t.message, (SELECT tr.message FROM #__support_ticket_resp AS tr WHERE tr.id_ticket=t.id ORDER BY tr.id DESC LIMIT 0, 1) AS last_message
		FROM #__support_ticket AS t 
			 LEFT JOIN #__support_workgroup AS w ON w.id=t.id_workgroup 
			 LEFT JOIN #__support_client AS c ON c.id=t.id_client 
			 LEFT JOIN #__users AS u1 ON u1.id=t.id_user
			 LEFT JOIN #__support_status AS s ON s.id=t.id_status
			 LEFT JOIN #__users AS u2 ON u2.id=t.assign_to
			 LEFT JOIN #__support_category AS ca ON ca.id=t.id_category
		" . $where . " ORDER BY t.id ASC";
$database->setQuery($sql);
$rows = null;
$rows = $database->loadObjectList();

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];

    if ($col_ticketid) {
        $data .= utf8_decode($row->ticketmask) . ';';
    }
    if ($col_workgroup) {
        $data .= utf8_decode($row->wkdesc) . ';';
    }
    if ($col_subject) {
        $data .= utf8_decode($row->subject) . ';';
    }
    if ($col_category) {
        $data .= utf8_decode($row->category) . ';';
    }
    if ($col_client) {
        $data .= utf8_decode($row->clientname) . ';';
    }
    if ($col_user) {
        $data .= utf8_decode($row->name) . ';';
    }
    if ($col_duedate) {
        $data .= utf8_decode($row->duedate) . ';';
    }
    if ($col_status) {
        $data .= utf8_decode($row->status) . ';';
    }
    if ($col_assign) {
        $data .= utf8_decode($row->supportuser) . ';';
    }
    if ($col_date_created) {
        $data .= utf8_decode($row->date) . ';';
    }
    if ($col_message) {
        $data .= utf8_decode(str_replace(chr(10), " ", str_replace(chr(13), " ", strip_tags($row->message)))) . ';';
    }
    if ($col_last_message) {
        $data .= utf8_decode(str_replace(chr(10), " ", str_replace(chr(13), " ", strip_tags($row->last_message)))) . ';';
    }
    $data .= "\n";
}

header('HTTP/1.1 200 OK');
header('Status: 200 OK');
header('Pragma: public');
header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=export_analysis.csv");
header("Pragma: no-cache");
header('Cache-Control: cache, must-revalidate');
header("Expires: 0");
set_time_limit(0);
echo $data;
exit();
