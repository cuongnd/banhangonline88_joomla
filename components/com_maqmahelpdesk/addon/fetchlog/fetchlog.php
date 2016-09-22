<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: fetchlog.php 646 2012-05-22 08:20:58Z pdaniel $
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

if (!get_cfg_var('safe_mode')) {
	$time_limit = ini_get('max_execution_time');
	set_time_limit(0);
}

// Get config
$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='fetchlog'");
$published = $database->loadResult();

if ($published)
{
	// get tomorrow date
	$yesterday = date("Y-m-d", mktime(0, 0, 0, HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d") - 1, HelpdeskDate::DateOffset("%Y")));

	//get the total number of records
	$database->setQuery("SELECT f.email AS emailaccount, l.date, l.email, l.log FROM #__support_mail_log l INNER JOIN #__support_mail_fetch f ON f.id=l.id_mail_fetch WHERE SUBSTRING(l.date,1,10) = '" . $yesterday . "' ORDER BY l.date");
	$rows = $database->loadObjectList();

	$feed_summary = '';
	$entries = 0;

	if (count($rows) > 0) {
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			$entries++;

			$feed_summary .= '
				<tr>
					<td bgcolor="#FFFFFF" nowrap valign="top" align="left" class="bodytext">' . $row->date . '</td>
					<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->emailaccount . '</td>
					<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->email . '</td>
					<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->log . '</td>
				</tr>		
				';
		}
	} else {
		$feed_summary .= '
				<tr>
					<td bgcolor="#FFFFFF" colspan="2" class="bodytext">  -  </td>
				</tr>		
				';
	}

	$email_subject = JText::_('fetch_addon_title');
	ob_start();
	$tmplfile = HelpdeskTemplate::GetFile('mail/mail_fetching_list');
	include_once($tmplfile);
	$email_body = ob_get_contents();
	ob_end_clean();
	$email_body = str_replace('<tag:feed_summary/>', $feed_summary, $email_body);
	$email_body = str_replace('<tag:entries/>', $entries, $email_body);
	$email_body = str_replace('%1', $yesterday, $email_body);
	echo $email_body."<hr>";
	//if (JUtility::sendMail($CONFIG->mailfrom, JText::_('tasks_mail_from') . " <" . $CONFIG->mailfrom . ">", $CONFIG->mailfrom, $email_subject, $email_body, 1))
	//	print "<p>Fetch mail complete.";
}

if (!get_cfg_var('safe_mode')) {
	set_time_limit($time_limit);
}


