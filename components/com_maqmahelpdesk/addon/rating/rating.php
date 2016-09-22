<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: rating.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();
$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='rating'");
$published = $database->loadResult();

if ($published)
{
	// Get records
	$sql = "SELECT t.`id`, t.`an_name`, t.`an_mail`, t.`id_workgroup`
		  	FROM `#__support_ticket` as t
		  		 INNER JOIN `#__support_status` AS s ON t.`id_status`=s.`id`
		  	WHERE s.`status_group`='C'
		  	  AND DAY(t.`date`)='" . date("d", mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))) . "'
		  	  AND MONTH(t.`date`)='" . date("m", mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))) . "'
		  	  AND YEAR(t.`date`)='" . date("Y", mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))) . "'
		  	  AND t.`id` NOT IN (SELECT r.`id_table`
		  	                     FROM `#__support_rate` AS r
		  	                     WHERE r.`source`='T')";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	if (count($rows) > 0)
	{
		// Get email template
		ob_start();
		$tmplfile = HelpdeskTemplate::GetFile('mail/rating_addon');
		include_once($tmplfile);
		$email_body_tmpl = ob_get_contents();
		ob_end_clean();

		for ($i = 0; $i < count($rows); $i++)
		{
			$row = &$rows[$i];
			$link = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->id);

			// Fill email body
			$email_body = str_replace('[message]', sprintf(JText::_('RATE_INVITE_MESSAGE'), $row->an_name, $link), $email_body_tmpl);

			// Send email
			unset($mailer);
			$mailer = JFactory::getMailer();
			$mailer->setSender($CONFIG->mailfrom, JText::_('tickets_mail_from'));
			$mailer->addRecipient($row->an_mail);
			$mailer->setSubject(JText::_('rating_subject'));
			$mailer->setBody($email_body);
			$mailer->IsHTML(true);
			print '<p>email to ' . $row->an_mail . ' - ' . $mailer->Send() . '</p>';
		}
	}
}
