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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

switch ($task)
{
	case "imageupload":
		MQMImageUpload();
		break;
	case "javascript":
		MQMJavascriptHeader();
		break;
	case "cometchat":
		LogCometChat();
		break;
	case "getclient":
		GetClientAjax();
		break;
	case "getuser":
		GetUserAjax();
		break;
	case "duedate":
		GetDuedateAjax();
		break;
	case "getusermails":
		GetUserMailsAjax();
		break;
	case "rating":
		GetRating();
		break;
	case "merge":
		MergeTicket();
		break;
	case "asreply":
		AsReplyTicket();
		break;
	case "priority":
		getWorkgroupPriority();
		break;
	case "reply":
		getDefinedReply();
		break;
	case "analysis":
		GetAnalysis();
		break;
	case "cvs":
		GetCVS();
		break;
	case "checkuser":
		MQMCheckUser();
		break;
}

function MQMCheckUser()
{
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();

	if (!$is_support)
	{
		return 1;
	}

	$username = JRequest::getVar('username', '', '', 'string');
	$email = JRequest::getVar('email', '', '', 'string');

	// Check username
	$sql = "SELECT COUNT(*)
			FROM `#__user`
			WHERE `username`='$username'";
	$database->setQuery($sql);
	$username_check = $database->loadResult();

	if ($username_check)
	{
		return 2; // Username exists
	}

	// Check e-mail
	$sql = "SELECT COUNT(*)
			FROM `#__user`
			WHERE `email`='$email'";
	$database->setQuery($sql);
	$email_check = $database->loadResult();

	if ($email_check)
	{
		return 3; // Email exists
	}

	return 0; // Username and email doesnt exist
}

function MQMImageUpload()
{
	header("Content-type: text/json");
	$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

	if ($_FILES['file']['type'] == 'image/png'
		|| $_FILES['file']['type'] == 'image/jpg'
		|| $_FILES['file']['type'] == 'image/gif'
		|| $_FILES['file']['type'] == 'image/jpeg'
		|| $_FILES['file']['type'] == 'image/pjpeg')
	{
		// setting file's mysterious name
		$filename = md5(date('YmdHis')).'.jpg';
		$file = JPATH_SITE.'/images/'.$filename;

		// copying
		move_uploaded_file($_FILES['file']['tmp_name'], $file);

		// displaying file
		$array = array(
			'filelink' => '/'.$filename
		);

		echo stripslashes(json_encode($array));
	}
}

function MQMJavascriptHeader()
{
	header("Content-type: text/javascript");
	$Itemid = JRequest::getInt('Itemid', 0);
	$jquery_url = HelpdeskUtility::GetJQueryURL();
	$supportConfig = HelpdeskUtility::GetConfig();
	echo 'var SITEURL = "' . JURI::root() . '";'."\n";
	echo 'var LOADJQUERY = "' . ($jquery_url != '' ? 'true' : 'false') . '";'."\n";
	echo 'var MQM_ITEMID = "' . $Itemid . '";'."\n";
	echo 'var IMQM_ICON_THEME = "' . $supportConfig->theme_icon . '";'."\n";
	echo 'var IMQM_ANALYTICS = "' . $supportConfig->google_adwords . '";'."\n";
	echo 'var IMQM_CLOSE = "' . JText::_("CLOSE") . '";'."\n";
	echo 'var IMQM_REPLIES_TITLE = "' . JText::_("PREDEFINED_REPLIES") . '";'."\n";
	die();
}

function LogCometChat()
{
	$usertable = TABLE_PREFIX . DB_USERTABLE;
	$usertable_username = DB_USERTABLE_NAME;
	$usertable_userid = DB_USERTABLE_USERID;
	$is_support = HelpdeskUser::IsSupport();

	if (!$is_support)
	{
		return;
	}

	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$data = JRequest::getInt('chat', 0);
	$data = preg_split("/,/", base64_decode($data));

	$sql = "SELECT m1.*, f.$usertable_username fromu, t.$usertable_username tou
			FROM cometchat m1, $usertable f, $usertable t
			WHERE  f.$usertable_userid = m1.from
			  AND t.$usertable_userid = m1.to
			  AND ((m1.from = '" . $user->id . "') OR (m1.to = '" . $user->id . "'))
			  AND m1.id >= $data[0]
			  AND m1.id < $data[1]
			ORDER BY id";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	echo 'Chat logged into ticket.';
}

