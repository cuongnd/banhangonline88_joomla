<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: escalation.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
	return false;
}

// Get Configuration Options
$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='escalation'");
$published = $database->loadResult();

if ($published) {
	// Array to get the ID of the treated Tickets to prevent double work
	$tickets_updated[] = array(0, 0);

	// Gets the support staff
	$support_staff = '';
	$database->setQuery("SELECT DISTINCT(id_user) FROM #__support_permission ORDER BY id_user");
	$support_members = $database->loadObjectList();

	// If there are will check if it can fill or not
	for ($i = 0; $i < count($support_members); $i++) {
		$staff = $support_members[$i];
		$support_staff .= $staff->id_user . ',';
	}
	$support_staff = JString::substr($support_staff, 0, strlen($support_staff) - 1);
	print "<p><b>support_staff:</b> $support_staff";

	// Checks if there's any ticket without DATE_SUPPORT filled
	$database->setQuery("SELECT * FROM #__support_ticket WHERE date_support IS NULL OR date_support = '0000-00-00 00:00:00'");
	$tickets = $database->loadObjectList();
	print "<p><b>" . JText::_('tickets') . ":</b> " . count($tickets);

	// If there are will check if it can fill or not
	for ($i = 0; $i < count($tickets); $i++) {
		$ticket = $tickets[$i];
		$database->setQuery("SELECT MIN(`date`) FROM #__support_ticket_resp WHERE id_ticket='" . $ticket->id . "' AND id_user IN (" . $support_staff . ")");
		$min_date = $database->loadResult();

		if ($min_date != '') {
			$database->setQuery("UPDATE #__support_ticket SET date_support='" . $min_date . "' WHERE id='" . $ticket->id . "'");
			$database->query();
		}
	}

	// Gets the existing rules
	$database->setQuery("SELECT id, id_workgroup, id_client, id_user, id_assign, id_priority, id_status, id_category, days_reply, days_open, id_status_trigger, id_assign_trigger FROM #__support_escalation_config ORDER BY ordering");
	$actions = $database->loadObjectList();
	print "<p><b>" . JText::_('actions') . ":</b> " . count($actions);

	for ($i = 0; $i < count($actions); $i++) {
		$action = $actions[$i];

		// Filters
		$filter = '';
		if ($action->id_user > 0) {
			$filter .= " AND t.id_user='" . $action->id_user . "'";
		}
		if ($action->id_client > 0) {
			$filter .= " AND t.id_user IN (SELECT c.id_user FROM #__support_client_users c WHERE c.id_client='" . $action->id_client . "')";
		}

		// Get tickets for DAYS WITHOUT SUPPORT REPLY
		if ($action->days_reply > 0) {
			$database->setQuery("SELECT t.* FROM #__support_ticket t INNER JOIN #__support_status s ON t.id_status=s.id WHERE s.status_group='O' AND t.id NOT IN (SELECT r.id_ticket FROM #__support_ticket_resp r WHERE r.id_user NOT IN(" . $support_staff . ")) AND ( to_days( now() ) - to_days( t.`date` ) ) > " . $action->days_reply . " AND t.id_workgroup='" . $action->id_workgroup . "'" . $filter);
			$tickets = $database->loadObjectList();
			print "<p><b>" . JText::_('tickets') . ":</b> " . count($tickets);

			for ($x = 0; $x < count($tickets); $x++) {
				$ticket = $tickets[$x];

				// Array to get the ID of the treated Tickets to prevent double work
				$tickets_updated[] = array($ticket->id, $ticket->id);
				arsort($tickets_updated);

				if (!array_key_exists($ticket->id, $tickets_updated)) {
					makeActions($supportConfig, $action, $ticket);
				}
			}
		}

		// Get tickets for DAYS TICKET IS OPENED
		if ($action->days_open > 0) {
			$database->setQuery("SELECT t.* FROM #__support_ticket t INNER JOIN #__support_status s ON t.id_status=s.id WHERE s.status_group='O' AND ( to_days( now() ) - to_days( t.`date` ) ) > " . $action->days_open . " AND t.id_workgroup='" . $action->id_workgroup . "'" . $filter);
			$tickets = $database->loadObjectList();
			print "<p><b>" . JText::_('tickets') . ":</b> " . count($tickets);

			for ($x = 0; $x < count($tickets); $x++) {
				$ticket = $tickets[$x];

				// Array to get the ID of the treated Tickets to prevent double work
				$tickets_updated[] = array($ticket->id, $ticket->id);
				arsort($tickets_updated);

				if (!array_key_exists($ticket->id, $tickets_updated)) {
					makeActions($supportConfig, $action, $ticket);
				}
			}
		}

		// JP 10.02.2009 status and assign trigger
		if ($action->id_status_trigger > 0) {
			$database->setQuery("SELECT t.* FROM #__support_ticket t INNER JOIN #__support_status s ON t.id_status=s.id WHERE s.status_group='O' AND t.id_status='" . $action->id_status_trigger . "' AND t.id_workgroup='" . $action->id_workgroup . "'" . $filter);
			$tickets = $database->loadObjectList();
			print "<p><b>" . JText::_('tickets') . ":</b> " . count($tickets);

			for ($x = 0; $x < count($tickets); $x++) {
				$ticket = $tickets[$x];

				// Array to get the ID of the treated Tickets to prevent double work
				$tickets_updated[] = array($ticket->id, $ticket->id);
				arsort($tickets_updated);

				if (!array_key_exists($ticket->id, $tickets_updated)) {
					makeActions($supportConfig, $action, $ticket);
				}
			}
		}
		if ($action->id_assign_trigger > 0) {
			$database->setQuery("SELECT t.* FROM #__support_ticket t INNER JOIN #__support_status s ON t.id_status=s.id WHERE s.status_group='O' AND t.assign_to='" . $action->id_assign_trigger . "' AND t.id_workgroup='" . $action->id_workgroup . "'" . $filter);
			$tickets = $database->loadObjectList();
			print "<p><b>" . JText::_('tickets') . ":</b> " . count($tickets);

			for ($x = 0; $x < count($tickets); $x++) {
				$ticket = $tickets[$x];

				// Array to get the ID of the treated Tickets to prevent double work
				$tickets_updated[] = array($ticket->id, $ticket->id);
				arsort($tickets_updated);

				if (!array_key_exists($ticket->id, $tickets_updated)) {
					makeActions($supportConfig, $action, $ticket);
				}
			}
		}


	}
}

