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

class HelpdeskTicket
{
	static function GetOrdering($field)
	{
		switch($field)
		{
			case 't.ticketmask':
				$ordering = JText::_("TICKETID");
				break;
			case 't.subject':
				$ordering = JText::_("SUBJECT");
				break;
			case 't.date':
				$ordering = JText::_("CREATED_DATE");
				break;
			case 't.duedate':
				$ordering = JText::_("DUEDATE");
				break;
			case 't.last_update':
				$ordering = JText::_("LAST_UPDATE");
				break;
			case 'w.wkdesc':
				$ordering = JText::_("WORKGROUP");
				break;
			case 's.description':
				$ordering = JText::_("STATUS");
				break;
			case 'u2.name':
				$ordering = JText::_("TKT_CHNG_STAT_NFY_SUP");
				break;
			case 'p.description':
				$ordering = JText::_("PRIORITY");
				break;
			case 'cy.name':
				$ordering = JText::_("CATEGORY");
				break;
			case 'c.clientname':
				$ordering = JText::_("CLIENT_TH");
				break;
			case 't.an_name':
				$ordering = JText::_("USER_NAME");
				break;
			case 't.an_mail':
				$ordering = JText::_("E_MAIL");
				break;
		}

		return $ordering;
	}

	static function Log($id, $msg, $msg_reserved, $id_status = '', $field = '', $value = '0', $time_elapse = '0', $image = '')
	{
		$user = JFactory::getUser();
		$database = JFactory::getDBO();

		// permite que o time elapse se refira a altera??es do mesmo tipo
		if (($field == 'status') || ($field == 'assign') || ($field == 'attachfile') || ($field == 'category') || ($field == 'priority') || ($field == 'rate')) {
			$sql = "SELECT id, UNIX_TIMESTAMP(date_time) AS last_time FROM #__support_log WHERE `id_ticket` = '" . $id . "' AND `field` = '" . $field . "' AND `time_elapse` = 0 ORDER BY `date_time` DESC LIMIT 1";
			$database->setQuery($sql);
			$last_field_changed = $database->loadObject();
			!$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';
			if (count($last_field_changed)) {
				$nowtime = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
				$time_dif = $nowtime - $last_field_changed->last_time;
				$sql = "UPDATE #__support_log SET `time_elapse` =  '" . $time_dif . "' WHERE id='" . $last_field_changed->id . "' ";
				$database->setQuery($sql);
				!$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';
			}
		}

		$sql = "INSERT INTO #__support_log(`id_ticket`, `id_user`, `date_time`, `log`, `log_reserved`, `id_status`, `field`, `value`, `time_elapse`, `image`)
				VALUES('" . $id . "', '" . $user->id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', " . $database->quote($msg) . ", " . $database->quote($msg_reserved) . ", '" . $id_status . "', '" . $field . "', '" . $value . "', 0, '" . $image . "')";
		$database->setQuery($sql);
		!$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';
	}

