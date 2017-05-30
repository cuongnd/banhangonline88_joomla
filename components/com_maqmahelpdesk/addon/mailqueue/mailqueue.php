<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: mailqueue.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.mail.helper');

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

//get the total number of records
$database->setQuery("SELECT * FROM #__support_mail_queue");
$rows = $database->loadObjectList();
echo $database->getErrorMsg();

for ($i = 0; $i < count($rows); $i++) {
	unset($mailer);
	$mailer = JFactory::getMailer();
	$row = &$rows[$i];
	if ($row->cc != '') {
		$mailer->addCC(explode(',', $row->cc));
	}
	if ($row->bcc != '') {
		$mailer->addBCC(explode(',', $row->bcc));
	}
	$mailer->setSender($row->wkmail, $row->wkmail_name);
	$mailer->addRecipient($row->usermail);
	$mailer->setSubject($row->subject);
	$mailer->setBody($row->body);
	$mailer->IsHTML(true);
	$sendmail = $mailer->Send();
	echo "<p>sendmail: ";
	print_r($sendmail);

	$database->setQuery("DELETE FROM #__support_mail_queue WHERE id='" . $row->id . "'");
	$database->query();

	$j = $i + 1;
	print '<p>Email sended (' . $j . ')';
}

if (!get_cfg_var('safe_mode')) {
	set_time_limit($time_limit);
}
