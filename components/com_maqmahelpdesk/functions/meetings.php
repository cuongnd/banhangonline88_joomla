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

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/bbb.php');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'meetings', $task, $id);

switch ($task) {
	case "view":
		viewMeeting();
		break;

	case "new":
		newMeeting();
		break;

	case "start":
		startMeeting();
		break;

	case "join":
		joinMeeting();
		break;

	case "link":
		saveLink();
		break;

	default:
		showMeetings();
		break;
}

function showMeetings()
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	$sql = "SELECT b.`id`, b.`meeting_date`, b.`meeting_hours`, t.`ticketmask`, t.`subject`, b.`id_ticket`
			FROM `#__support_bbb` AS b
				 INNER JOIN `#__support_ticket` AS t ON t.`id` = b.`id_ticket`
			WHERE (b.`id_user`=" . $user->id . " OR '" . $user->email . "' IN (SELECT i.`invite` FROM `#__support_bbb_invites` AS i WHERE i.`id_meeting`=b.`id`))
			ORDER BY `meeting_date` DESC, `meeting_hours` DESC";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	HelpdeskUtility::PageTitle('showMeetings');

	$tmplfile = HelpdeskTemplate::GetFile('meetings/list');
	include $tmplfile;
}

function newMeeting()
{
	//if( $is_support ) {
	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$id_ticket = JRequest::getInt('id_ticket', 0);
	$date = JRequest::getVar('date', '', '', 'string');
	$hours = JRequest::getVar('hours', '', '', 'string');
	$invites = JRequest::getVar('meeting_invites', '', '', 'string');

	// Insert meeting
	$sql = "INSERT INTO `#__support_bbb`(`id_user`, `date_created`, `meeting_date`, `meeting_hours`, `id_ticket`)
				VALUES('" . $user->id . "', '" . date("Y-m-d H:i:s") . "', '" . $date . "', '" . $hours . "', '" . $id_ticket . "')";
	$database->setQuery($sql);
	$database->query();
	$id_meeting = $database->insertid();

	// Get ticket details
	$sql = "SELECT `subject`, `ticketmask`
				FROM `#__support_ticket`
				WHERE `id`=" . $id_ticket;
	$database->setQuery($sql);
	$ticket = $database->loadObject();

	// Insert invites
	$invites = explode(PHP_EOL, $invites);
	for ($i = 0; $i < count($invites); $i++) {
		if (trim($invites[$i]) != '') {
			$sql = "INSERT INTO `#__support_bbb_invites`(`id_meeting`, `invite`)
						VALUES('" . $id_meeting . "', '" . $invites[$i] . "')";
			$database->setQuery($sql);
			$database->query();

			$htmlBody = sprintf(JText::_("MEETINGS_INVITE_EMAIL"), $ticket->ticketmask, $ticket->subject, $date, $hours);
			unset($mailer);
			$mailer = JFactory::getMailer();
			$mailer->addRecipient($invites[$i]);
			$mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
			$mailer->setSubject($CONFIG->sitename);
			$mailer->setBody($htmlBody);
			$mailer->IsHTML(true);
			$mailer->Send();
		}
	}
	/*}else{
		 $content['message'] = '<p>No valid permissions!</p>';
		 $content['joinurl'] = '';
		 echo json_encode($content);
		 return;
	 }*/
}