	static function Reply($id, $reply_summary, $reply_msg, $replytime = 0, $travel_time = 0, $tickettravel = 0, $clientrate = 0, $activity_rate = 0, $activity_type = 0, $start_time = 0, $end_time = 0, $break_time = 0, $reply_date = '', $reply_hours = '')
	{
		global $wkoptions;

		$database = JFactory::getDBO();
		$user = JFactory::getUser();

		if ($reply_date != '')
		{
			$reply_date = $reply_date . ' ' . $reply_hours;
		}
		else
		{
			$reply_date = HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S");
		}

		// For new tickets the system doesnt send the clientrate variable
		// First, get the user of the ticket
		$database->setQuery("SELECT id_user FROM #__support_ticket WHERE id='" . $id . "'");
		$client_user = $database->loadResult();
		// Second, get the rate of the client
		$database->setQuery("SELECT c.rate FROM #__support_client c INNER JOIN #__support_client_users cu ON c.id=cu.id_client WHERE cu.id_user='" . $client_user . "'");
		$clientrate = $database->loadResult();
		// Third, get the multiplier of the activity rate
		$database->setQuery("SELECT multiplier FROM #__support_activity_rate WHERE id='" . $activity_rate . "'");
		$multiplier = $database->loadResult();

		$sql = "INSERT INTO #__support_ticket_resp(id_ticket, id_user, `date`, message, timeused, travel_time, tickettravel, id_activity_rate, id_activity_type, user_rate, start_time, end_time, break_time, reply_summary)
				VALUES('" . $id . "', '" . $user->id . "', '" . $reply_date . "', " . $database->quote($reply_msg) . ", " . $database->quote(str_replace(':', '.', $replytime)) . ", " . $database->quote($travel_time) . ", " . $database->quote(str_replace(':', '.', $tickettravel)) . ", '" . $activity_rate . "', '" . $activity_type . "', '" . $clientrate . "', " . $database->quote($start_time) . ", " . $database->quote($end_time) . ", " . $database->quote($break_time) . ", " . $database->quote($reply_summary) . ")";
		$database->setQuery($sql);
		$database->query();
		$reply_id = $database->insertid();

		if ($database->getErrorMsg() == '') {
			// Update Contracts
			$contract = HelpdeskContract::Get($client_user);
			if (isset($contract)) {
				if ($contract != false) {
					switch ($contract->unit) {
						case 'H' :
							// Get actual time
							$sql = "SELECT actual_value
									FROM #__support_contract
									WHERE id=" . $contract->id;
							$database->setQuery($sql);
							$actual_value = $database->loadResult();

							// Update actual time
							$actual_value = HelpdeskDate::ConvertHoursMinutesToDecimal($replytime) + $actual_value;
							$sql = "UPDATE #__support_contract
									SET actual_value=" . $actual_value . "
									WHERE id=" . $contract->id;
							$database->setQuery($sql);
							if (!$database->query()) {
								HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
								echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
								exit();
							}
					}

					HelpdeskContract::MakeInactive($contract->id);
				}
			}
			// Calls the add-on's engine
			HelpdeskAddon::Execute(2, 0, $id, $reply_id);
			return $reply_id;
		} else {
			HelpdeskUtility::AddGlobalMessage('Ticket reply not saved.<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
			return 0;
		}
	}

	static function BuildStatusList($id_status, $old_id_status, $selectfield = true, $id = 0, $id_workgroup = 0, $bulk = false)
	{
		$database = JFactory::getDBO();
		$is_support = HelpdeskUser::IsSupport();

		// Build Status select list
		$clause = "";
		if (!$is_support) {
			$clause = " WHERE (user_access = '1' OR (user_access = '0' AND id = '" . $id_status . "')) ";

			$sql = "SELECT `allow_old_status_back` FROM #__support_status WHERE id = '" . $id_status . "' ";
			$database->setQuery($sql);
			$allow_old_status_back = $database->loadResult();

			// lista de estados que o actual estado permite selecionar
			$sql = "SELECT `status_workflow` FROM #__support_status WHERE `id` = '" . $id_status . "' AND `status_workflow` != '0' AND `status_workflow` != ''";
			$database->setQuery($sql);
			$status_workflow = $database->loadResult();
			$status_checked = explode("#", $status_workflow);

			if ($status_workflow != '') {
				$list = "";
				if ($allow_old_status_back == 1) {
					$list .= $old_id_status . ",";
				}

				for ($i = 0; $i < count($status_checked); $i++)
				{
					if (($allow_old_status_back == 1) && ($status_checked[$i] != $old_id_status)) {
						$list .= $status_checked[$i] . ",";
					//} else {
					//	$list .= $status_checked[$i] . ",";
					}
				}

				if ($list != "") {
					$list = substr($list, 0, -1);
					$list_status_allowed = "(" . $id_status . ($list != '' ? ',' : '') . $list . ")";
					$clause .= ($clause == "") ? " WHERE " : " AND ";
					$clause .= " `id` IN " . $list_status_allowed . " ";
				}
			}
		}

		$sql = "SELECT `id` AS value, `description` AS text
				FROM #__support_status " . $clause . "
				ORDER BY `ordering`, `description`";
		$sql = str_replace(',,', ',', $sql);
		$database->setQuery($sql);
		$rows_status = $database->loadObjectList();

		if ($selectfield) {
			return JHTML::_('select.genericlist', $rows_status, 'id_status', 'class="span10" size="1"', 'value', 'text', $id_status);
		} else {
			$html = '';
			for ($i = 0; $i < count($rows_status); $i++) {
				$row = $rows_status[$i];
				// TODO - Place color of the status
				if ($bulk)
				{
					$html .= '<li><a href="javascript:;" onclick="SetTicketStatusBulk(' . $row->value . ');">' . $row->text . '</a></li>';
				}
				else
				{
					$html .= '<li><a href="javascript:;" onclick="SetTicketStatus(' . $id . ',' . $row->value . ',' . $id_workgroup . ');">' . $row->text . '</a></li>';
				}
			}
			return $html;
		}
	}

	static function IgnoreSearch($value)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$ignore = explode(' ', $supportConfig->ticket_ignore_letter);
		if (in_array($value, $ignore)) {
			return false;
		} else {
			return true;
		}
	}

