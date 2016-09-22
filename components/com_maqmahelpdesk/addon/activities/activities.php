<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();
$supportConfig = HelpdeskUtility::GetConfig();

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='activities'");
$published = $database->loadResult();

if ($published) {
	// Get records
	$sql = "SELECT l.`id_ticket`, l.`id_user`, l.`date_time`, l.`log`, l.`image`, t.`ticketmask`, t.`id_workgroup`, t.`an_name`, u.`name`, c.`clientname`, su.`avatar`
		  	FROM `#__support_log` as l
			  	 INNER JOIN `#__support_ticket` as t ON t.`id`=l.`id_ticket`
			  	 LEFT JOIN `#__support_users` AS su ON su.`id_user`=l.`id_user`
				 LEFT JOIN `#__users` AS u ON u.`id`=l.`id_user`
			   	 LEFT JOIN `#__support_client_users` AS cu ON cu.`id_user`=t.`id_user`
			  	 LEFT JOIN `#__support_client` AS c ON c.`id`=cu.`id_client`
		  	WHERE substring(l.date_time,1,10) = '" . date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))) . "'
		  	ORDER BY l.date_time ASC";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	echo $database->getErrorMsg();

	$feed_summary = '';

	if (count($rows) > 0) {
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];

			// Get the user avatar
			$avatar = HelpdeskUser::GetAvatar($row->id_user);

			// Build the row
			$feed_summary .= '<tr class="' . ($i % 2 ? 'even' : 'odd') . '">
								<td><img src="' . $avatar . '" width="32" height="32" /></td>
								<td>' . trim(JString::substr($row->date_time, 10)) . '</td>
								<td>' . ($row->id_user ? $row->name : $row->an_name) . ($row->clientname != '' ? '<br /><span><i>' . $row->clientname . '</i></span>' : '') . '</td>
								<td><a href="' . JRoute::_('index.php?option=com_maqmahelpdesk&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->id_ticket) . '">' . $row->ticketmask . '</a></td>
								<td><img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/logs/' . $row->image . '" style="padding:5px;" align="left" />' . $row->log . '</td>
							 </tr>';
		}

		// Get email template
		ob_start();
		$tmplfile = HelpdeskTemplate::GetFile('mail/activities_list');
		include_once($tmplfile);
		$email_body = ob_get_contents();
		ob_end_clean();

		// Send email
		$mailer = JFactory::getMailer();
		$mailer->setSender($CONFIG->mailfrom, JText::_('tickets_mail_from'));
		$mailer->addRecipient($CONFIG->mailfrom);
		$mailer->setSubject(JText::_('activities_subject'));
		$mailer->setBody($email_body);
		$mailer->IsHTML(true);
		print $mailer->Send();
	}
}