function startMeeting()
{
	//if( $is_support ) {
	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$id_ticket = JRequest::getInt('id_ticket', 0);
	$id_meeting = JRequest::getInt('id_meeting', 0);

	//Calls endMeeting on the bigbluebutton server
	$response = BigBlueButton::createMeetingArray($user->username, $id_ticket . '-' . $id_meeting, null, "mp", "ap", $supportConfig->bbb_apikey, $supportConfig->bbb_url, JURI::root());

	//If the server is unreachable
	if (!$response) {
		$content['message'] = '<p>Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running!</p>';
		$content['joinurl'] = '';
		echo json_encode($content);
		return;
		//The meeting was not created
	} elseif ($response['returncode'] == 'FAILED') {
		if ($response['messageKey'] == 'checksumError') {
			$content['message'] = '<p>A checksum error occured. Make sure you entered the correct salt!</p>';
			$content['joinurl'] = '';
			echo json_encode($content);
			return;
		} else {
			$content['message'] = $response['message'];
			$content['joinurl'] = '';
			echo json_encode($content);
			return;
		}
		//The meeting was created, and the user will now be joined
	} else {
		$bbb_joinURL = BigBlueButton::joinURL($id_ticket . '-' . $id_meeting, $user->username, "mp", $supportConfig->bbb_apikey, $supportConfig->bbb_url);
	}

	$inviteURL = JURI::root() . 'index.php?option=com_maqmahelpdesk&task=meetings_join&tmpl=component&meetingID=' . $id_ticket . '-' . $id_meeting;

	// Returns to ticket view
	$content['joinurl'] = $bbb_joinURL;
	$content['message'] = '<p>Meeting link is <br /><a href="' . $bbb_joinURL . '" target="_blank"><small>' . $bbb_joinURL . '</small></a></p><p>Invite link is <br /><a href="' . $inviteURL . '" target="_blank"><small>' . $inviteURL . '</small></a></p>';
	echo json_encode($content);

	// Get ticket details
	$sql = "SELECT `subject`, `ticketmask`
				FROM `#__support_ticket`
				WHERE `ticketmask`=" . $id_ticket;
	$database->setQuery($sql);
	$ticket = $database->loadObject();

	// Get meeting invites
	$sql = "SELECT `invite`
				FROM `#__support_bbb_invites`
				WHERE `id_meeting`=" . $id_meeting;
	$database->setQuery($sql);
	$invites = $database->loadObjectList();

	$htmlBody = sprintf(JText::_("MEETINGS_START_EMAIL"), $ticket->ticketmask, $ticket->subject, $inviteURL);
	for ($i = 0; $i < count($invites); $i++) {
		unset($mailer);
		$mailer = JFactory::getMailer();
		$mailer->addRecipient($invites[$i]->invite);
		$mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
		$mailer->setSubject($CONFIG->sitename);
		$mailer->setBody($htmlBody);
		$mailer->IsHTML(true);
		$mailer->Send();
	}
	/*}else{
		 $content['message'] = '<p>No valid permissions!</p>';
		 $content['joinurl'] = '';
		 echo json_encode($content);
		 return;
	 }*/
}

function joinMeeting()
{
	$supportConfig = HelpdeskUtility::GetConfig();
	$meetingID = JRequest::getInt('meetingID', 0);
	$username = JRequest::getVar('username', '');

	if ($username == '') {
		?>
	<form id="joinForm" name="joinForm" method="post" action="index.php">
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="meetings_join"/>
		<input type="hidden" name="tmpl" value="component"/>
		<input type="hidden" name="meetingID" value="<?php echo $meetingID;?>"/>
		<input type="hidden" name="username" value="<?php echo $username;?>"/>

		<p>Username: <input type="text" name="username" value=""/></p>

		<p><input type="submit" name="submit" value="Join"/></p>
	</form><?php
	} else {
		$bbb_joinURL = BigBlueButton::joinURL($meetingID, $username, "ap", $supportConfig->bbb_apikey, $supportConfig->bbb_url);
		header("Location: $bbb_joinURL");
	}
}

function saveLink()
{
	//if( $is_support ) {
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$id_meeting = JRequest::getInt('id_meeting', 0);
	$link = JRequest::getVar('link', '', '', 'string');

	// Insert meeting
	$sql = "INSERT INTO `#__support_bbb_links`(`id_meeting`, `id_user`, `link`)
				VALUES('" . $id_meeting . "', " . $user->id . ", '" . $link . "')";
	echo $sql;
	$database->setQuery($sql);
	$database->query();
	//}
}
