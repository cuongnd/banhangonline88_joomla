<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: tasks.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();
$supportConfig = HelpdeskUtility::GetConfig();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
	return false;
}

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='tasks'");
$published = $database->loadResult();

// Get the template from the file
$tmplfile = HelpdeskTemplate::GetFile('mail/tasks_list');
$fp = fopen($tmplfile, 'rb');
$tmpl_code = fread($fp, filesize($tmplfile));
fclose($fp);

if ($published) {
	// get tomorrow date
	$tomorrow = date("Y-m-d", mktime(0, 0, 0, HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d") + 1, HelpdeskDate::DateOffset("%Y")));

	//get the total number of records
	$database->setQuery("SELECT t.id_user, t.id_ticket, t.date_time, t.task, t.status, u.name, u.email FROM #__support_task t, #__users u WHERE u.id=t.id_user AND (t.date_time LIKE '" . $tomorrow . "%' OR (t.date_time<'$tomorrow' AND t.status='O')) ORDER BY t.id_user, t.date_time");
	$rows = $database->loadObjectList();
	echo $database->getErrorMsg();

	$prev_user = 0;
	$prev_name = '';
	$prev_mail = '';
	$tasks = 0;
	$feed_summary = '';

	if (count($rows) > 0) {
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			if ($prev_user == '') {
				$prev_user = $row->id_user;
				$prev_name = $row->name;
				$prev_mail = $row->email;
			}
			$tasks = $tasks + 1;

			$database->setQuery("SELECT ticketmask FROM #__support_ticket WHERE id='" . $row->id_ticket . "'");
			$ticketmask = $database->loadResult();
			echo $database->getErrorMsg();

			$feed_summary .= '
				<tr>
				<td bgcolor="#FFFFFF" nowrap valign="top" align="left" class="bodytext">' . JString::substr($row->date_time, 0, 10) . '</td>
				<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . JString::substr($row->date_time, 11, 5) . '</td>
				<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $ticketmask . '</td>
				<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->task . '</td>
				<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . ($row->status == 'O' ? JText::_('open') : JText::_('closed')) . '</td>
				</tr>		
				';

			if ($prev_user != $row->id_user || (count($rows) - $i) == 1) {
				$admin_email = $prev_mail;
				$email_subject = JText::_('tasks_addon_title');
				$email_body = str_replace('<tag:feed_summary />', $feed_summary, $tmpl_code);
				$email_body = str_replace('<tag:tasks />', $tasks, $email_body);

				print "<p>mail: " . JUtility::sendMail($CONFIG->mailfrom, JText::_('tasks_mail_from') . " <" . $CONFIG->mailfrom . ">", $admin_email, $email_subject, $email_body, 1);

				// Re-start counter and change previous user info
				$prev_user = $row->id_user;
				$prev_name = $row->name;
				$prev_mail = $row->email;
				$tasks = 0;
				$feed_summary = '';
			}
		}
	}
}
