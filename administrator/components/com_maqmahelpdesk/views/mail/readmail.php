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

function ticketLog2($ticketId, $log, $log_reserved, $id_user, $id_status = 0, $image = '')
{
	$database = JFactory::getDBO();
	$sql = "INSERT INTO #__support_log(id_ticket, id_user, date_time, log, `log_reserved`, `id_status`, `time_elapse`, `image` )
			VALUES('" . $ticketId . "', '" . $id_user . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', " . $database->quote($log) . ", " . $database->quote($log_reserved) . ", " . $id_status . ", 0, '" . $image . "')";
	$database->setQuery($sql);
	$database->query();
}

function ticketMessage($ticketId, $message, $id_user)
{
	if ($message != '') {
		$database = JFactory::getDBO();
		$sql = "INSERT INTO #__support_ticket_resp(id_ticket, id_user, `date`, `message`)
				VALUES('$ticketId', '$id_user', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', " . $database->quote($message) . ")";
		$database->setQuery($sql);
		$database->query();
	}
}

function Mail2Ticket($id, $id_workgroup, $id_category, $subject, $body, $name, $email, $queue = 0, $status = 0)
{
	$database = JFactory::getDBO();
	$mailer = JFactory::getMailer();
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication('administrator');

	$ticketmask = "";

	if ($id == 0)
	{
		$task = 1;
	}
	else
	{
		$task = 0;
	}
	$GLOBALS['content'] .= '<table cellpadding="5">';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">task</td><td>' . ($task ? 'new ticket' : 'ticket reply') . '</td></tr>';
	$GLOBALS['content'] .= '</table>';

	// Get the Itemid
	$sql = "SELECT id
			FROM #__menu
			WHERE link='index.php?option=com_maqmahelpdesk'";
	$database->setQuery($sql);
	$Itemid = $database->loadResult();

	// Get Workgroup Options
	$sql = "SELECT *
			FROM #__support_workgroup
			WHERE id=" . (int) $id_workgroup;
	$database->setQuery($sql);
	$wkoptions = null;
	$wkoptions = $database->loadObject();
	$GLOBALS['content'] .= '<table cellpadding="5">';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Support User Auto-Assign</td><td>' . $wkoptions->auto_assign . '</td></tr>';
	$GLOBALS['content'] .= '</table>';

	// Get configuration options
	$supportConfig = HelpdeskUtility::GetConfig();

	// Get assigned user
	$sql = "SELECT name, email
			FROM #__users
			WHERE id=" . (int) $wkoptions->auto_assign;
	$database->setQuery($sql);
	$assigned = null;
	$assigned = $database->loadObject();

	// Get user
	$sql = "SELECT id, name, email
			FROM #__users
			WHERE email=" . $database->quote($email);
	$database->setQuery($sql);
	$loguser = null;
	$loguser = $database->loadObject();

	if (!isset($loguser->id) && $supportConfig->readmail_create_user && !$queue)
	{
		$GLOBALS['content'] .= '<table cellpadding="5">';
		$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Create new user</td><td>' . $email . '</td></tr>';
		$GLOBALS['content'] .= '</table>';
		$database->setQuery("SELECT id, name, email FROM #__users WHERE id=" . createUser($email));
		$loguser = null;
		$loguser = $database->loadObject();
	}

	// User doesn't exist and shouldn't be created set values
	if (!isset($loguser))
	{
		$loguser->id = 0;
		$loguser->name = $name;
		$loguser->email = $email;
	}

	$GLOBALS['content'] .= '<table cellpadding="5">';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">User ID</td><td>' . $loguser->id . '</td></tr>';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">User Name</td><td>' . $loguser->name . '</td></tr>';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">User E-Mail</td><td>' . $loguser->email . '</td></tr>';
	$GLOBALS['content'] .= '</table>';

	// Get the client id
	$sql = "SELECT `id_client`
			FROM `#__support_client_users`
			WHERE `id_user`=" . (int) $loguser->id;
	$database->setQuery($sql);
	$id_client = $database->loadResult();

	// Get Default Status
	if (!$status)
	{
		$database->setQuery("SELECT id FROM #__support_status WHERE isdefault='1'");
		$status = $database->loadResult();
	}

	// Get Default Priority
	$database->setQuery("SELECT id FROM #__support_priority WHERE isdefault='1'");
	$priority = $database->loadResult();
	$duedate = HelpdeskTicket::ReturnDueDate(date("Y"), date("m"), date("d"), date("H"), date("i"), $priority);

	// Check if user is from support or customer
	$sql = "SELECT COUNT(*)
			FROM #__support_permission
			WHERE id_workgroup=" . (int) $id_workgroup . " AND id_user=" . (int) $loguser->id;
	$database->setQuery($sql);
	$is_support = $database->loadResult();
	$GLOBALS['content'] .= '<table cellpadding="5">';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Is support</td><td>' . $is_support . '</td></tr>';
	$GLOBALS['content'] .= '</table>';

	if ((!$queue && isset($loguser) && $loguser->id) || $queue)
	{
		// Creates or Updates the ticket
		if ($id == 0)
		{
			$sql = "INSERT INTO #__support_ticket(id, id_workgroup, id_category, id_status, id_user, id_client, date, subject, message, last_update, assign_to, id_priority, source, ticketmask, an_name, an_mail, duedate, queue, day_week, ipaddress)
					VALUES(0, '" . $id_workgroup . "', '" . $id_category . "', '" . $status . "', '" . $loguser->id . "', '" . $id_client . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', " . $database->quote($subject) . ", " . $database->quote($body) . ", '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', '" . $wkoptions->auto_assign . "', '" . $priority . "', 'M', '', " . $database->quote($name) . ", " . $database->quote($email) . ", '" . $duedate . "', " . $queue . ", '" . HelpdeskDate::DateOffset("%w") . "', '')";
			$database->setQuery($sql);
			$database->query();
			$id = $database->insertid();

			// Set ticketmask
			if ($supportConfig->tickets_numbers)
			{
				$ticketmask = rand(10, 99) . $id . rand(1000, 9999);
			}
			else
			{
				$ticketmask = $id;
			}
			$GLOBALS['content'] .= '<table cellpadding="5">';
			$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Ticket Mask</td><td>' . $ticketmask . '</td></tr>';
			$GLOBALS['content'] .= '</table>';
			$sql = "UPDATE #__support_ticket
					SET ticketmask='" . $ticketmask . "'
					WHERE id=" . (int) $id;
			$database->setQuery($sql);
			$database->query();

			// Get the ticket record
			$row_ticket = null;
			$sql = "SELECT *
					FROM #__support_ticket
					WHERE id=" . (int) $id;
			$database->setQuery($sql);
			$row_ticket = $database->loadObject();
		}
		else
		{
			// Get the ticket record
			$row_ticket = null;
			$sql = "SELECT *
					FROM #__support_ticket
					WHERE id=" . (int) $id;
			$database->setQuery($sql);
			$row_ticket = $database->loadObject();

			// Get assigned user of the current ticket
			if ($row_ticket->assign_to)
			{
				//$is_support = 1;
				$sql = "SELECT name, email
						FROM #__users
						WHERE id=" . (int) $row_ticket->assign_to;
				$database->setQuery($sql);
				$assigned = null;
				$assigned = $database->loadObject();
			}

			// Check if auto-change status should be used
			$sql = "SELECT `id`
					FROM `#__support_status`
					WHERE `auto_status_" . ($is_support ? 'agents' : 'users') . "`=1";
			$database->setQuery($sql);
			$status_id_new = (int) $database->loadResult();

			if ($status_id_new)
			{
				// Get current status of ticket
				$sql = "SELECT `id_status`
						FROM `#__support_ticket`
						WHERE `id`=" . $id;
				$database->setQuery($sql);
				$status_id_old = (int) $database->loadResult();

				// If different change it
				if ($status_id_old != $status_id_new)
				{
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Status auto change</td><td>' . $status_id_new . '</td></tr>';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Old status</td><td>' . $status_id_old . '</td></tr>';
					$GLOBALS['content'] .= '</table>';
					$status = $status_id_new;
					$ticketLogMsg = JText::_('changed_status');
					$ticketLogMsg = str_replace('%1', $loguser->name, $ticketLogMsg);
					$ticketLogMsg = str_replace('%2', HelpdeskStatus::GetName($status_id_old), $ticketLogMsg);
					$ticketLogMsg = str_replace('%3', HelpdeskStatus::GetName($status_id_new), $ticketLogMsg);
					HelpdeskTicket::Log($id, $ticketLogMsg, JText::_('changed_status_hidden'), $status_id_new, 'status', $status_id_new, 0, 'change.png');
				}

				$sql = "UPDATE #__support_ticket
						SET id_status=" . (int) $status . "
						WHERE id=" . $id;
					$database->setQuery($sql);
					$database->query();
			}

			$sql = "UPDATE #__support_ticket
					SET last_update='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "'
					WHERE id=" . $id;
			$database->setQuery($sql);
			$database->query();

			// Get the ticket record
			$row_ticket = null;
			$sql = "SELECT *
					FROM #__support_ticket
					WHERE id=" . (int) $id;
			$database->setQuery($sql);
			$row_ticket = $database->loadObject();

			ticketMessage($id, $body, $loguser->id);
			HelpdeskTicket::Log($id, str_replace('%1', $loguser->name, JText::_('posted_reply')), JText::_('posted_reply_customer'), $row_ticket->id_status, '', 0, 0, 'add_message.png');
		}

		$GLOBALS['content'] .= '<table cellpadding="5">';
		$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">ID</td><td>' . $id . '</td></tr>';
		$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Ticket Mask</td><td>' . $row_ticket->ticketmask . '</td></tr>';
		$GLOBALS['content'] .= '</table>';

		// Insert Custom Fields
		$sql = "SELECT id_field
				FROM #__support_wk_fields
				WHERE id_workgroup=" . (int) $id_workgroup . "
				ORDER BY ordering";
		$database->setQuery($sql);
		$customfields = $database->loadObjectList();

		if ($task == 1)
		{
			$x = 0;
			for ($x = 0; $x < count($customfields); $x++)
			{
				$rowField = $customfields[$x];
				$sql = "INSERT INTO #__support_field_value(id_field, id_ticket, newfield)
						VALUES('" . $rowField->id_field . "', '" . $id . "', '')";
				$database->setQuery($sql);
				$database->query();
			}
		}

		// Insert Logs of Creation of Ticket and Auto-Assign
		if ($task == 1)
		{
			ticketLog2($id, str_replace('%1', $name, JText::_('ticket_created')), JText::_('ticket_created_hidden'), $loguser->id, 0, 'add_ticket.png');

			if ($wkoptions->auto_assign > 0)
			{
				ticketLog2($id, str_replace('%1', $assigned->name, JText::_('ticket_assigned')), JText::_('assigned_hidden'), $loguser->id, 0, 'add_assign.png');
			}
		}

		$sendmail1 = "";
		$sendmail2 = "";

		// Ticket messages history
		$messages = '';
		if (!$task)
		{
			$bodymessages = HelpdeskTemplate::Parse(array(), ($is_support ? 'ticket_reply_mail_notify_support' : 'ticket_reply_mail_notify_customer'));
			$messages_start = stripos($bodymessages, '<!-- messages:start -->');
			$messages_end = stripos($bodymessages, '<!-- messages:end -->');

			if ($messages_start !== false && $messages_end !== false)
			{
				$messages_loop = JString::substr($bodymessages, $messages_start, ($messages_end - $messages_start));

				// Get Ticket Messages
				$ticketMsgs[0]->id_user = $row_ticket->id_user;
				$ticketMsgs[0]->user = $row_ticket->an_name;
				$ticketMsgs[0]->date = $row_ticket->date;
				$ticketMsgs[0]->message = $row_ticket->message;

				$sql = "(SELECT u.name as user, r.date, r.message, r.id_user
						FROM #__support_ticket_resp as r 
							 LEFT JOIN #__users as u ON r.id_user=u.id 
						WHERE r.id_ticket=" . (int) $row_ticket->id . ")
						
						UNION
						
						(SELECT u.name as user, n.date_time AS date, n.note, n.id_user
						FROM #__support_note AS n
							 INNER JOIN #__users AS u ON u.id=n.id_user
						WHERE n.show=1 AND n.id_ticket=" . (int) $row_ticket->id . ")
						
						ORDER BY `date` DESC";
				$database->setQuery($sql);
				$ticketMsgs = array_merge($database->loadObjectList(), $ticketMsgs);

				for ($i = 0; $i < count($ticketMsgs); $i++)
				{
					$messages .= str_replace('[messages:date]', $ticketMsgs[$i]->date, str_replace('[messages:author]', $ticketMsgs[$i]->user, str_replace('[messages:message]', $ticketMsgs[$i]->message, str_replace('[messages:avatar]', HelpdeskUser::GetAvatar($ticketMsgs[$i]->id_user), $messages_loop))));
				}
			}
		}

		// Set Email Notify Template variables
		$ticket_url = JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $wkoptions->id . '&task=ticket_view&id=' . $id;
		$GLOBALS['content'] .= '<table cellpadding="5">';
		$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Ticket URL</td><td>' . $ticket_url . '</td></tr>';
		$GLOBALS['content'] .= '</table>';

		$var_set = array('[number]' => $row_ticket->ticketmask,
			'[title]' => '',
			'[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
			'[dateshort]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
			'[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
			'[duedate]' => $row_ticket->duedate,
			'[duedate_old]' => '',
			'[assign]' => isset($assigned) ? $assigned->name : '',
			'[assign_email]' => isset($assigned) ? $assigned->email : '',
			'[unassigned]' => '',
			'[unassigned_email]' => '',
			'[subject]' => stripslashes($subject),
			'[message]' => stripslashes($body),
			'[summary]' => '',
			'[author]' => HelpdeskUser::GetName($loguser->id),
			'[recipient]' => ($is_support ? $row_ticket->an_name : (isset($assigned) ? $assigned->name : '')),
			'[email]' => $email,
			'[client]' => '',
			'[url]' => $ticket_url,
			'[department]' => $wkoptions->wkdesc,
			'[priority]' => HelpdeskPriority::GetName($priority),
			'[priority_old]' => '',
			'[status]' => HelpdeskStatus::GetName($status),
			'[status_old]' => '',
			'[category]' => HelpdeskCategory::GetName($row_ticket->id_category),
			'[category_old]' => '',
			'[source]' => JText::_('email'),
			'[anonymous]' => sprintf(JText::_('anonymous_ticket_fetch'), $row_ticket->ticketmask, $row_ticket->ticketmask, $email),
			'[intro]' => '',
			'[messages]' => $messages,
			'[helpdesk]' => JURI::root()
		);

		// Notify users - Assigned User and User that requested the support
		if ($task == 1)
		{
			if (!$queue)
			{
				if ($wkoptions->auto_assign > 0)
				{
					$var_set['[intro]'] = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_notify_support'));
					$msginfo->message = HelpdeskTemplate::Parse($var_set, 'ticket_create_mail_notify_support');
					if ($wkoptions->add_mail_tag == 2)
					{
						$msginfo->message = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $msginfo->message;
					}
					elseif ($wkoptions->add_mail_tag == 1)
					{
						$msginfo->message = JText::_("mail_tag") . $msginfo->message;
					}
					$msginfo->subject = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_subject'));
					$mailer->setSender(($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom), $wkoptions->wkmail_address_name);
					$mailer->addRecipient($assigned->email);
					$mailer->setSubject($msginfo->subject);
					$mailer->setBody($msginfo->message);
					$mailer->IsHTML(true);
					$sendmail = $mailer->Send();
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('mail_to_assigned_user') . '</td><td>' . $assigned->email . ' : ' . $sendmail . '</td></tr>';
					$GLOBALS['content'] .= '</table>';
				}

				// Notify Workgroup Administrator
				if ($wkoptions->wkemail && $wkoptions->wkadmin_email)
				{
					$var_set['[intro]'] = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_notify_support'));
					$msginfo->message = HelpdeskTemplate::Parse($var_set, 'ticket_create_mail_notify_support');
					if ($wkoptions->add_mail_tag == 2)
					{
						$msginfo->message = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $msginfo->message;
					}
					elseif ($wkoptions->add_mail_tag == 1)
					{
						$msginfo->message = JText::_("mail_tag") . $msginfo->message;
					}
					$msginfo->subject = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_subject'));
					$mailer->setSender(($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom), $wkoptions->wkmail_address_name);
					$mailer->addRecipient(($wkoptions->wkadmin_email != '' ? $wkoptions->wkadmin_email : ($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom)));
					$mailer->setSubject($msginfo->subject);
					$mailer->setBody($msginfo->message);
					$mailer->IsHTML(true);
					$sendmail = $mailer->Send();
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('notify_workgroup_admin') . '</td><td>' . ($wkoptions->wkadmin_email != '' ? $wkoptions->wkadmin_email : ($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom)) . ' : ' . $sendmail . '</td></tr>';
					$GLOBALS['content'] .= '</table>';
				}

				// Notify System Administrator
				if ($supportConfig->receive_mail)
				{
					$var_set['[intro]'] = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_notify_support'));
					$body = HelpdeskTemplate::Parse($var_set, 'ticket_create_mail_notify_support');
					if ($wkoptions->add_mail_tag == 2)
					{
						$body = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $body;
					}
					elseif ($wkoptions->add_mail_tag == 1)
					{
						$msginfo->message = JText::_("mail_tag") . $msginfo->message;
					}
					$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_subject'));
					$mailer->setSender(($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom), $wkoptions->wkmail_address_name);
					$mailer->addRecipient($CONFIG->mailfrom);
					$mailer->setSubject($subject);
					$mailer->setBody($body);
					$mailer->IsHTML(true);
					$sendmail = $mailer->Send();
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('notify_system_admin') . '</td><td>' . $CONFIG->mailfrom . ' : ' . $sendmail . '</td></tr>';
					$GLOBALS['content'] .= '</table>';
				}

				// Notify customer about ticket creation
				unset($mailer);
				$mailer = JFactory::getMailer();
				$var_set['[intro]'] = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_notify_confirmation'));
				$body = HelpdeskTemplate::Parse($var_set, 'ticket_create_mail_notify_confirmation');
				if ($wkoptions->add_mail_tag == 2)
				{
					$body = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $body;
				}
				elseif ($wkoptions->add_mail_tag == 1)
				{
					$body = JText::_("mail_tag") . $body;
				}
				$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('created_mail_subject'));
				$mailer->setSender(($wkoptions->wkmail_address != '' ? $wkoptions->wkmail_address : $CONFIG->mailfrom), $wkoptions->wkmail_address_name);
				$mailer->addRecipient($loguser->email);
				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->IsHTML(true);
				$sendmail = $mailer->Send();
				$GLOBALS['content'] .= '<table cellpadding="5">';
				$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('mail_to_user') . '</td><td>' . $loguser->email . ' : ' . $sendmail . '</td></tr>';
				$GLOBALS['content'] .= '</table>';
			}
		}
		else
		{
			$var_set['[intro]'] = HelpdeskTemplate::Parse($var_set, '', JText::_('reply_mail_notify'));

			// Notify assigned user if the message is from customer and notify customer that message was saved
			if (!$is_support)
			{
				// Get assigned user
				$sql = "SELECT u.name, u.email
						FROM  #__users AS u
						WHERE u.id=" . (int) $row_ticket->assign_to;
				$database->setQuery($sql);
				$assigned = null;
				$assigned = $database->loadObject();

				$msginfo->subject = HelpdeskTemplate::Parse($var_set, '', JText::_('reply_mail_subject'));
				$msginfo->message = HelpdeskTemplate::Parse($var_set, 'ticket_reply_mail_notify_support');

				// Ticket messages history
				$messages_start = stripos($msginfo->message, '<!-- messages:start -->');
				$messages_end = stripos($msginfo->message, '<!-- messages:end -->');
				if ($messages_start !== false && $messages_end !== false)
				{
					$msginfo->message = JString::substr($msginfo->message, 0, $messages_start) . '[messages]' . JString::substr($msginfo->message, ($messages_end + 21));
					$msginfo->message = str_replace('[messages]', $var_set['[messages]'], $msginfo->message);
				}
				if ($wkoptions->add_mail_tag == 2)
				{
					$msginfo->message = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $msginfo->message;
				}
				elseif ($wkoptions->add_mail_tag == 1)
				{
					$msginfo->message = JText::_("mail_tag") . $msginfo->message;
				}
				$mailer->setSender($wkoptions->wkmail_address, $wkoptions->wkmail_address_name);
				$mailer->addRecipient($assigned->email);
				$mailer->setSubject($msginfo->subject);
				$mailer->setBody($msginfo->message);
				$mailer->IsHTML(true);
				$sendmail = $mailer->Send();
				$GLOBALS['content'] .= '<table cellpadding="5">';
				$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" colspan="2">(message to notify support agent)</td></tr>';
				$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('mail_to_user') . '</td><td>' . $assigned->email . ' : ' . $sendmail . '</td></tr>';
				$GLOBALS['content'] .= '</table>';

			}
			else
			// Notify customer if the message is from support user
			{
				$msginfo->subject = HelpdeskTemplate::Parse($var_set, '', JText::_('reply_mail_subject'));
				$msginfo->message = HelpdeskTemplate::Parse($var_set, 'ticket_reply_mail_notify_customer');
				// Ticket messages history
				$messages_start = stripos($msginfo->message, '<!-- messages:start -->');
				$messages_end = stripos($msginfo->message, '<!-- messages:end -->');
				if ($messages_start !== false && $messages_end !== false)
				{
					$msginfo->message = JString::substr($msginfo->message, 0, $messages_start) . '[messages]' . JString::substr($msginfo->message, ($messages_end + 21));
					$msginfo->message = str_replace('[messages]', $var_set['[messages]'], $msginfo->message);
				}
				if ($wkoptions->add_mail_tag == 2)
				{
					$msginfo->message = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $msginfo->message;
				}
				elseif ($wkoptions->add_mail_tag == 1)
				{
					$msginfo->message = JText::_("mail_tag") . $msginfo->message;
				}
				$mailer->setSender($wkoptions->wkmail_address, $wkoptions->wkmail_address_name);
				$mailer->addRecipient($row_ticket->an_mail);
				$mailer->setSubject($msginfo->subject);
				$mailer->setBody($msginfo->message);
				$mailer->IsHTML(true);
				$sendmail = $mailer->Send();
				$GLOBALS['content'] .= '<table cellpadding="5">';
				$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" colspan="2">(message to notify customer)</td></tr>';
				$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">' . JText::_('mail_to_user') . '</td><td>' . $row_ticket->an_mail . ' : ' . $sendmail . '</td></tr>';
				$GLOBALS['content'] .= '</table>';
			}
		}
	}
	else
	{
		$GLOBALS['content'] .= '<p style="color:#f00000;">' . JText::_('user_email_dont_exist') . '</p>';
	}

	return $id;
}

