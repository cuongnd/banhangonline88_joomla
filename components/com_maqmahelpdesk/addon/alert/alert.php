<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: alert.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

$UserName = JRequest::getCmd('UserName', '', '', 'string');
$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
	return false;
}

// Check if user is from support staff
$database->setQuery("SELECT DISTINCT(p.id_user) FROM #__support_permission p INNER JOIN #__users u ON u.id=p.id_user WHERE u.username=" . $database->quote($UserName));
$is_support = 0;
$is_support = $database->loadResult();
//print "<p>IS SUPPORT: $is_support";

// Check if add-on is published
$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='alert'");
$published = $database->loadResult();
//print "<p>PUBLISHED: $published";

if ($published && $is_support > 0) {
	//get the assigned tickets
	$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t, #__support_status s WHERE t.id_status=s.id AND s.status_group='O' AND t.assign_to='" . $is_support . "'");
	$rows_assign = $database->loadResult();
	echo $database->getErrorMsg();
	//print "<p>ROWS ASSIGN: $rows_assign";

	//get the tickets from today (independent of user)
	$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t WHERE SUBSTRING(t.date, 0, 10)='" . date("Y-m-d") . "'");
	$rows_today = $database->loadResult();
	echo $database->getErrorMsg();
	//print "<p>ROWS TODAY: $rows_today";

	//get the tickets opened (independent of user)
	$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t, #__support_status s WHERE t.id_status=s.id AND s.status_group='O'");
	$rows_tickets = $database->loadResult();
	echo $database->getErrorMsg();
	//print "<p>ROWS TICKETS: $rows_tickets";

	//get the tickets with todays duedate
	$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t, #__support_status s WHERE t.id_status=s.id AND s.status_group='O' AND t.assign_to='" . $is_support . "' AND SUBSTRING(t.duedate,1,10)='" . date("Y-m-d") . "'");
	$rows_duedate = $database->loadResult();
	echo $database->getErrorMsg();
	//print "<p>ROWS TICKETS: $rows_tickets";

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<Tickets>\n";
	echo "<Opened>" . $rows_tickets . "</Opened>\n";
	echo "<Today>" . $rows_today . "</Today>\n";
	echo "<Assigned>" . $rows_assign . "</Assigned>\n";
	echo "<DueToday>" . $rows_duedate . "</DueToday>\n";
	echo "</Tickets>";
}