function GetCVS()
{
	include JPATH_SITE . '/components/com_maqmahelpdesk/includes/makecvs.php';
}

function GetAnalysis()
{
	include JPATH_SITE . '/components/com_maqmahelpdesk/includes/analysis.php';
}

function getDefinedReply()
{
	$database = JFactory::getDBO();
	$id = JRequest::getInt('id', 0);

	$sql = "SELECT `answer`
			FROM #__support_reply
			WHERE `id`=" . $id;
	$database->setQuery($sql);
	echo $database->loadResult();
}

function getWorkgroupPriority()
{
	$database = JFactory::getDBO();
	$id = JRequest::getInt('id', 0);

	$sql = "SELECT id_priority
			FROM #__support_workgroup
			WHERE id=" . $id;
	$database->setQuery($sql);

	if (!$database->loadResult())
	{
		$sql = "SELECT id
				FROM #__support_priority
				WHERE isdefault='1'";
		$database->setQuery($sql);
	}

	echo $database->loadResult();
}

function AsReplyTicket()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$id_to = HelpdeskTicket::GetID(JRequest::getInt('id_to', 0));
	$id_from = JRequest::getInt('id_from', 0);

	if ($id_to && $id_from)
	{
		$database->setQuery("UPDATE `#__support_ticket_resp` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_log` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_task` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_file` SET `id`='" . $id_to . "' WHERE `id`='" . $id_from . "' AND `source`='T'");
		$database->query();
		$database->setQuery("UPDATE `#__support_note` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("SELECT `id_user`, `message`, `date`, `ticketmask` FROM `#__support_ticket` WHERE `id`='" . $id_from . "'");
		$ticketFrom = $database->loadObject();
		$database->setQuery("INSERT INTO `#__support_ticket_resp`(`id_ticket`, `id_user`, `date`, `message`)
							  VALUES('" . $id_to . "', '" . $ticketFrom->id_user . "', '" . $ticketFrom->date . "', '" . $ticketFrom->message . "')");
		$database->query();
		$database->setQuery("DELETE FROM `#__support_ticket` WHERE `id`='" . $id_from . "'");
		$database->query();

		$msg_normal = sprintf(JText::_('as_reply_log'), $ticketFrom->ticketmask, HelpdeskUser::GetName($user->id));
		$msg_hidden = sprintf(JText::_('as_reply_log_hidden'), $ticketFrom->ticketmask);
		HelpdeskTicket::Log($id_to, $msg_normal, $msg_hidden, 0, '', 0, 0, 'merge.png');

		echo $id_to;
	}
	else
	{
		return false;
	}
}

function MergeTicket()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$id_to = JRequest::getInt('id_to', 0);
	$id_from = HelpdeskTicket::GetID(JRequest::getInt('id_from', 0));

	if ($id_to && $id_from)
	{
		$database->setQuery("UPDATE `#__support_ticket_resp` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_log` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_task` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("UPDATE `#__support_file` SET `id`='" . $id_to . "' WHERE `id`='" . $id_from . "' AND `source`='T'");
		$database->query();
		$database->setQuery("UPDATE `#__support_note` SET `id_ticket`='" . $id_to . "' WHERE `id_ticket`='" . $id_from . "'");
		$database->query();
		$database->setQuery("SELECT `message` FROM `#__support_ticket` WHERE `id`='" . $id_from . "'");
		$message = $database->loadResult();
		$database->setQuery("UPDATE `#__support_ticket` SET `message`=CONCAT(`message`,'<p><i><u>" . JText::_('merged_from') . "</u></i><p/>','" . $message . "') WHERE `id`='" . $id_to . "'");
		$database->query();
		$database->setQuery("DELETE FROM `#__support_ticket` WHERE `id`='" . $id_from . "'");
		$database->query();
		$msg_normal = sprintf(JText::_('merge_log'), JRequest::getInt('id_from', 0), HelpdeskUser::GetName($user->id));
		$msg_hidden = sprintf(JText::_('merge_log_hidden'), JRequest::getInt('id_from', 0));
		HelpdeskForm::Log($id_to, $msg_normal, $msg_hidden, 0, '', 0, 0, 'merge.png');

		return true;
	}
	else
	{
		return false;
	}
}

function GetRating()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$task2 = JRequest::getVar('task2', '', 'GET', 'string');
	$id = JRequest::getVar('id', 0, 'GET', 'int');
	$rating = JRequest::getVar('rating', '', 'POST', 'int');
	$id_user = JRequest::getVar('id_user', '', 'GET', 'int');

	switch ($task2)
	{
		case 'kb_rate':
			$sql = "SELECT COUNT(*)
					FROM #__support_rate
					WHERE source='K' AND id_table='" . $id . "' AND id_user='" . $id_user . "'";
			$database->setQuery($sql);
			if ($database->loadResult() == 0 && $id_user > 0)
			{
				$sql = "INSERT INTO #__support_rate(id_table, source, id_user, date, rate)
						VALUES('" . $id . "', 'K', '" . $id_user . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', '" . $rating . "')";
				$database->setQuery($sql);
				$database->query();
			}
			elseif ($id_user == 0)
			{
				$sql = "INSERT INTO #__support_rate(id_table, source, id_user, date, rate)
						VALUES('" . $id . "', 'K', '0', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', '" . $rating . "')";
				$database->setQuery($sql);
				$database->query();
			}
			break;

		case 'ticket_rate':
			$database->setQuery("SELECT COUNT(*) FROM #__support_rate WHERE source='T' AND id_table='" . $id . "' AND id_user='" . $user->id . "'");
			if ($database->loadResult() == 0 && $user->id > 0)
			{
				$sql = "INSERT INTO #__support_rate(id_table, source, id_user, date, rate)
						VALUES('" . $id . "', 'T', '" . $user->id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', '" . $rating . "')";
				$database->setQuery($sql);
				$database->query();
			}
			elseif ($user->id == 0)
			{
				$sql = "INSERT INTO #__support_rate(id_table, source, id_user, date, rate)
						VALUES('" . $id . "', 'T', '0', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', '" . $rating . "')";
				$database->setQuery($sql);
				$database->query();
			}
			HelpdeskForm::Log($id, JText::_('ticket_rated'), JText::_('ticket_rated'), '', 'rate', $rating);
			break;
	}
}

function GetUserMailsAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$id_client = HelpdeskUser::IsClient();

	if (!$userl->id || !$supportConfig->extra_email_notification)
	{
		return false;
	}

	$data = '';
	$where = '';
	$name = mysql_escape_string(trim($_GET["q"]));
	if (!$name)
	{
		return;
	}

	if (!$is_support && $supportConfig->extra_email_notification < 2)
	{
		$where = "AND u.id IN (SELECT cu.`id_user` FROM `#__support_client_users` AS cu WHERE cu.`id_client`=" . $id_client . ")";
	}

	// Search Users and Clients tables
	$sql = "SELECT u.name, u.email
			FROM #__users u 
			WHERE u.block=0
			   AND (UCASE(u.name) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.email) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.username) LIKE UCASE('%" . ($name) . "%'))
			   $where
			ORDER BY u.name, u.email 
			LIMIT 0, 10";
	$database->setQuery($sql);
	$users = $database->loadObjectList();

	for ($i = 0; $i < count($users); $i++)
	{
		$userr = $users[$i];
		$data .= $userr->email . '|' . $userr->name . "\n";
	}

	echo $data;
}

