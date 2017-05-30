<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: tickets.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
	return false;
}

// Get config
$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='tickets'");
$published = $database->loadResult();

if ($published)
{
	//get the total number of records
	$database->setQuery("SELECT t.id as dbid, t.ticketmask as ticketid, t.subject, s.description as status, t.duedate, t.date as date_created, t.last_update, w.wkdesc as workgroup, u.email, u.name, t.id_user, t.an_name, t.assign_to
						  FROM #__support_ticket as t 
						       INNER JOIN #__support_status as s ON t.id_status=s.id
							   INNER JOIN #__users as u ON u.id=t.assign_to
							   INNER JOIN #__support_workgroup as w ON w.id=t.id_workgroup
						  WHERE s.status_group = 'O'
						  ORDER BY t.assign_to, t.duedate ASC");
	$rows = $database->loadObjectList();

	$prev_user = 0;
	$prev_name = '';
	$prev_mail = '';
	$feed_summary = '';
	$tickets = 0;

	if (count($rows) > 0)
	{
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			if ($prev_user == '')
			{
				$prev_user = $row->assign_to;
				$prev_name = $row->name;
				$prev_mail = $row->email;
				$tickets = 0;
			}
			$tickets++;

			$feed_summary .= '
				<tr>
					<td bgcolor="#fff" valign="top" align="left" class="bodytext">' . $row->date_created . '</td>
					<td bgcolor="#fff" valign="top" align="left" class="bodytext">' . $row->duedate . '</td>
					<td bgcolor="#fff" valign="top" align="left" class="bodytext">' . $row->ticketid . '</td>
					<td bgcolor="#fff" valign="top" align="left" class="bodytext">' . $row->subject . '</td>
					<td bgcolor="#fff" valign="top" align="left" class="bodytext">' . $row->workgroup . '</td>
				</tr>		
				';

			if ($prev_user != $row->assign_to || (count($rows) - $i) == 1)
			{
				$admin_email = $prev_mail;
				$email_subject = JText::_('tickets_addon_title');

				ob_start();
				$tmplfile = HelpdeskTemplate::GetFile('mail/tickets_list');
				include_once($tmplfile);
				$email_body = ob_get_contents();
				ob_end_clean();

				unset($mailer);
				$mailer = JFactory::getMailer();
				$mailer->setSender($CONFIG->mailfrom, JText::_('tickets_mail_from'));
				$mailer->addRecipient($admin_email);
				$mailer->setSubject($email_subject);
				$mailer->setBody($email_body);
				$mailer->IsHTML(true);
                print "<p>tickets: $tickets</p>";
				print "<p>mail (" . $admin_email . "): " . $mailer->Send();

				// Re-start counter and change previous user info
				$prev_user = $row->assign_to;
				$prev_name = $row->name;
				$prev_mail = $row->email;
				$feed_summary = '';
			}
		}
	}
}