function mailLog($id, $mail, $log, $emailid)
{
	$database = JFactory::getDBO();

	$sql = "INSERT INTO #__support_mail_log(`id_mail_fetch`, `date`, `email`, `log`, `emailid`)
			VALUES('$id', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', '$mail', '$log', '$emailid')";
	$database->setQuery($sql);
	$database->query();
}

function createUser($email)
{
	$database = JFactory::getDBO();

	$CONFIG = new JConfig();
	$mailer = JFactory::getMailer();

	list($username, $domain) = explode("@", $email);
	$username1 = $username . rand(100, 999) . rand(100, 999);
	$salt = md5(rand(100, 999));
	$password = rand(1000, 99999);
	$pass = md5($password . $salt) . ':' . $salt;

	$sql = sprintf("INSERT INTO `#__users`(`name`, `username`, `email`, `password`, `registerDate`)
					VALUES('%s', '%s', '%s', '%s', '" . date("Y-m-d H:i:s") . "')",
		$username,
		$username1,
		$email,
		$pass);
	$database->setQuery($sql);
	$database->query();
	$id_user = $database->insertid();

	$sql = "INSERT INTO `#__user_usergroup_map`(`user_id`, `group_id`)
			VALUES('" . $id_user . "', '2')";
	$database->setQuery($sql);
	$database->query();

	$body_html = "<p>Welcome " . $username . ",</p>
	<p>Your account has been activated with the following details:</p>
	<p>Email : " . $email . " <br />Username : " . $username1 . " <br />Password : " . $password . " </p>
	<p>Kind Regards, <br />
	" . $CONFIG->sitename;

	$subject = 'New User Details';

	// check if Global Config `mailfrom` and `fromname` values exist
	if ($CONFIG->mailfrom != '' && $CONFIG->fromname != '')
	{
		$adminName2 = $CONFIG->fromname;
		$adminEmail2 = $CONFIG->mailfrom;
	}
	else
	{
		// use email address and name of first superadmin for use in email sent to user
		$query = "SELECT name, email"
			. "\n FROM #__users"
			. "\n WHERE LOWER( usertype ) = 'superadministrator'"
			. "\n OR LOWER( usertype ) = 'super administrator'";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$row2 = $rows[0];

		$adminName2 = $row2->name;
		$adminEmail2 = $row2->email;
	}

	// Send email to user
	$mailer->addRecipient($email);
	$mailer->setSender($adminEmail2, $adminName2);
	$mailer->setSubject($subject);
	$mailer->setBody($body_html);
	$mailer->IsHTML(true);
	$sendmail = $mailer->Send();

	$GLOBALS['content'] .= '<table cellpadding="5">';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" colspan="2">(user account created)</td></tr>';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Email</td><td>' . $email . '</td></tr>';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Username</td><td>' . $username1 . '</td></tr>';
	$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Mail user creation</td><td>' . $sendmail . '</td></tr>';
	$GLOBALS['content'] .= '</table>';

	return $id_user;
}
