<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: sms.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// $addon_action = 1 is a new ticket
// $addon_action = 0 is new activity
// $ticket_id is the DB ID of the ticket (#__support_ticket)
// $reply_id is the DB ID of the ticket reply (#__support_ticket_resp)

$database = JFactory::getDBO();
$user = JFactory::getUser();
$is_support = HelpdeskUser::IsSupport();
$is_client = HelpdeskUser::IsClient();

require_once(JPATH_SITE . "/components/com_maqmahelpdesk/includes/phpmailer/class.phpmailer.php");

// Get SMS configuration
//$database->setQuery( "SELECT * FROM #__support_sms_config WHERE id='1'" );
//$database->loadObject( $sms );

// If it's a new reply gets the ticket info
if ($addon_action == 0) {
	$database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'");
	$database->loadObject($ticket_info);
	$the_mask = $ticket_info->ticketmask;
} else {
	$the_mask = $row->ticketmask;
}

print "<br>ADDON START_________";
print "<br><pre>";
print_r($row);
print "</pre>";
if (isset($ticket_info)) {
	print "<br><pre>";
	print_r($ticket_info);
	print "</pre>";
}
print "<br>ticket_id = $ticket_id";
print "<br>reply_id = $reply_id";
print "<br>addon_action = $addon_action";
print "<br>my->id = " . $user->id;
print "<br>ADDON END___________";
print "<br><hr>";

$sms_message = "Ticket #" . $the_mask . " (" . $row->subject . ") " . ($addon_action == 0 ? 'updated' : 'created');
if (strlen($sms_message) < 150) {
	$sms_message .= ' ' . utf8_decode(HelpdeskUser::GetName($user->id));
}
if (strlen($sms_message) > 150) {
	$sms_message = JString::substr($sms_message, 0, 150);
}

// Get the user SMS
if ($user->id == $row->id_user) { // is the customer adding warns, support user
	print "<p><b>is the customer adding, warns support user</b>";
	$sql = "SELECT s.`phone` FROM `#__support_users` AS s WHERE s.`id_user`='" . $row->assign_to . "'";
} else { // is the support user adding, warns customer
	print "<p><b>is the support user adding, warns customer</b>";
	$sql = "SELECT s.`phone` FROM `#__support_users` AS s WHERE s.`id_user`='" . $row->id_user . "'";
}
print "<br>$sql";
$database->setQuery($sql);
$user_data = $database->loadResult();

// Phone number is not filled returns out
if ($user_data == '') {
	return true;
}

// Using iPipi
$mail = new PHPMailer23();
$mail->IsSMTP();
$mail->Host = 'ipipi.com';
$mail->SMTPAuth = true;
$mail->Port = 25;
$mail->Username = 'globodigital';
$mail->Password = 'aAeoBa01';
$mail->From = 'globodigital@sms.ipipi.com';
$mail->FromName = 'Customer Service';
$mail->AddAddress($user_data . "@sms.ipipi.com", ''); // example number +351938477950
$mail->Subject = "";
$mail->Body = $sms_message;
if (!$mail->Send()) {
	$message = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
} else {
	$message = "Message has been sent: " . $sms_message;

	$database->setQuery("INSERT INTO #__support_sms_log(id_user, id_user_message, id_ticket, phone_number, date_message, action) VALUES('" . $user->id . "', '" . ($user->id == $row->id_user ? $row->assign_to : $row->id_user) . "', '" . $ticket_id . "', '" . $user_data . "', '" . date("Y-m-d H:i") . "', '" . $addon_action . "')");
	$database->query();
}

echo "<p>->" . $message;

?>