function GetDuedateAjax()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();

	// Get parameters
	$id_priority = JRequest::getVar('p', 0, '', 'int');
	$id_supportuser = JRequest::getVar('s', 0, '', 'int');
	$id_workgroup = JRequest::getVar('w', 0, '', 'int');

	// Get default priority if none is set
	if (!$id_priority)
	{
		$sql = "SELECT `id`
				FROM `#__support_priority`
				WHERE `isdefault`=1";
		$database->setQuery($sql);
		$id_priority = $database->loadResult();
	}

	//echo "<p>id_priority: $id_priority</p>";

	// Get priority details
	$sql = "SELECT `timevalue`, `timeunit`
			FROM `#__support_priority`
			WHERE `id`=" . $id_priority;
	$database->setQuery($sql);
	$priority = $database->loadObject();

	//echo "<p>priority:</p>";
	//print_r($priority);
	//echo "<hr>";

	// Get conditions
	$condition_algoritm         = $supportConfig->duedate_algoritm;
	$condition_firstday         = $supportConfig->duedate_firstday;
	$condition_firstday_minimum = $supportConfig->duedate_firstday_minimum;
	$condition_schedule         = $supportConfig->duedate_schedule;
	$condition_hoursday         = $supportConfig->duedate_hoursday;
	$condition_holidays         = $supportConfig->duedate_holidays;
	$condition_vacations        = $supportConfig->duedate_vacations;
	$condition_schedule_default = $supportConfig->id_schedule_default;

	//echo "<p>condition_algoritm: $condition_algoritm</p>";
	//echo "<p>condition_schedule: $condition_schedule</p>";
	//echo "<p>id_supportuser: $id_supportuser</p>";

	// Get support user schedule (or default schedule)
	if ($condition_schedule && $id_supportuser)
	{
		$sql = "SELECT `id_schedule`
				FROM `#__support_permission`
				WHERE `id_workgroup`=" . $id_workgroup . " AND `id_user`=" . $id_supportuser;
		$database->setQuery($sql);
		$condition_schedule_default = ($database->loadResult()>0 ? $database->loadResult() : $condition_schedule);
	}
	//echo "<p>condition_schedule_default: $condition_schedule_default</p>";

	// Get schedule details
	$sql = "SELECT `weekday`, `work_start`, `work_end`, `break_start`, `break_end`
			FROM `#__support_schedule_weekday`
			WHERE `id_schedule`=" . $condition_schedule_default . "
			ORDER BY `weekday` ASC";
	$database->setQuery($sql);
	$schedule = $database->loadObjectList();

	// Start the calculation
	if ($condition_algoritm)
	{
		if ($priority->timeunit == 'D' || !$condition_schedule || !$condition_schedule_default)
		{
			$data_termo = HelpdeskDate::DueDateOffset("%Y-%m-%d %H:%M:%S", mktime((int) HelpdeskDate::DueDateOffset("%H"), (int) HelpdeskDate::DueDateOffset("%M"), (int) HelpdeskDate::DueDateOffset("%S"), (int) HelpdeskDate::DueDateOffset("%m"), (int) (HelpdeskDate::DueDateOffset("%d")+$priority->timevalue), (int) HelpdeskDate::DueDateOffset("%Y")) );
			//echo "<p>due date = $data_termo</p>";
		}
		else
		{
			$dia_semana = HelpdeskDate::DueDateOffset("%w");
			$data_hora_actual = HelpdeskDate::DueDateOffset("%Y-%m-%d %H:%M:%S");
			$data_actual = HelpdeskDate::DueDateOffset("%Y-%m-%d");
			$hora_criacao = HelpdeskDate::DueDateOffset("%H:%M");
			$tempo = $priority->timevalue;
			$hora_criacao_segundos = explode(':', $hora_criacao);
			$hora_criacao_segundos = ( $hora_criacao_segundos[0] * 3600 ) + ( $hora_criacao_segundos[1] * 60 );
			$tempo_segundos = $tempo * 3600;
			$tempo_resposta = $tempo_segundos;	// para loop do tempo
			/*echo "<p>day of the week = $dia_semana</p>";
			echo "<p>current = $data_hora_actual</p>";
			echo "<p>current hours = $hora_criacao</p>";
			echo "<p>current seconds = $hora_criacao_segundos</p>";
			echo "<p>time of response = $tempo</p>";
			echo "<p>time of response in seconds = $tempo_segundos</p>";
			echo "<hr>";*/

			// Schedule
			// Sunday
			$day[0]['start'] 	           = $schedule[6]->work_start;
			$day[0]['end'] 		           = $schedule[6]->work_end;
			$day[0]['break_start']         = $schedule[6]->break_start;
			$day[0]['break_end']           = $schedule[6]->break_end;
			$day[0]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[0]['start']);
			$day[0]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[0]['end']);
			$day[0]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[0]['break_start']);
			$day[0]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[0]['break_end']);
			// Monday
			$day[1]['start'] 	           = $schedule[0]->work_start;
			$day[1]['end'] 		           = $schedule[0]->work_end;
			$day[1]['break_start']         = $schedule[0]->break_start;
			$day[1]['break_end']           = $schedule[0]->break_end;
			$day[1]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[1]['start']);
			$day[1]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[1]['end']);
			$day[1]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[1]['break_start']);
			$day[1]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[1]['break_end']);
			// Tuesday
			$day[2]['start'] 	           = $schedule[1]->work_start;
			$day[2]['end'] 		           = $schedule[1]->work_end;
			$day[2]['break_start']         = $schedule[1]->break_start;
			$day[2]['break_end']           = $schedule[1]->break_end;
			$day[2]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[2]['start']);
			$day[2]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[2]['end']);
			$day[2]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[2]['break_start']);
			$day[2]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[2]['break_end']);
			// Wednesday
			$day[3]['start'] 	           = $schedule[2]->work_start;
			$day[3]['end'] 		           = $schedule[2]->work_end;
			$day[3]['break_start']         = $schedule[2]->break_start;
			$day[3]['break_end']           = $schedule[2]->break_end;
			$day[3]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[3]['start']);
			$day[3]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[3]['end']);
			$day[3]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[3]['break_start']);
			$day[3]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[3]['break_end']);
			// Thursday
			$day[4]['start'] 	           = $schedule[3]->work_start;
			$day[4]['end'] 		           = $schedule[3]->work_end;
			$day[4]['break_start']         = $schedule[3]->break_start;
			$day[4]['break_end']           = $schedule[3]->break_end;
			$day[4]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[4]['start']);
			$day[4]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[4]['end']);
			$day[4]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[4]['break_start']);
			$day[4]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[4]['break_end']);
			// Friday
			$day[5]['start'] 	           = $schedule[4]->work_start;
			$day[5]['end'] 		           = $schedule[4]->work_end;
			$day[5]['break_start']         = $schedule[4]->break_start;
			$day[5]['break_end']           = $schedule[4]->break_end;
			$day[5]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[5]['start']);
			$day[5]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[5]['end']);
			$day[5]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[5]['break_start']);
			$day[5]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[5]['break_end']);
			// Saturday
			$day[6]['start'] 	           = $schedule[5]->work_start;
			$day[6]['end'] 		           = $schedule[5]->work_end;
			$day[6]['break_start']         = $schedule[5]->break_start;
			$day[6]['break_end']           = $schedule[5]->break_end;
			$day[6]['start_seconds'] 	   = HelpdeskDate::HoursToSeconds($day[6]['start']);
			$day[6]['end_seconds']   	   = HelpdeskDate::HoursToSeconds($day[6]['end']);
			$day[6]['break_start_seconds'] = HelpdeskDate::HoursToSeconds($day[6]['break_start']);
			$day[6]['break_end_seconds']   = HelpdeskDate::HoursToSeconds($day[6]['break_end']);

			/*echo "<h4>schedule</h4>";
			for ($i=0; $i<7; $i++)
			{
				echo "<p>day $i = {$day[$i]['start']} / {$day[$i]['break_start']} / {$day[$i]['break_end']} / {$day[$i]['end']} <br />";
				echo "day $i = {$day[$i]['start_seconds']} / {$day[$i]['break_start_seconds']} / {$day[$i]['break_end_seconds']} / {$day[$i]['end_seconds']}</p>";
			}
			echo "<hr>";*/

			// Calcula tempo disponivel no proprio dia
			$disponivel = 0;
			// Hora entre o horario do dia e sem intervalo definido
			if ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && !$day[$dia_semana]['break_start_seconds'] && !$day[$dia_semana]['break_end_seconds']) {
				$disponivel = ($day[$dia_semana]['end_seconds']-$hora_criacao_segundos);
				// Hora entre o horario do dia e ja passou do intervalo definido
			}elseif ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && $hora_criacao_segundos > $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos > $day[$dia_semana]['break_end_seconds']) {
				$disponivel = ($day[$dia_semana]['end_seconds']-$hora_criacao_segundos);
				// Hora entre o horario do dia antes do intervalo definido
			}elseif ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && $hora_criacao_segundos < $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos < $day[$dia_semana]['break_end_seconds']) {
				$disponivel = ($day[$dia_semana]['end_seconds']-$hora_criacao_segundos);
				// Hora antes do horario de inicio
			}elseif ($hora_criacao_segundos < $day[$dia_semana]['start_seconds']) {
				$disponivel = ($day[$dia_semana]['end_seconds']-$day[$dia_semana]['start_seconds']-($day[$dia_semana]['break_end_seconds']-$day[$dia_semana]['break_start_seconds']));
				// Hora depois do horario de fim
			}elseif ($hora_criacao_segundos > $day[$dia_semana]['end_seconds']) {
				$disponivel = 0;
				// Hora entre o intervalo
			}elseif ($hora_criacao_segundos >= $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos <= $day[$dia_semana]['break_end_seconds']) {
				$disponivel = ($day[$dia_semana]['end_seconds']-$day[$dia_semana]['break_end_seconds']);
			}

			// Verifica se e feriado no dia de hoje (faz OVERRIDE ao $disponivel em cima)
			if (HelpdeskDate::CheckIfHoliday($data_actual))
			{
				$disponivel = 0;
			}

			// Verifica se dia de hoje esta nas ferias da pessoa (faz OVERRIDE ao $disponivel em cima)
			// TODO...

			// Verifica o tempo minimo no proprio dia  (faz OVERRIDE ao $disponivel em cima)
			// TODO ...

			$disponivel_calc = gmdate("H:i", $disponivel);
			//echo "<p>available today = $disponivel / $disponivel_calc</p>";
			//echo "<hr>";

			// Verifica se passa para o proximo dia ou nao
			if( $disponivel >= $tempo_segundos ) {
				// Hora entre o horario do dia e sem intervalo definido
				if ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && !$day[$dia_semana]['break_start_seconds'] && !$day[$dia_semana]['break_end_seconds']) {
					$data_termo = date("Y-m-d H:i:s", mktime(HelpdeskDate::DueDateOffset("%H"), HelpdeskDate::DueDateOffset("%M"), $tempo_segundos, HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d"), HelpdeskDate::DueDateOffset("%Y")) );
					// Hora entre o horario do dia e ja passou do intervalo definido
				}elseif ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && $hora_criacao_segundos > $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos > $day[$dia_semana]['break_end_seconds']) {
					$data_termo = date("Y-m-d H:i:s", mktime(HelpdeskDate::DueDateOffset("%H"), HelpdeskDate::DueDateOffset("%M"), $tempo_segundos, HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d"), HelpdeskDate::DueDateOffset("%Y")) );
					// Hora entre o horario do dia antes do intervalo definido
				}elseif ($day[$dia_semana]['start_seconds'] < $hora_criacao_segundos && $day[$dia_semana]['end_seconds'] > $hora_criacao_segundos && $hora_criacao_segundos < $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos < $day[$dia_semana]['break_end_seconds']) {
					$tempo_segundos = $tempo_segundos + ($day[$dia_semana]['break_end_seconds']-$day[$dia_semana]['break_start_seconds']);
					$data_termo = date("Y-m-d H:i:s", mktime(HelpdeskDate::DueDateOffset("%H"), HelpdeskDate::DueDateOffset("%M"), $tempo_segundos, HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d"), HelpdeskDate::DueDateOffset("%Y")) );
					// Hora antes do horario de inicio
				}elseif ($hora_criacao_segundos < $day[$dia_semana]['start_seconds']) {
					$data_calculo = explode(":", gmdate("H:i", $day[$dia_semana]['start_seconds']));
					$data_termo = date("Y-m-d H:i:s", mktime($data_calculo[0], $data_calculo[1], $tempo_segundos, HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d"), HelpdeskDate::DueDateOffset("%Y")) );
					// Hora entre o intervalo
				}elseif ($hora_criacao_segundos >= $day[$dia_semana]['break_start_seconds'] && $hora_criacao_segundos <= $day[$dia_semana]['break_end_seconds']) {
					$tempo_segundos = $tempo_segundos + ($day[$dia_semana]['break_end_seconds']-$hora_criacao_segundos);
					$data_termo = date("Y-m-d H:i:s", mktime(HelpdeskDate::DueDateOffset("%H"), HelpdeskDate::DueDateOffset("%M"), $tempo_segundos, HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d"), HelpdeskDate::DueDateOffset("%Y")) );
				}
				//echo "<p>there is time in the current day</p>";
			}else{
				//echo "<p>there is no time in the current day</p>";
				$tempo_resposta = $tempo_resposta - $disponivel;	// retira o tempo do proprio dia ao total de tempo de resposta
				$dia_loop = ($dia_semana==6 ? 0 : ($dia_semana+1));
				$dias = 0;
				$data_termo = 0;
				while ($tempo_resposta > 0)
				{
					$tempo_disponivel_no_dia = ($day[$dia_loop]['end_seconds']-$day[$dia_loop]['start_seconds']) - ($day[$dia_loop]['break_end_seconds']-$day[$dia_loop]['break_start_seconds']);
					$data_actual = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+($dia_loop-1), date("Y")));
					if (HelpdeskDate::CheckIfHoliday($data_actual))
					{
						$tempo_disponivel_no_dia = 0;
					}
					if ($tempo_resposta > $tempo_disponivel_no_dia)
					{
						$tempo_resposta = $tempo_resposta - $tempo_disponivel_no_dia;
					}else
					{
						$data_termo = $day[$dia_loop]['start_seconds']+$tempo_resposta;
						if ($data_termo>=$day[$dia_loop]['break_start_seconds'] && $data_termo<=$day[$dia_loop]['break_end_seconds'])
						{
							$data_termo = $data_termo+($day[$dia_loop]['break_end_seconds']-$day[$dia_loop]['break_start_seconds']);
						}
						if (HelpdeskDate::CheckIfHoliday(date("Y-m-d", $data_termo)))
						{
							$tempo_resposta = $tempo_resposta;
						}
						else
						{
							$tempo_resposta = 0;
						}
					}
					//echo "<p><b>day $dias ($dia_loop) </b><br />&nbsp;&nbsp;&nbsp; duedate = $data_termo (".gmdate("H:i:s", $data_termo).") <br />&nbsp;&nbsp;&nbsp; available time in the day = $tempo_disponivel_no_dia (".gmdate("H:i:s", $tempo_disponivel_no_dia).") <br />&nbsp;&nbsp;&nbsp; response time missing = $tempo_resposta (".gmdate("H:i:s", $tempo_resposta).")";
					$dias++;
					$dia_loop = ($dia_loop==6 ? 0 : ($dia_loop+1));
				}
				$data_termo = HelpdeskDate::DueDateOffset("%Y-%m-%d %H:%M:%S", mktime(gmdate("H",$data_termo), gmdate("i",$data_termo), gmdate("s",$data_termo), HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d")+$dias, HelpdeskDate::DueDateOffset("%Y")) );
			}
		}
	}
	else
	{
		$data_termo = HelpdeskDate::DueDateOffset("%Y-%m-%d %H:%M:%S", mktime(HelpdeskDate::DueDateOffset("%H"), HelpdeskDate::DueDateOffset("%M"), HelpdeskDate::DueDateOffset("%S"), HelpdeskDate::DueDateOffset("%m"), HelpdeskDate::DueDateOffset("%d")+$priority->timevalue, HelpdeskDate::DueDateOffset("%Y")) );
		//echo "<p>due date = $data_termo</p>";
	}

	echo JString::substr($data_termo,0,16);
}

function number_pad($number, $n)
{
	return str_pad((int)$number, $n, "0", STR_PAD_LEFT);
}

function GetUserAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();

	$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $userl->id . "' AND w.id = p.id_workgroup");
	$is_support = $database->loadResult();

	// user may be in backend and not logged in frontend and $is_support will fail
	if (!$userl->id || !$is_support)
	{
		$session = JRequest::getVar('session', '', 'GET', 'string');
		$sql = "SELECT COUNT(*)
				FROM `#__session`
				WHERE `session_id`='" . $database->quote($session) . "'
				  AND `client_id`=1
				  AND `guest`=0";
		$database->setQuery($sql);
		$is_support = $database->loadResult();
	}

	if (!$is_support)
	{
		return false;
	}

	//$data = array();
	$data = '';

	$name = JRequest::getVar('q', '', '', 'string');
	if (!$name)
	{
		return;
	}

	// Maximum number of users that will be displayed in the results ajax box
	$results_item_limit = 20;

	// Search Users and Clients tables
	$sql = "SELECT u.id, u.name, c.clientname, c.id AS id_client, u.email
			FROM #__users u 
				 LEFT JOIN #__support_client_users cu ON cu.id_user=u.id 
				 LEFT JOIN #__support_client c ON c.id=cu.id_client 
			WHERE u.block=0
			   AND (UCASE(u.name) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(c.clientname) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.username) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.email) LIKE UCASE('%" . ($name) . "%'))
			ORDER BY u.name, c.clientname 
			LIMIT 0, " . $results_item_limit . "";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	for ($i = 0; $i < count($rows); $i++)
	{
		$row = $rows[$i];
		$data .= $row->id . "|" . $row->name . "|" . $row->id_client . "|" . $row->clientname . "|" . $row->email . "|" . HelpdeskUser::GetAvatar($row->id) . "\n";
	}

	echo $data;
}

function GetClientAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();

	$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $userl->id . "' AND w.id = p.id_workgroup");
	$is_support = $database->loadResult();

	// user may be in backend and not logged in frontend and $is_support will fail
	if (!$userl->id || !$is_support)
	{
		$session = JRequest::getVar('session', '', 'GET', 'string');
		$sql = "SELECT COUNT(*)
				FROM `#__session`
				WHERE `session_id`='" . $database->quote($session) . "'
				  AND `client_id`=1
				  AND `guest`=0";
		$database->setQuery($sql);
		$is_support = $database->loadResult();
	}

	if (!$is_support)
	{
		return false;
	}

	//$data = array();
	$data = '';

	$name = JRequest::getVar('q', '', '', 'string');
	if (!$name) return;

	// Maximum number of users that will be displayed in the results ajax box
	$results_item_limit = 20;

	// Search Users and Clients tables
	$sql = "SELECT c.id, c.clientname, c.logo
			FROM #__support_client c 
			WHERE UCASE(c.clientname) LIKE UCASE('%" . ($name) . "%')
			ORDER BY c.clientname 
			LIMIT 0, " . $results_item_limit . "";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	for ($i = 0; $i < count($rows); $i++)
	{
		$row = $rows[$i];
		$data .= $row->id . "|" . $row->clientname . "|" . ($row->logo != '' ? JURI::root() . 'media/com_maqmahelpdesk/images/logos/' . $row->logo : JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/48px/clients.png') . "\n";
	}

	echo $data;
}
