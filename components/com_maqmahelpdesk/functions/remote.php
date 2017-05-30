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

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();

/*
	http://localhost/joomla/index.php?option=com_maqmahelpdesk&format=raw&tmpl=component&task=remote_login&callback=?&username=admin&password=admin
	http://localhost/joomla/index.php?option=com_maqmahelpdesk&format=raw&tmpl=component&task=remote_items&callback=?
 */

// Activities logger
HelpdeskUtility::ActivityLog('site', 'remote', $task);

switch ($task) {
	case 'login':
		RemoteLogin();
		break;
	case 'items':
		RemoteItems();
		break;
	case 'ticket':
		RemoteTicket();
		break;
	case 'reply':
		RemoteReply();
		break;
}

function RemoteLogin()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: text/xml');

	$username = JRequest::getVar('username', '', '', 'string');
	$password = JRequest::getVar('password', '', '', 'string');

	$credentials = array('username' => $username, 'password' => $password);
	$application = JFactory::getApplication('site');
	$application->login($credentials, $options = array());

	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	if (isset($user->id) && $user->id) {
		// TODO Get the user details to check the permissions
		/*$sql = "SELECT
				  FROM `#__support_permission`
				  WHERE ";
		  $database->setQuery( $sql );
		  $support = $database->loadObjectList();*/

		// Get status list
		$sql = "SELECT `id`, `description`, `status_group` FROM `#__support_status` WHERE `show`=1";
		$database->setQuery($sql);
		$status = $database->loadObjectList();

		// Get priorities list
		$sql = "SELECT `id`, `description` FROM `#__support_priority` WHERE `show`=1";
		$database->setQuery($sql);
		$priorities = $database->loadObjectList();

		// Get categories list
		$sql = "SELECT `id`, `name`, `id_workgroup`, `level` FROM `#__support_category` WHERE `show`=1";
		$database->setQuery($sql);
		$categories = $database->loadObjectList();

		echo $_GET['callback'] . '(' . json_encode( array('response'=>'OK', 'name'=>utf8_encode('Pedro Goncalves'), 'id'=>62, 'status'=>$status, 'priorities'=>$priorities, 'categories'=>$categories) ) . ')';
	} else {
		echo $_GET['callback'] . '(' . json_encode( array('response'=>'ERROR', 'description'=>'Incorrect login!!!') ) . ')';
	}
}

function RemoteItems()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: text/xml');

	$username = JRequest::getVar('username', '', '', 'string');
	$password = JRequest::getVar('password', '', '', 'string');

	$credentials = array('username' => $username, 'password' => $password);
	$application = JFactory::getApplication('site');
	$application->login($credentials, $options = array());

	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	if (isset($user->id) && $user->id) {
		// TODO Get the user details

		// Get tickets list
		$sql = "SELECT t.`id`, t.`subject`, t.`last_update`, s.`description` AS status
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
				WHERE s.`status_group`='O'
				  AND t.`assign_to`=" . $user->id . "
				ORDER BY t.`last_update` ASC";
		$database->setQuery($sql);
		$tickets = $database->loadObjectList();

		// Set the output
		$jsonData = '<?xml version="1.0" encoding="utf-8"?>';
		$jsonData .= '<output>';
		$jsonData .= '   <status>OK</status>';
		foreach ($tickets as $item) {
			$jsonData .= '   <ticket id="' . $item->id . '">';
			$jsonData .= '	  <subject>' . $item->subject . '</subject>';
			$jsonData .= '	  <last_update>' . JString::substr($item->last_update, 0, strlen($item->last_update) - 3) . '</last_update>';
			$jsonData .= '	  <status>' . $item->status . '</status>';
			$jsonData .= '   </ticket>';
		}
		$jsonData .= '</output>';
		echo $jsonData;
	} else {
		$jsonData = '<?xml version="1.0" encoding="utf-8"?>';
		$jsonData .= '<output>';
		$jsonData .= '   <status>Incorrect login!!!</status>';
		$jsonData .= '</output>';
		echo $jsonData;
	}
}