	static function GetMessages($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_ticket='" . $id . "'");
		return $database->loadResult();
	}

	static function VerifySource($source)
	{
		switch ($source) {
			case 'P':
				break;
			case 'F':
				break;
			case 'M':
				break;
			case 'W':
				break;
			case 'O':
				break;
			case 'A':
				break;
			case 'T':
				break;
			default :
				$source = 'O';
				break;
		}
		return $source;
	}

	static function SwitchSource($src)
	{
		$source = '';
		switch ($src) {
			case 'P':
				$source = JText::_('phone');
				break;
			case 'F':
				$source = JText::_('fax');
				break;
			case 'M':
				$source = JText::_('email');
				break;
			case 'W':
				$source = JText::_('website');
				break;
			case 'O':
				$source = JText::_('other');
				break;
			case 'A':
				$source = 'Facebook';
				break;
			case 'T':
				$source = 'Twitter';
				break;
		}
		return $source;
	}

	static function GetID($value)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `id`
				FROM #__support_ticket 
				WHERE `ticketmask`='" . $value . "'";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	static function GetAssign($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name FROM #__users WHERE id='$id'");
		return ($database->loadResult() != '' ? $database->loadResult() : JText::_('ticket_unassigned_status'));
	}

	static function GetStatusGroup($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT status_group FROM #__support_status WHERE id='$id'");
		return $database->loadResult();
	}

	static function ReturnDueDate($year, $month, $day, $hour, $minute, $priority)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__support_priority WHERE id='$priority'");
		$hours = null;
		$hours = $database->loadObject();
		$due_date = mktime($hour + ($hours->timeunit == 'H' ? $hours->timevalue : 0), $minute, 0, $month, $day + ($hours->timeunit == 'D' ? $hours->timevalue : 0), $year);
		return date("Y-m-d H:i", $due_date);
	}

	static function IsDueDateValid($duedate, $priority, $status, $replies, $assign, $style)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		$database->setQuery("SELECT status_group FROM #__support_status WHERE id='$status'");
		$status = $database->loadResult();

		$due_date = mktime(substr($duedate, 11, 2), substr($duedate, 14, 2), 0, substr($duedate, 5, 2), substr($duedate, 8, 2), substr($duedate, 0, 4));
		$cur_date = mktime((int) HelpdeskDate::DateOffset("%H"), (int) HelpdeskDate::DateOffset("%M"), 0, (int) HelpdeskDate::DateOffset("%m"), (int) HelpdeskDate::DateOffset("%d"), (int) HelpdeskDate::DateOffset("%Y"));

		if ($status == 'C') {
			$img = 'ok.png';
			$alt = JText::_('icon_closed');
		} elseif ($assign > 0 && $due_date <= $cur_date) {
			$img = 'clock.png';
			$alt = JText::_('icon_overdue');
		} elseif ($assign > 0 && $due_date >= $cur_date) {
			$img = 'hour.png';
			$alt = JText::_('icon_progress');
		} elseif ($assign == 0) {
			$img = 'alert.png';
			$alt = JText::_('icon_onhold');
		}

		switch ($style) {
			case '0':
				return ' <img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . $img . '" border="0" align="absmiddle" alt="' . $alt . '" /> ';
			case '1':
				return $alt;
			case '2':
				return $img;
		}
	}

	static function NumberOfAttachments($id)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT COUNT(*)
				FROM `#__support_file`
				WHERE `id`='" . $id . "' AND `source`='T'";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	static function GetMessageAttachments($id, $reply)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `id`, `id_file`, `filename`, `description`
				FROM `#__support_file`
				WHERE `id`='" . $id . "' AND `id_reply`='" . ($reply==$id ? 0 : $reply) . "' AND `source`='T'";
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	static function GetTicketField($id, $field='', $customfield=0)
	{
		$database = JFactory::getDBO();

		if ($field != '')
		{
			$sql = "SELECT `$field`
					FROM `#__support_ticket`
					WHERE `id`=" . (int) $id;
		}else{
			$sql = "SELECT `newfield`
					FROM `#__support_field_value`
					WHERE `id_ticket`=" . (int) $id . "`id_field`=" . (int) $customfield;
		}
		$database->setQuery($sql);
		$value = $database->loadResult();

		return $value;
	}
}