function makeActions($supportConfig, $action, $ticket)
{
	$database = JFactory::getDBO();
	$mailer = JFactory::getMailer();

	if (JString::substr($ticket->last_update, 0, 10) != date("Y-m-d")) {
		?>
	<hr/>
	<pre><?php print_r($ticket); ?></pre><br/>
	<pre><?php print_r($action); ?></pre><?php
		// Initialise variables
		$wkoptions = null;
		$userinfo = null;
		$managersInfo = null;
		$assigned = null;
		$Itemid = 0;

		// Get Workgroup Options
		$database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $ticket->id_workgroup . "'");
		// $database->loadObject( $wkoptions );
		$wkoptions = $database->loadObject();

		// Get Ticket Users Details
		$database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->id_user . "'");
		$userinfo = $database->loadObject();

		// Get Ticket Users Client ID
		$client_id = HelpdeskClient::GetIDByUser($ticket->id_user);

		// Get Ticket Users Client Name
		$client_name = HelpdeskClient::GetName($ticket->id_user);

		// Get Ticket Client Manager
		if ($client_id > 0) {
			$database->setQuery("SELECT u.name, u.email FROM #__support_client_user c, #__users u WHERE c.id_user=u.id AND c.id_client='" . $client_id . "' AND c.manager='1'");
			$managersInfo = $database->loadObjectList();
		}

		// Get Ticket Source
		$source = HelpdeskTicket::SwitchSource($ticket->source);

		// Get Ticket Priority
		$priority_id_old = $ticket->id_priority;
		$priority_id_new = $action->id_priority;

		// Get Ticket Status
		$status_id_old = $ticket->id_status;
		$status_id_new = $action->id_status;

		// Get Ticket Categories
		$category_id_old = $ticket->id_category;
		$category_id_new = $action->id_category;

		// Get Assigned User
		$assigned_id_old = $ticket->assign_to;
		$assigned_name_old = HelpdeskUser::GetName($assigned_id_old);
		$assigned_id_new = $action->id_assign;

		// Get new and old assigned users info
		$database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_new'");
		$assigned_new = $database->loadObject();
		$database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_old'");
		$assigned_old = $database->loadObject();

		// Set Ticket URL Link
		$ticket_url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $wkoptions->id . '&task=ticket_view&id=' . $ticket->id);

		// Set Email Notify Template variables
		$var_set = array('%ticket%' => $ticket->ticketmask,
			'%cur_dateshort%' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
			'%cur_datelong%' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
			'%assigned%' => HelpdeskUtility::String2HTML((isset($assigned_new) ? $assigned_new->name : '')),
			'%assigned_email%' => (isset($assigned_new) ? $assigned_new->email : ''),
			'%unassigned%' => HelpdeskUtility::String2HTML((isset($assigned_old) ? $assigned_old->name : '')),
			'%unassigned_email%' => (isset($assigned_old) ? $assigned_old->email : ''),
			'%tkt_subj%' => HelpdeskUtility::String2HTML(($ticket->subject)),
			'%tkt_msg%' => (nl2br(HelpdeskUtility::String2HTML($ticket->message))),
			'%rpl_summary%' => '',
			'%rpl_msg%' => '',
			'%chng_author%' => '',
			'%tkt_user%' => HelpdeskUtility::String2HTML($userinfo->name),
			'%tkt_user_email%' => $userinfo->email,
			'%tkt_client%' => HelpdeskUtility::String2HTML($client_name),
			'%tkt_url%' => $ticket_url,
			'%workgroup%' => HelpdeskUtility::String2HTML($wkoptions->wkdesc),
			'%priority%' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_new)),
			'%priority_old%' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_old)),
			'%status%' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
			'%status_old%' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_old)),
			'%category%' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_new)),
			'%category_old%' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_old)),
			'%source%' => HelpdeskUtility::String2HTML($source)
		);


		////////////////////////
		// Ticket Assignments //
		////////////////////////
		if ($assigned_id_old != $assigned_id_new) {
			print "<p>" . JText::_('assignment') . ":";
			print "<br> - ticket: " . $ticket->id;
			print "<br> - assigned_id_old: $assigned_id_old";
			print "<br> - assigned_id_new: $assigned_id_new";
			// Update Ticket Assigned user
			$database->setQuery("UPDATE #__support_ticket SET assign_to='" . $assigned_id_new . "' WHERE id='" . $ticket->id . "'");
			if ($database->query()) {

				// Notify Customer User
				if ($wkoptions->tkt_asgn_nfy_usr_one && !$assigned_id_old) {
					// Work out if its the first assignment by checking if anyone else has replied to the ticket
					$sql = "SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_user<>'" . $userinfo->id . "' AND id_ticket ='" . $ticket->id . "'";
					$database->setQuery($sql);
					if ($database->loadResult() == 0) {
						$body = HelpdeskTemplate::Parse($var_set, 'escalation_notify_first');
						$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('tkt_assigned_notify_user_first_subj'));
						$mailer->addRecipient($userinfo->email);
						$mailer->setSubject($subject);
						$mailer->setBody($body);
						$mailer->IsHTML(true);
						$sendmail = $mailer->Send();
					}
				}

				// Notify Unassigned User
				if ($assigned_id_old > 0) {
					if ($wkoptions->tkt_asgn_old_asgn) {
						$body = HelpdeskTemplate::Parse($var_set, 'escalation_old_assignee');
						$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('tkt_assigned_old_assignee_subj'));
						$mailer->addRecipient($assigned_old->email);
						$mailer->setSubject($subject);
						$mailer->setBody($body);
						$mailer->IsHTML(true);
						$sendmail = $mailer->Send();
					}
					HelpdeskForm::Log($ticket->id, str_replace('%1', $assigned_old->name, JText::_('ticket_unassigned')), JText::_('ticket_unassigned_hidden'), $ticket->id_status); // Ticket log message
				}
				// New Assigned User
				if ($assigned_id_new > 0) {
					if ($wkoptions->tkt_asgn_new_asgn) {
						$body = HelpdeskTemplate::Parse($var_set, 'escalation_new_assignee');
						$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('tkt_assigned_new_assignee_subj'));
						$mailer->addRecipient($assigned_new->email);
						$mailer->setSubject($subject);
						$mailer->setBody($body);
						$mailer->IsHTML(true);
						$sendmail = $mailer->Send();
					}
					HelpdeskForm::Log($ticket->id, str_replace('%1', $assigned_new->name, JText::_('ticket_assigned')), JText::_('assigned_hidden'), $ticket->id_status); // Assigned Ticket log msg
				}
			} else {
				HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
			}
		}

		/////////////////////
		//  Ticket Status  //
		/////////////////////
		if ($status_id_old != $status_id_new) {
			print "<p>" . JText::_('tpl_status') . ":";
			print "<br> - ticket: " . $ticket->id;
			print "<br> - status_id_old: $status_id_old";
			print "<br> - status_id_new: $status_id_new";
			// Update Ticket Status
			$database->setQuery("UPDATE #__support_ticket SET id_status='" . $status_id_new . "' WHERE id='" . $ticket->id . "'");
			if ($database->query()) {
				// Ticket log
				$ticketLogMsg = JText::_('changed_status');
				$ticketLogMsg = str_replace('%1', 'System', $ticketLogMsg);
				$ticketLogMsg = str_replace('%2', HelpdeskStatus::GetName($status_id_old), $ticketLogMsg);
				$ticketLogMsg = str_replace('%3', HelpdeskStatus::GetName($status_id_new), $ticketLogMsg);
				HelpdeskForm::Log($ticket->id, $ticketLogMsg, JText::_('changed_status_hidden'), $ticket->id_status);
				// Notify User

				$body = HelpdeskTemplate::Parse($var_set, 'escalation_new_status');
				$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('tkt_chng_status_notify_subj'));
				$mailer->addRecipient($userinfo->email);
				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->IsHTML(true);
				$sendmail = $mailer->Send();

				// Notify Assigned User
				if ($assigned_id_new > 0) {
					$body = HelpdeskTemplate::Parse($var_set, 'escalation_new_status');
					$subject = HelpdeskTemplate::Parse($var_set, '', JText::_('tkt_chng_status_notify_subj'));
					$mailer->addRecipient($assigned_new->email);
					$mailer->setSubject($subject);
					$mailer->setBody($body);
					$mailer->IsHTML(true);
					$sendmail = $mailer->Send();
				}
			} else {
				HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
			}
		}

		//////////////////////
		//  Category Change //
		//////////////////////
		if ($category_id_old != $category_id_new) {
			print "<p>" . JText::_('category') . ":";
			print "<br> - ticket: " . $ticket->id;
			print "<br> - category_id_old: $category_id_old";
			print "<br> - category_id_new: $category_id_new";
			$database->setQuery("UPDATE #__support_ticket SET id_category='" . $category_id_new . "' WHERE id='" . $ticket->id . "'");
			if ($database->query()) {
				$ticketLogMsg = JText::_('changed_category');
				$ticketLogMsg = str_replace('%1', 'System', $ticketLogMsg);
				$ticketLogMsg = str_replace('%2', HelpdeskCategory::GetName($category_id_old), $ticketLogMsg);
				$ticketLogMsg = str_replace('%3', HelpdeskCategory::GetName($category_id_new), $ticketLogMsg);
				HelpdeskForm::Log($ticket->id, $ticketLogMsg, JText::_('changed_category_hidden'), $ticket->id_status);
			} else {
				HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
			}
		}

		//////////////////////
		//  Priority Change //
		//////////////////////
		if ($priority_id_old != $priority_id_new) {
			print "<p>" . JText::_('priority') . ":";
			print "<br> - ticket: " . $ticket->id;
			print "<br> - priority_id_old: $priority_id_old";
			print "<br> - priority_id_new: $priority_id_new";
			$database->setQuery("UPDATE #__support_ticket SET id_priority='" . $priority_id_new . "' WHERE id='" . $ticket->id . "'");
			if ($database->query()) {
				$ticketLogMsg = str_replace('%1', 'System', JText::_('changed_priority'));
				$ticketLogMsg = str_replace('%2', HelpdeskPriority::GetName($priority_id_old), $ticketLogMsg);
				$ticketLogMsg = str_replace('%3', HelpdeskPriority::GetName($priority_id_new), $ticketLogMsg);
				HelpdeskForm::Log($ticket->id, $ticketLogMsg, JText::_('changed_priority_hidden'), $ticket->id_status);
			} else {
				HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
			}
		}

		//////////////////////////////////////////
		//  Update Tickets Last Updated Date	//
		//////////////////////////////////////////
		$database->setQuery("UPDATE #__support_ticket SET last_update='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "' WHERE id='" . $ticket->id . "'");
		!$database->query() ? HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1)) : '';
	}
}

function checkFilter($workgroup, $client, $user)
{
	$database = JFactory::getDBO();

	$database->setQuery("SELECT COUNT(*) FROM #__support_config WHERE id_workgroup='" . $workgroup . "' AND id_client='" . $client . "' AND id_user='" . $user . "'");
	$result = $database->loadResult();

	return $result;
}