function RemoteTicket()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: text/xml');

	$ticketid = JRequest::getVar('ticketid', 0, '', 'int');
	$username = JRequest::getVar('username', '', '', 'string');
	$password = JRequest::getVar('password', '', '', 'string');

	$credentials = array('username' => $username, 'password' => $password);
	$application = JFactory::getApplication('site');
	$application->login($credentials, $options = array());

	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	if (isset($user->id) && $user->id) {
		// TODO Get the user details

		// Get ticket details
		$sql = "SELECT t.`id`, t.`id_workgroup`, t.`id_status`, t.`id_user`, t.`id_category`, t.`id_priority`, t.`id_client`, t.`date`, t.`subject`, t.`message`, t.`last_update`,
		t.`assign_to`, t.`source`, t.`ticketmask`, t.`an_name`, t.`an_mail`, t.`duedate`, w.`wkdesc` AS workgroup, s.`description` AS status, c.`name` AS category, p.`description` AS priority, 
		l.`clientname` AS client, u.`name` AS agent
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_workgroup` AS w ON w.`id`=t.`id_workgroup`
					 INNER JOIN `#__support_status`	AS s ON s.`id`=t.`id_status`
					 INNER JOIN `#__support_priority`  AS p ON p.`id`=t.`id_priority`
					 LEFT JOIN `#__support_category`  AS c ON c.`id`=t.`id_category`
					 LEFT JOIN  `#__support_client`	AS l ON l.`id`=t.`id_client`
					 LEFT JOIN  `#__users`			 AS u ON u.`id`=t.`assign_to`
				WHERE t.`id`=" . $ticketid;
		$database->setQuery($sql);
		$ticket = $database->loadObject();

		// Get ticket messages
		$sql = "SELECT t.`id`, t.`subject`, t.`last_update`, s.`description` AS status
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
				WHERE s.`status_group`='O'
				  AND t.`assign_to`=" . $user->id . "
				ORDER BY t.`last_update` ASC";
		$database->setQuery($sql);
		$messages = $database->loadObjectList();

		// Set the output
		$jsonData = '<?xml version="1.0" encoding="utf-8"?>';
		$jsonData .= '<output>';
		$jsonData .= '   <status>OK</status>';
		$jsonData .= '   <id>' . $ticket->id . '</id>';
		$jsonData .= '   <id_workgroup>' . $ticket->id_workgroup . '</id_workgroup>';
		$jsonData .= '   <id_status>' . $ticket->id_status . '</id_status>';
		$jsonData .= '   <id_user>' . $ticket->id_user . '</id_user>';
		$jsonData .= '   <id_category>' . $ticket->id_category . '</id_category>';
		$jsonData .= '   <id_priority>' . $ticket->id_priority . '</id_priority>';
		$jsonData .= '   <id_client>' . $ticket->id_client . '</id_client>';
		$jsonData .= '   <date>' . JString::substr($ticket->date, 0, strlen($ticket->date) - 3) . '</date>';
		$jsonData .= '   <subject>' . $ticket->subject . '</subject>';
		//$jsonData.= '   <message>'.strip_tags($ticket->message).'</message>';
		$jsonData .= '   <last_update>' . JString::substr($ticket->last_update, 0, strlen($ticket->last_update) - 3) . '</last_update>';
		$jsonData .= '   <assign_to>' . $ticket->assign_to . '</assign_to>';
		$jsonData .= '   <ticketmask>' . $ticket->ticketmask . '</ticketmask>';
		$jsonData .= '   <an_name>' . $ticket->an_name . '</an_name>';
		$jsonData .= '   <an_mail>' . $ticket->an_mail . '</an_mail>';
		$jsonData .= '   <duedate>' . JString::substr($ticket->duedate, 0, strlen($ticket->duedate) - 3) . '</duedate>';
		$jsonData .= '   <workgroup>' . $ticket->workgroup . '</workgroup>';
		$jsonData .= '   <status>' . $ticket->status . '</status>';
		$jsonData .= '   <category>' . $ticket->category . '</category>';
		$jsonData .= '   <priority>' . $ticket->priority . '</priority>';
		$jsonData .= '   <client>' . $ticket->client . '</client>';
		$jsonData .= '   <agent>' . $ticket->agent . '</agent>';
		foreach ($messages as $item) {

		}
		$jsonData .= '</output>';
		echo $jsonData;
	} else {
		$jsonData = '<?xml version="1.0" encoding="utf-8"?>';
		$jsonData .= '<output>';
		$jsonData .= '   <status>Incorrect login!!!</status>';
		$jsonData .= '</output>';
		echo $jsonData;
	}
}

function RemoteReply()
{

}

