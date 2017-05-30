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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$database = JFactory::getDBO();
$supportConfig = HelpdeskUtility::GetConfig();
$my = JFactory::getUser();

// Export History delete
$id_export = JRequest::getVar('id', 0, '', 'int');
$export_task = JRequest::getVar('export_task', '', '', 'string');

if ($id_export > 0 && $export_task) {
	$id_profile = ($id_export > 0 ? JRequest::getVar('task', 0, '', 'int') : JRequest::getVar('export_profile_id', '', 'POST', 'int'));

	switch ($export_task) {
		case 'view':
			$database->setQuery("SELECT `export_data`, `export_date`, `hits` FROM #__support_export WHERE id='" . $id_export . "'");
			$old_export = $database->loadObject();
			if ($old_export) {
				$filename = date("Ymd_H-i", HelpdeskDate::ParseDate($old_export->export_date, "%Y-%m-%d %H:%M:%S"));
				$data = $old_export->export_data;
				$hits = $old_export->hits;
				$hits++;
				$database->setQuery("UPDATE #__support_export SET hits='" . $hits . "' WHERE id='" . $id_export . "'");
				if ($database->query()) {
					header('HTTP/1.1 200 OK');
					header('Status: 200 OK');
					header('Pragma: public');
					header("Content-type: application/octet-stream");
					header("Content-Transfer-Encoding: binary");
					header("Content-Disposition: attachment; filename=$filename.csv");
					header("Pragma: no-cache");
					header('Cache-Control: cache, must-revalidate');
					header("Expires: 0");
					set_time_limit(0);
					echo $data;
					exit();
				} else {
					echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			break;

		case 'tpl':
			$database->setQuery("SELECT `profile_tmpl`, `export_date` FROM #__support_export WHERE id='" . $id_export . "'");
			$old_export = $database->loadObject();
			$filename = date("Ymd_H-i", HelpdeskDate::ParseDate($old_export->export_date, "%Y-%m-%d %H:%M:%S"));
			$data = $old_export->profile_tmpl;
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename.tpl.txt");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $data;
			exit();
			break;
	}
}

// Get the profile options
$export_profile_id = JRequest::getVar('export_profile_id', '', 'POST', 'string');


if ($export_profile_id == 0) {
	echo "<script type='text/javascript'> alert('You must select an export profile first.'); window.history.go(-1); </script>\n";
	exit();
}

$database->setQuery("SELECT * FROM #__support_export_profile WHERE id=" . $database->quote($export_profile_id));
$export_profile_options = null;
$export_profile_options = $database->loadObject();
echo $database->getErrorMsg();

$client = JRequest::getInt('client', 0);
$export_type = $export_profile_options->export_type;
$id_user = JRequest::getInt('id_user', 0);
$id_workgroup = JRequest::getInt('selwk', 0);
$fileformat = JRequest::getVar('fileformat', 'CSV', 'POST', 'string');
$status = JRequest::getInt('id_export_statuses', 0);
$billable_only = $export_profile_options->billableonly;
$auto_save = $export_profile_options->auto_save;
$update_exported = $export_profile_options->update_exported;
$year = JRequest::getVar('year', '', 'POST', 'string');
$month = JRequest::getVar('month', '', 'POST', 'string');


$log_export_options = '';


// Workgroup export filter
if ($id_workgroup > 0) {
	$id_workgroup_sql = " AND t.id_workgroup='" . $id_workgroup . "'";
	$log_export_options .= '<b>' . JText::_('workgroup') . '</b>: ' . HelpdeskDepartment::GetName($id_workgroup) . "\n";

} else {
	$id_workgroup_sql = '';
	$log_export_options .= "<b>" . JText::_('workgroup') . "</b>: " . JText::_('all_workgroups') . "\n";
}

// Client export filter
if ($client > 0) {
	$client_sql = " AND c.id='" . $client . "'";
	$log_export_options .= '<b>' . JText::_('tpl_client') . 'Client</b>: ' . HelpdeskClient::GetName($client) . "\n";
} else {
	$client_sql = '';
	$log_export_options .= "<b>" . JText::_('tpl_client') . "</b>: " . JText::_('all_clients') . "\n";
}

// User export filter
if ($id_user > 0) {
	$id_user_sql = " AND u.id='" . $id_user . "'";
	$database->setQuery("SELECT `name` FROM #__users WHERE id='" . $id_user . "'");
	$id_user_name = $database->loadResult();
	$log_export_options .= '<b>' . JText::_('user') . '</b>: ' . HelpdeskClient::GetName($id_user_name) . "\n";

} else {
	$id_user_sql = '';
	$log_export_options .= "<b>" . JText::_('user') . "</b>: " . JText::_('all_users') . "\n";
}

// Status export filter
if ($status == "0") {
	$status_sql = "";
	$log_export_options .= "<b>" . JText::_('tpl_status') . "</b>: " . JText::_('all_status') . "\n";

} elseif ($status == 'C') {
	$status_sql = " AND s.status_group = 'C'";
	$log_export_options .= "<b>" . JText::_('tpl_status') . "</b>: " . JText::_('all_close_status') . "\n";

} elseif ($status == 'O') {
	$status_sql = " AND s.status_group = 'O'";
	$log_export_options .= "<b>" . JText::_('tpl_status') . "</b>: " . JText::_('all_open_status') . "\n";

} elseif ($status > 0) {
	$status_sql = " AND t.id_status = '" . $status . "'";
	$log_export_options .= "<b>" . JText::_('tpl_status') . "</b>: " . HelpdeskStatus::GetName($status) . "\n";

} else exit();

// Billable only jobs filter (positive labour time)
if ($billable_only > 0) {
	$billable_sql = "AND ((TIME_TO_SEC( (REPLACE(r.timeused,'.', ':' )) ))/3600) > 0 ";
} else {
	$billable_sql = "AND t.last_update ";
}


if ($year != '00' & $month != '00') {
	if ($export_type == "T") {
		$ticket_sql = " AND year(t.last_update)='" . intval($year) . "' AND month(t.last_update)='" . intval($month) . "' ";
	} else {
		$ticket_sql = " AND year(r.date)='" . intval($year) . "' AND month(r.date)='" . intval($month) . "' ";
	}
} else {
	if ($export_type == "T") {
		$ticket_sql = "AND t.last_update ";
	} else {
		$ticket_sql = "AND r.date ";
	}
}

//	SQL query based on the export type
switch ($export_type) {
	case 'T': // Tickets related export
		$available = array('', 'db_id', 'ticket_num', 'ticket_subj', 'ticket_msg', 'client_name', 'customer_name', 'assigned_id', 'assigned_name', 'ticket_status', 'ticket_status_grp', 'ticket_pri', 'ticket_pri_timevalue', 'ticket_pri_timeunit', 'ticket_cat', 'ticket_last_update', 'ticket_source', 'ticket_duedate', 'ticket_create', 'extra_field', 'workgroup');
		$sql = "
				SELECT 
					t.id AS db_id, 
					t.ticketmask AS ticket_num, 
					t.subject AS ticket_subj,
					t.message AS ticket_msg,
					c.clientname AS client_name, 
					t.an_name AS customer_name, 
					t.assign_to AS assigned_id,
					u2.name AS assigned_name,
					s.description AS ticket_status,
					s.status_group AS ticket_status_grp,
					pr.description AS ticket_pri,
					pr.timevalue AS ticket_pri_timevalue,
					pr.timeunit AS ticket_pri_timeunit,
					cat.name AS ticket_cat,
					t.last_update AS ticket_last_update,
					t.source AS ticket_source,
					t.duedate AS ticket_duedate,
					t.date AS ticket_create,
					ext.newfield AS extra_field,
					w.wkdesc AS workgroup
				FROM #__support_ticket t 
				LEFT JOIN #__users u ON u.id = t.id_user 
				LEFT JOIN #__users u2 ON u2.id = t.assign_to 
				LEFT JOIN #__support_client_users cu ON cu.id_user = u.id 
				LEFT JOIN #__support_client c ON cu.id_client = c.id 
				LEFT JOIN #__support_status s ON t.id_status = s.id 
				LEFT JOIN #__support_priority pr ON t.id_priority = pr.id 
				LEFT JOIN #__support_category cat ON t.id_category = cat.id
				LEFT JOIN #__support_field_value ext ON t.id = ext.id_ticket
				LEFT JOIN #__support_workgroup w ON t.id_workgroup = w.id
				WHERE t.id_export = '" . $id_export . "' " . $status_sql . " " . $client_sql . " " . $id_user_sql . " " . $id_workgroup_sql . " " . $ticket_sql . "
				ORDER BY t.id";
		break;

	case 'C': // Clients related export
		$available = array('', 'client_id', 'client_created_date', 'client_name', 'client_desc', 'client_block', 'client_address', 'client_zip', 'client_city', 'client_state', 'client_country', 'client_phone', 'client_fax', 'client_mobile', 'client_email', 'client_contact', 'client_website', 'client_travel_time', 'client_rate');
		$sql = "
				SELECT 
					c.id AS client_id,
					c.date_created AS client_created_date, 
					c.clientname  AS client_name, 
					c.description AS client_desc,
					c.block AS client_block,
					c.address AS client_address,
					c.zipcode AS client_zip,
					c.city AS client_city,
					c.state AS client_state,
					c.country AS client_country,
					c.phone AS client_phone,
					c.fax AS client_fax,
					c.mobile AS client_mobile,
					c.email AS client_email,
					c.contactname AS client_contact,
					c.website AS client_website,
					c.travel_time AS client_travel_time,
					c.rate AS client_rate
				FROM #__support_client c 
				WHERE c.id > 0 " . $client_sql . "
				ORDER BY c.clientname";
		break;

	case 'U': // Users related export
		$available = array('', 'user_id', 'user_fullname', 'user_username', 'user_email', 'user_type', 'user_block', 'user_date_regd', 'user_date_lastvisit', 'user_phone', 'user_fax', 'user_mobile', 'client_id', 'client_created_date', 'client_name', 'client_desc', 'client_block', 'client_address', 'client_zip', 'client_city', 'client_state', 'client_country', 'client_phone', 'client_fax', 'client_mobile', 'client_email', 'client_contact', 'client_website', 'client_travel_time', 'client_rate');
		$sql = "
				SELECT 
					u.id AS user_id,
					u.name AS user_fullname,
					u.username AS user_username,
					u.email AS user_email,
					u.usertype AS user_type,
					u.block AS user_block,
					u.registerDate AS user_date_regd,
					u.lastvisitDate AS user_date_lastvisit,
					cu.phone AS user_phone,
					cu.fax AS user_fax,
					cu.mobile AS user_mobile,
					c.id AS client_id,
					c.date_created AS client_created_date, 
					c.clientname  AS client_name, 
					c.description AS client_desc,
					c.block AS client_block,
					c.address AS client_address,
					c.zipcode AS client_zip,
					c.city AS client_city,
					c.state AS client_state,
					c.country AS client_country,
					c.phone AS client_phone,
					c.fax AS client_fax,
					c.mobile AS client_mobile,
					c.email AS client_email,
					c.contactname AS client_contact,
					c.website AS client_website,
					c.travel_time AS client_travel_time,
					c.rate AS client_rate
				FROM #__users u
				LEFT JOIN #__support_client_users cu ON cu.id_user = u.id 
				LEFT JOIN #__support_client c ON cu.id_client = c.id 
				WHERE u.id > 0 " . $client_sql . " " . $id_user_sql . " " . $id_workgroup_sql . "
				ORDER BY c.clientname";
		break;

	default: // Activity related export is our default
		$available = array('', 'db_id', 'ticket_num', 'ticket_subj', 'ticket_msg', 'client_name', 'customer_name', 'assigned_id', 'assigned_name', 'act_id', 'act_author_id', 'act_author_name', 'act_start_time', 'act_finish_time', 'act_labour_time', 'act_labour_time_decimal', 'act_breaks_time', 'act_breaks_time_decimal', 'act_travel_time', 'act_travel_time_decimal', 'act_total_time', 'act_total_time_decimal', 'client_rate', 'act_summary_msg', 'act_msg', 'act_type_name', 'act_rate', 'act_rate_desc', 'act_rate_multip', 'act_date', 'act_author_id', 'act_author_name', 'workgroup');
		$sql = "
					SELECT 
						t.id AS db_id, 
						t.ticketmask AS ticket_num, 
						t.subject AS ticket_subj,
						t.message AS ticket_msg,
						t.id_workgroup as workgroup_id,
						c.clientname AS client_name, 
						t.an_name AS customer_name, 
						t.assign_to AS assigned_id,
						u2.name AS assigned_name,
						r.id AS act_id,
						r.user_rate AS client_rate,
						r.reply_summary as act_summary_msg,
						r.message AS act_msg, 
						y.description AS act_type_name, 
						(ra.multiplier * r.user_rate) AS act_rate,
						ra.description AS act_rate_desc, 
						ra.multiplier AS act_rate_multip,
						r.date AS act_date, 
						r.id_user as act_author_id,
						u3.name AS act_author_name,
						r.start_time AS act_start_time, 
						r.end_time AS act_finish_time, 
						(REPLACE(r.timeused,'.', ':' )) AS act_labour_time,
						((TIME_TO_SEC( (REPLACE(r.timeused,'.', ':' )) ))/3600)  AS act_labour_time_decimal, 
						(REPLACE(r.break_time,'.', ':' )) AS act_breaks_time,
						((TIME_TO_SEC( (REPLACE(r.break_time,'.', ':' )) ))/3600)  AS act_breaks_time_decimal, 
						(REPLACE(r.tickettravel,'.', ':' )) AS act_travel_time, 
						((TIME_TO_SEC( (REPLACE(r.tickettravel,'.', ':' )) ))/3600)  AS act_travel_time_decimal, 
						TIME_FORMAT(
							(SEC_TO_TIME(
							(TIME_TO_SEC(REPLACE(r.timeused,'.', ':' ))) 
							+ 
							(TIME_TO_SEC(REPLACE(r.tickettravel,'.', ':' )))
							)), '%H:%i'
							) AS act_total_time,
						((TIME_TO_SEC( (REPLACE(r.timeused,'.', ':' )) ) + (TIME_TO_SEC((REPLACE(r.tickettravel,'.', ':' )))))/3600) AS act_total_time_decimal,
						w.wkdesc AS workgroup
					FROM #__support_ticket t 
					INNER JOIN #__support_ticket_resp r ON r.id_ticket = t.id 
					LEFT JOIN #__support_activity_rate ra ON r.id_activity_rate = ra.id 
					LEFT JOIN #__support_activity_type y ON r.id_activity_type = y.id 
					LEFT JOIN #__users u ON u.id = t.id_user 
					LEFT JOIN #__users u2 ON u2.id = t.assign_to 
					LEFT JOIN #__users u3 ON u3.id = r.id_user 
					LEFT JOIN #__support_client_users cu ON cu.id_user = u.id 
					LEFT JOIN #__support_client c ON cu.id_client = c.id 
					LEFT JOIN #__support_permission p ON r.id_user = p.id_user AND t.id_workgroup = p.id_workgroup
					LEFT JOIN #__support_status s ON t.id_status = s.id
					LEFT JOIN #__support_workgroup w ON t.id_workgroup = w.id					
					WHERE t.id_export = '0' " . $billable_sql . " " . $status_sql . " " . $client_sql . " " . $id_user_sql . " " . $id_workgroup_sql . " " . $ticket_sql . "
					ORDER BY t.id ";
		break;
}

if ($sql) {
	$database->setQuery($sql);
	$exports = $database->loadObjectList();
	$num_records = count($exports);
}

$cr = "\r\n";
$split_delimiter = " ";
$data = '';
$tickets = '';
$exp_tags = null;

$line = $export_profile_options->export_tmpl;
$line_tmp = $line;

// Quit if no records were found
if ($num_records < 0) {
	echo "<script type='text/javascript'> alert('Nothing to Export.'); window.history.go(-1); </script>\n";
	exit();
}

// For use in RegexBuddy: <tag:([^{/\s]+)({S}(\d+){/S})?({L}(\d+){/L})?({D}([^{]+){/D})?\s*/>
// $export_tags[0] <- whole tag from <tag to />
// $export_tags[1] <- variable (ie assigned_name);
// $export_tags[2] <- splitter whole block (if exists then split)
// $export_tags[3] <- splitter number
// $export_tags[4] <- limiter whole block (if exists then limit)
// $export_tags[5] <- limiter number
// $export_tags[6] <- date re-format whole block (if exists then format)
// $export_tags[7] <- date re-format instructions
preg_match_all('/<tag:([^{\/\\s]+)({S}(\\d+){\/S})?({L}(\\d+){\/L})?({D}([^{]+){\/D})?\\s*\/>/', $line, $exp_tags, PREG_SET_ORDER);

// Get ready for processing h:m to decimal conversion
//$available_decimals = array('act_travel_time_decimal', 'act_breaks_time_decimal', 'act_labour_time_decimal', 'act_total_time_decimal');

// Process each export record and each export template tag
foreach ($exports as $export) {
	foreach ($exp_tags as $exp_tag) {
		if (array_search($exp_tag[1], $available)) { // Make sure we only process the valid tags

			/* depriciated as its now done in mysql above
			   // Processing for decimal tags, which are not in the SQL table as same var names
			   if ( array_search($exp_tag[1], $available_decimals) ) {
				   $tmp_time = $export->$exp_tag[1];
				   if ( strpos($tmp_time, '.') ) {
					   $delim = '.';
				   }elseif ( strpos($tmp_time, ':') ) {
					   $delim = ':';
				   }
				   if ($delim) {
					   $tmp_hours = JString::substr($tmp_time, 0, strpos($tmp_time, $delim));
					   $tmp_mins = JString::substr($tmp_time, strpos($tmp_time, $delim)+1, strlen($tmp_time));
					   $tmp_mins_dec = $tmp_mins / 60;
					   $tmp_time_dec = round(($tmp_hours + $tmp_mins_dec), 2);
					   $export->$exp_tag[1] = $tmp_time_dec;
				   }
			   }  */

			$exp_value = $export->$exp_tag[1]; // Get the rest of values which are in SQL under same names as vars

			// Character splitter {S}{/S} processing
			if (!empty($exp_tag[2]) && !empty($exp_tag[3])) {
				if (strpos($exp_value, $split_delimiter)) {
					$exp_value_split = explode($split_delimiter, $exp_value);
					$tmp_num = $exp_tag[3] - 1; // Reduce the splitter number by 1 as explode starts from 0
					!empty($exp_value_split[$tmp_num]) ? $exp_value = $exp_value_split[$tmp_num] : '';
				}
			}

			// Character limiter {L}{/L} processing
			if (!empty($exp_tag[4]) && (!empty($exp_tag[5]) && $exp_tag[5] > 0)) {
				$exp_value = JString::substr($exp_value, 0, $exp_tag[5]);
			}

			// Date format {D}{/D} processing
			if (!empty($exp_tag[6])) { // Date re-format check
				if (empty($exp_tag[7])) { // Custom date format check
					$supportConfig->date_short ? $tmp_format = $supportConfig->date_short : $tmp_format = 'd/m/Y';
				} else {
					$tmp_format = $exp_tag[7];
				}
				// Fail check from date()
				if ($exp_value) {
					if (!$tmp_timestamp = HelpdeskDate::ParseDate($exp_value, "%Y-%m-%d %H:%M:%S")) {
						$tmp_timestamp = strtotime($exp_value);
					}
				}
				if ($exp_value_tmp = date($tmp_format, $tmp_timestamp)) {
					$exp_value = $exp_value_tmp;
				}
			}

			// Now thats the value is processed, replace the tag with it
			$line_tmp = str_replace($exp_tag[0], $exp_value, $line_tmp);
		}
	}

	// Take care of line breaks
	// $line_tmp = htmlentities($line_tmp); //make remaining items html entries.
	// $line_tmp = nl2br($line_tmp); //add html line returns
	$line_tmp = str_replace(chr(10), " ", $line_tmp); //remove carriage returns
	$line_tmp = str_replace(chr(13), " ", $line_tmp); //remove carriage returns
	// $line_tmp = str_replace('<br />', '\r\n', $line_tmp);

	$data .= $line_tmp . $cr;
	$line_tmp = $line;

	// Build the list of ticket numbers if we are exporting Activities or Tickets
	if ($export_type == 'A' || $export_type == 'T') {
		$tickets .= $export->db_id . ',';
	}
}

// Quit if processed data is empty
if (strlen($data) < 1) {
	echo "<script type='text/javascript'> alert('Nothing to Export.'); window.history.go(-1); </script>\n";
	exit();
}

// Save tickets into the export history
if ($auto_save) {
	$my_username = $my->username;
	$database->setQuery("SELECT `name` FROM #__users WHERE id='" . $my->id . "'");
	$my_name = $database->loadResult();
	$export_author = $my_name . ' (' . $my_username . ')';
	//$export_author = 'Chinfrado Datola';

	$database->setQuery("INSERT INTO #__support_export(export_type, export_date, export_author, export_options, profile_name, profile_tmpl, fileformat, num_records, export_data, hits) VALUES(" . $database->quote($export_profile_options->export_type) . ",'" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "'," . $database->quote($export_author) . "," . $database->quote($log_export_options) . "," . $database->quote($export_profile_options->name) . "," . $database->quote($export_profile_options->export_tmpl) . "," . $database->quote($fileformat) . ",'" . $num_records . "'," . $database->quote($data) . ",'1')");
	$database->query();
	echo $database->getErrorMsg();
	$tmp_export_history_id = ' (' . mysql_insert_id() . ')';
} else {
	$tmp_export_history_id = '';
}

// Update tickets as exported
if ($update_exported && ($export_type == 'A' || $export_type == 'T') && (HelpdeskTicket::GetStatusGroup($status) == 'C' || $status == 'C')) {
	$tickets = JString::substr($tickets, 0, strlen($tickets) - 1);

	// Build list of affected ticket numbers
	$database->setQuery("SELECT ticketmask FROM #__support_ticket WHERE id IN (" . $database->quote($tickets) . ")");
	$unlocked_tickets_nums = $database->loadObject();
	if (!$unlocked_tickets_nums) {
//	if (! $database->loadObject( $unlocked_tickets_nums ) ) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$unlocked_tickets_nums_list = '';
	foreach ($unlocked_tickets_nums as $unlocked_ticket_num) {
		$unlocked_tickets_nums_list .= $unlocked_ticket_num . ',';
	}
	$unlocked_tickets_nums_list = JString::substr($unlocked_tickets_nums_list, 0, strlen($unlocked_tickets_nums_list) - 1);

	// Update tickets as exported
	$database->setQuery("UPDATE #__support_ticket SET id_export='" . mysql_insert_id() . "' WHERE id IN (" . $database->quote($tickets) . ")");
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// Add a message in ticket logs
	$prev_id = 0;
	for ($i = 0; $i < count($exports); $i++) {
		$export = $exports[$i];

		if ($prev_id == 0) {
			HelpdeskForm::Log($export->db_id, JText::_('export_locked_message') . $tmp_export_history_id, JText::_('export_locked_message'), '');
		}
		$prev_id = ($export->db_id == $prev_id ? $prev_id : 0);
	}

}
$profile_name_tmp = str_replace(" ", "_", $export_profile_options->name);
$profile_name_tmp = trim(JString::strtolower($profile_name_tmp));
$profile_name_tmp = preg_replace('/[^\\w]+/', '', $profile_name_tmp);

$filename = $profile_name_tmp . '-' . HelpdeskDate::DateOffset("%Y%m%d_%H%M%S");

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;
