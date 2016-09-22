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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/addon.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/contract.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/digistore.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/geo.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/sms.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/user.php');

$id_workgroup = intval(JRequest::getVar('id_workgroup', 0, 'REQUEST', 'int'));
$filter_ticketid = JRequest::getVar('filter_ticketid', 0, '', 'string');
$id = intval(JRequest::getVar('id', '', '', 'string'));
$extid = intval(JRequest::getVar('extid', 0, '', 'int'));
$GLOBALS['extid'] = $extid;

$user = JFactory::getUser();

// Activities logger
HelpdeskUtility::ActivityLog('site', 'ticket', $task, $id);

// DigiStore integration
if (HelpdeskDigistore::Validation())
{
	$task = '';
}

switch ($task)
{
	case "notify":
		notifyFiles();
		break;
	
    case "views":
        $is_support ? manageViews() : HelpdeskValidation::NoAccessQuit();
        break;

    case "saveview":
        $is_support ? saveView() : HelpdeskValidation::NoAccessQuit();
        break;

    case "editview":
        $is_support ? editView() : HelpdeskValidation::NoAccessQuit();
        break;

    case "deleteview":
        $is_support ? deleteView() : HelpdeskValidation::NoAccessQuit();
        break;

    case "statuslist":
        HelpdeskStatus::GetList();
        break;

    case "parent":
        HelpdeskValidation::ValidPermissions($task) ? MakeParent() : HelpdeskValidation::NoAccessQuit();
        break;

    case "sticky":
        HelpdeskValidation::ValidPermissions($task) ? Sticky() : HelpdeskValidation::NoAccessQuit();
        break;

    case "checkticket":
        CheckTicket();
        break;

    case "changewk":
        HelpdeskValidation::ValidPermissions($task) ? setWorkgroup() : HelpdeskValidation::NoAccessQuit();
        break;

    case "setstatus":
        HelpdeskValidation::ValidPermissions($task) ? setStatus() : HelpdeskValidation::NoAccessQuit();
        break;

    case "quickreply":
        HelpdeskValidation::ValidPermissions($task) ? QuickReply() : HelpdeskValidation::NoAccessQuit();
        break;

    case "savenote":
        saveNote($id);
        break;

    case "savetask":
        saveTask($id);
        break;

    case "view":
        HelpdeskValidation::ValidPermissions($task) ? (!$user->id ? anonymousViewTicket($filter_ticketid, 0) : viewTicket($id, 0)) : HelpdeskValidation::NoAccessQuit();
        break;

    case "print":
        HelpdeskValidation::ValidPermissions($task) ? (!$user->id ? anonymousViewTicket($filter_ticketid, 1) : viewTicket($id, 1)) : HelpdeskValidation::NoAccessQuit();
        break;

    case "new":
        HelpdeskValidation::ValidPermissions($task) ? (!$user->id ? anonymousNewTicket() : newTicket()) : HelpdeskValidation::NoAccessQuit();
        break;

    case "save":
        HelpdeskValidation::ValidPermissions($task) ? (!$user->id ? anonymousSaveTicket(JRequest::getVar('problem', '', 'POST', 'string', 2)) : saveTicket(JRequest::getVar('problem', '', 'POST', 'string', 2), JRequest::getVar('reply', '', 'POST', 'string', 2))) : HelpdeskValidation::NoAccessQuit();
        break;

    case "reply":
        !$user->id ? anonymousSaveReply() : (HelpdeskValidation::ValidPermissions($task) ? saveReply() : HelpdeskValidation::NoAccessQuit());
        break;

    case "bookmark":
        HelpdeskValidation::ValidPermissions($task) ? bookmarkTicket($id) : HelpdeskValidation::NoAccessQuit();
        break;

    case "download":
        HelpdeskFile::Download($id, $extid, 'T');
        break;

    case "delattach":
        if (HelpdeskValidation::ValidPermissions($task)) {
            HelpdeskFile::deleteFile($id, $extid, 'T');
            $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $extid, false);
            $mainframe->redirect($url);
        } else {
            HelpdeskValidation::NoAccessQuit();
        }
        break;

    case "cancel":
    case "my":
        HelpdeskValidation::ValidPermissions($task) ? (!$user->id ? showAnonymousTickets() : showMyTickets()) : HelpdeskValidation::NoAccessQuit();
        break;

    case "report":
        reportTickets();
        break;

    case "analysis":
        HelpdeskValidation::ValidPermissions($task) ? ticketAnalysis() : HelpdeskValidation::NoAccessQuit();
        break;

    case "replieseditor":
        RepliesEditor();
        break;

    case "approve":
        approveTicket();
        break;

    case "delete":
        deleteTicket();
        break;

    case "duplicate":
        HelpdeskValidation::ValidPermissions($task) ? newTicket($id) : HelpdeskValidation::NoAccessQuit();
        break;

    case "field":
        HelpdeskValidation::ValidPermissions('new') ? ClientField() : '';
        break;
}

function ClientField()
{
	$database =& JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$ticket_field = JRequest::getInt('id_field',0);

	if($ticket_field && $is_support)
	{
		// Get Custom Fields for the Workgroup
		$sql = "SELECT c.id, w.id_workgroup, w.id_field, w.required, w.support_only, w.new_only, c.caption, c.ftype, c.value, c.size, c.maxlength, c.tooltip, w.section, w.id_category
			FROM #__support_wk_fields as w, #__support_custom_fields as c
			WHERE w.id_field=c.id AND c.id='" . $ticket_field . "' AND c.cftype='W'
			ORDER BY w.section, w.ordering";
		$database->setQuery($sql);
		$customfield = $database->loadObject();

		echo HelpdeskForm::WriteField(0, $ticket_field, $customfield->ftype, $customfield->value, $customfield->size, $customfield->maxlength, 0, 0, 0, 0, 0, $customfield->tooltip);
	}

	return;
}

function notifyFiles()
{
	$database =& JFactory::getDBO();
	$user =& JFactory::getUser();
	
	$id = JRequest::getVar( 'id', 0, '', 'int' );
	
	$sql = "SELECT `id_file` FROM `#__support_file` WHERE `source`='T' AND `id`=".$id;
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	
	for($i=0; $i<count($rows); $i++) {
		$row = $rows[$i];
		$sql = "INSERT INTO `#__support_file_notify`(`id_file`, `id_user`)
				VALUES(".$row->id_file.", ".$user->id.");";
		$database->setQuery($sql);
		$database->query();
	}
}

function RepliesEditor()
{
    $database = JFactory::getDBO();
    $Itemid = JRequest::getInt('Itemid', 0);
    $inline = JRequest::getInt('inline', 0);

    // Replies
    $sql = 'SELECT `id`, `subject`, `answer`
            FROM #__support_reply
            ORDER BY `subject`';
    $database->setQuery($sql);
    $replies = $database->loadObjectList();

	if (!$inline)
	{
		$tmplfile = HelpdeskTemplate::GetFile('editor/replies_inline');
	}
	else
	{
		$replies = JHTML::_('select.genericlist', $replies, 'reply', 'size="15"', 'id', 'subject', null);
		$tmplfile = HelpdeskTemplate::GetFile('editor/replies');
	}

	include $tmplfile;
}

function anonymousSaveTicket($problem)
{
    global $task;

    $mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
    $user = JFactory::getUser();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$valcalc = JRequest::getVar('valcalc', 0, '', 'int');
    JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Validation check
	if ($valcalc != $session->get("calculation"))
	{
		die('<script type="text/javascript"> history.go(-1); </script>');
	}

    $database = JFactory::getDBO();
    $CONFIG = new JConfig();
    $supportConfig = HelpdeskUtility::GetConfig();

    // Initialise variables
    $wkoptions = null;

    // Get Workgroup Options
    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    //////////////////////////
    //	Basic Ticket Setup	//
    //////////////////////////
    // Gather minimum required values
	$now = HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S");
    $status_id = HelpdeskStatus::GetDefault() ? HelpdeskStatus::GetDefault() : JRequest::getVar('id_status', '', 'POST', 'string');
    $id_workgroup = intval(JRequest::getVar('id_workgroup', '', 'POST', 'string'));
    $id_category = intval(JRequest::getVar('id_category', '', 'POST', 'string'));
    $id_directory = intval(JRequest::getVar('id_directory', '', 'POST', 'string'));
    $date = $now;
    $subject = stripslashes(JRequest::getVar('subject', '', 'POST', 'string'));
    $an_name = stripslashes(JRequest::getVar('an_name', '', 'POST', 'string'));
    $an_mail = stripslashes(JRequest::getVar('an_mail', '', 'POST', 'string'));
    $message = stripslashes($_POST['problem']);
    /*if (!$is_support) {
        $message = nl2br($message);
    }*/
    $last_update = $now;
    $wkoptions->auto_assign ? $assign_to = $wkoptions->auto_assign : $assign_to = 0;
    $id_priority = intval(JRequest::getVar('id_priority', '', 'POST', 'string'));
    $source = HelpdeskTicket::VerifySource(JRequest::getVar('source', '', 'POST', 'string'));
    $duedate = JRequest::getVar('duedate_date', '', 'POST', 'string') . ' ' . JRequest::getVar('duedate_hours', '', 'POST', 'string');
    !$duedate ? $duedate = HelpdeskTicket::ReturnDueDate(JString::substr($date, 0, 4), JString::substr($date, 5, 2), JString::substr($date, 8, 2), JString::substr($date, 11, 2), JString::substr($date, 14, 2), $id_priority) : '';

    // Verify against StopForumSpam
    $spamcheck = true;
    if ($supportConfig->stopspam) {
        $spamcheck = HelpdeskValidation::CheckStopSpam($an_mail, HelpdeskUser::GetIP());
    }
    if (!$spamcheck) {
        print "<p>STOP FORUM SPAM POSITIVE: " . JText::_('form_not_submited') . "</p>";
        die();
    }

    // Verify minimum requirements are met
    if ($an_name == '' || $an_mail == '' || !$subject || !$message) {
        HelpdeskUtility::AddGlobalMessage(JText::_('tkt_reqs_blank'), 'e');
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
        exit();
    }

    $sql = "SELECT id 
			FROM #__support_ticket 
			WHERE subject = " . $database->quote($subject) . "
			  AND SUBSTRING(date,1,10) = '" . JString::substr($date, 0, 10) . "'
			  AND ipaddress = '" . HelpdeskUser::GetIP() . "'
			  AND assign_to = '" . $assign_to . "'
			LIMIT 1 ";
    $database->setQuery($sql);
    $check_double_submition = $database->loadResult();
    if ($check_double_submition > 0) {
        HelpdeskUtility::AddGlobalMessage(JText::_('alert_double_form_submition'), 'e');
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
        exit();
    }

    //////////////////////////////////////////////////
    // Insert basic ticket and set ticket number	//
    //////////////////////////////////////////////////
    $database->setQuery("INSERT INTO #__support_ticket(id_workgroup, id_status, id_user, id_category, date, subject, message, last_update, assign_to, id_priority, source, an_name, an_mail, duedate, day_week, id_client, ipaddress, id_directory) 
						  VALUES('" . $id_workgroup . "', '" . $status_id . "', '0', '" . $id_category . "', " . $database->quote($date) . ", " . $database->quote($subject) . ", " . $database->quote($message) . ", '" . $last_update . "', '" . $assign_to . "', '" . $id_priority . "', " . $database->quote($source) . ", " . $database->quote($an_name) . ", " . $database->quote($an_mail) . ", '$duedate', '" . HelpdeskDate::DateOffset("%w") . "', '0', '" . HelpdeskUser::GetIP() . "', " . $id_directory . ")");
    $database->query();

    if ($database->getErrorMsg() != '') {
        HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
        exit();
    } else {
        $ticket_id = $database->insertid();

        $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'");
        $ticket = $database->loadObject();

        if ($supportConfig->tickets_numbers) {
            $ticketmask = rand(10, 99) . $ticket_id . rand(1000, 9999);
        } else {
            $ticketmask = $ticket_id;
        }
        $database->setQuery("UPDATE #__support_ticket SET ticketmask='" . $ticketmask . "' WHERE id='" . $ticket_id . "'");
        if (!$database->query()) {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
            exit();
        }

        $url_view_ticket = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&filter_ticketid=' . $ticketmask . '&filter_email=' . urlencode($an_mail);
        $url_new_ticket = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_new', false);
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('anonymous_ticket_create_sucess'), $ticketmask, $ticketmask, $an_mail, $url_view_ticket, $url_new_ticket), 'i');
    }

    //////////////////////////////////////
    //	Update Tickets Custom Fields	//
    //////////////////////////////////////
    $sql = "SELECT f.id_field, c.caption, c.ftype, f.id_category 
			FROM #__support_wk_fields AS f 
				 INNER JOIN #__support_custom_fields AS c ON c.id=f.id_field 
			WHERE f.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' 
			ORDER BY f.ordering";
    $database->setQuery($sql);
    $customfields = $database->loadObjectList();

    $x = 0;
    $database->setQuery("DELETE FROM #__support_field_value WHERE id_ticket='" . $ticket_id . "'");
    $database->query();
    $cfields_array = array();
    for ($x = 0; $x < count($customfields); $x++) {
        $ticketField = $customfields[$x];

        if (($ticketField->ftype == "checkbox")) {
            $custom_val2 = serialize(JRequest::getVar('custom' . $ticketField->id_field, '', '', 'array'));
            $custom_val = unserialize($custom_val2);
            if (is_array($custom_val)) {
                $tmp_custom_val = "";
                for ($t = 0; $t < sizeof($custom_val); $t++) {
                    $tmp_custom_val .= $custom_val[$t] . ",";
                }
                $custom_val = JString::substr($tmp_custom_val, 0, strlen($tmp_custom_val) - 1);
            }
        } else {
            $custom_val = JRequest::getVar('custom' . $ticketField->id_field, '', '', 'string');
	        $custom_val = str_replace('"', '', $custom_val);
            $custom_val = stripslashes($custom_val);
        }

        $cfields_array_tmp = array('[cfield' . $ticketField->id_field . '_caption]' => $ticketField->caption,
            '[cfield' . $ticketField->id_field . '_value]' => HelpdeskUtility::String2HTML($custom_val));
        $cfields_array = array_merge($cfields_array, $cfields_array_tmp);
        $database->setQuery("INSERT INTO #__support_field_value(id_field, id_ticket, newfield) VALUES('" . $ticketField->id_field . "', '" . $ticket_id . "', " . $database->quote($custom_val) . ")");

        if (!$database->query()) {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        }
    }

    // Intialise variables
    $ticket = null;
    $managersInfo = null;
    $assigned = null;
	$attachments = null;

    // Get Ticket Details
    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'");
    $ticket = $database->loadObject();

    // Get Ticket Users Client ID
    $client_id = 0;

    // Get Ticket Users Client Name
    $client_name = '';

    // Get Ticket Source Name
    $source_name = HelpdeskTicket::SwitchSource($ticket->source);

    // Get Ticket Categories
    $category_id_new = intval(JRequest::getVar('id_category', $ticket->id_category, 'POST', 'int'));
    $category_id_old = $ticket->id_category;

    // Get Ticket Status
    $status_id_old = $ticket->id_status;
    $status_id_new = intval(JRequest::getVar('id_status', $ticket->id_status, 'POST', 'int'));

    // Get Assigned New and Old User
    $assigned_id_new = intval(JRequest::getVar('assign_to', 0, 'POST', 'int'));
    $database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_new'");
    $assigned_new = $database->loadObject();

    // Set URL for this ticket
    $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&filter_ticketid=' . $ticket->ticketmask . '&filter_email=' . urlencode($ticket->an_mail), false);

    //////////////////////////////////
    // Calls the add-on's engine	//
    //////////////////////////////////
    HelpdeskAddon::Execute(2, 1, $ticket_id);

	//////////////////////
	//  Attached File	//
	//////////////////////
	for ($xx = 1; $xx <= $supportConfig->attachs_num; $xx++)
	{
		if (isset($_FILES['file' . $xx]))
		{
			if ($_FILES['file' . $xx]['name'] != '')
			{
				$fileupload = HelpdeskFile::Upload($ticket->id, 'T', "file$xx", $supportConfig->docspath, $_POST['desc' . $xx], 0, (int) $_POST['available' . $xx]);
				if ($fileupload)
				{
					$attachments[] = $fileupload;
					$ticketLogMsg = str_replace('%1', $an_name, JText::_('attached_file'));
					$ticketLogMsg = str_replace('%2', $_FILES['file' . $xx]['name'], $ticketLogMsg);
					HelpdeskTicket::Log($ticket_id, $ticketLogMsg, JText::_('attached_file_hidden'), '', 'attachfile', 0, 0, 'add_attach.png');
					HelpdeskUtility::AddGlobalMessage(JText::_('upload_ok'), 'i');
				}
			}
		}
	}

    // Set Email Notify Template variables
    $var_set = array('[duedate]' => $ticket->duedate,
        '[duedate_old]' => '',
        '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
        '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
        '[number]' => $ticket->ticketmask,
        '[assign]' => HelpdeskUtility::String2HTML((isset($assigned_new) ? $assigned_new->name : '')),
        '[assign_email]' => (isset($assigned_new) ? $assigned_new->email : ''),
        '[unassigned]' => '',
        '[unassigned_email]' => '',
        '[subject]' => HelpdeskUtility::String2HTML($ticket->subject),
        '[message]' => HelpdeskUtility::String2HTML($ticket->message),
        '[summary]' => '',
        '[author]' => $ticket->an_name,
        '[recipient]' => $ticket->an_name,
        '[email]' => $ticket->an_mail,
        '[client]' => '',
        '[url]' => $ticket_url,
        '[department]' => HelpdeskUtility::String2HTML($wkoptions->wkdesc),
        '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($id_priority)),
        '[priority_old]' => '',
        '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
        '[status_old]' => '',
        '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_new)),
        '[category_old]' => '',
        '[source]' => HelpdeskUtility::String2HTML($source),
        '[helpdesk]' => JURI::root()
    );
    $var_set = array_merge($var_set, $cfields_array);

    // Add ticket log message
    HelpdeskTicket::Log($ticket->id, str_replace('%1', $ticket->an_name, JText::_('ticket_created')), JText::_('ticket_created_hidden'), $ticket->id_status, 'status', $status_id_new, 0, 'add_ticket.png');

    // Notify user
    SendMailNotification($ticket->id, $var_set, $ticket->an_mail, 'created_mail_subject', 'created_mail_notify_confirmation', 'ticket_create_mail_notify_confirmation');

    // Notify workgroup admin
    if ($wkoptions->wkemail && $wkoptions->wkadmin_email)
    {
        $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);
        $var_set['[url]'] = $ticket_url;
        $var_set['[recipient]'] = $wkoptions->wkadmin_email;
        SendMailNotification($ticket->id, $var_set, $wkoptions->wkadmin_email, 'created_mail_subject', 'created_mail_notify_confirmation', 'ticket_create_mail_notify_confirmation', null, null, $attachments);
    }

    // Notify system admin
    if ($supportConfig->receive_mail)
    {
        $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);
        $var_set['[url]'] = $ticket_url;
        $var_set['[recipient]'] = $wkoptions->wkadmin_email;
        SendMailNotification($ticket->id, $var_set, $CONFIG->mailfrom, 'created_mail_subject', 'created_mail_notify_confirmation', 'ticket_create_mail_notify_confirmation', null, null, $attachments);
    }

    // Ticket Assignment
    if ($assigned_id_new)
    {
        // Update Ticket Assigned user
        $database->setQuery("UPDATE #__support_ticket SET assign_to='" . $assigned_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query())
        {
            $sql = "SELECT assign_report_users FROM `#__support_permission` WHERE id_user = '" . $assigned_id_new . "' AND id_workgroup = '" . $id_workgroup . "' LIMIT 1 ";
            $database->setQuery($sql);
            $additional_users_notify = $database->loadResult();
            $mailcc = array();
            if ($wkoptions->wkemail && $wkoptions->wkadmin_email)
            {
                $mailcc[] = $wkoptions->wkadmin_email; // Workgroup admin
            }
            if ($supportConfig->receive_mail)
            {
                $mailcc[] = $CONFIG->mailfrom; // System admin
            }

            if ($additional_users_notify != '')
            {
                $users_report_additional = explode('#', $additional_users_notify);
                $usercount = count($users_report_additional);
                if ($usercount > 0)
                {
                    for ($i = 0; $i < $usercount; $i++)
                    {
                        $database->setQuery("SELECT name, email FROM #__users WHERE id = '" . $users_report_additional[$i] . "' ");
                        $additional_user = $database->loadObject();
                        $mailcc[] = $additional_user->email;
                    }
                }
            }

            HelpdeskTicket::Log($ticket->id, str_replace('%1', $assigned_new->name, JText::_('ticket_assigned')), JText::_('assigned_hidden'), $ticket->id_status, '', 0, 0, 'add_assign.png');
            if ($wkoptions->tkt_asgn_new_asgn)
            {
                $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);
                $var_set['[url]'] = $ticket_url;
                $var_set['[recipient]'] = $assigned_new->name;
                SendMailNotification($ticket->id, $var_set, $assigned_new->email, 'created_mail_subject', 'created_mail_notify_support', 'ticket_create_mail_notify_support', $mailcc, null, $attachments);
                if( $supportConfig->sms_assign )
                {
                    HelpdeskSMS::SendSMS(sprintf(JText::_('SMS_ASSIGN_MESSAGE'), $ticket->subject), JText::_('ASSIGNMENT'), $ticket->id);
                }
            }
        }
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my', false);
    $mainframe->redirect($url);
}


function anonymousNewTicket()
{
    global $supportOptions;

    $database = JFactory::getDBO();
	$session = JFactory::getSession();
    $document = JFactory::getDocument();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $id_category = JRequest::getInt('id_category', 0);
    $id_directory = JRequest::getVar('id_directory', 0, '', 'int');
	$calculation1 = rand(1,9);
	$calculation2 = rand(1,9);
	$calculation = $calculation1 + $calculation2;
	$session->set("calculation", $calculation);

    $document->addScriptDeclaration( 'var MQM_CALC_VAL = '.$calculation.';' );
    $document->addScriptDeclaration( 'var MQM_IS_ANONYMOUS = true;' );
    $document->addScriptDeclaration( 'var MQM_LOADING = "'.addslashes(JText::_('loading')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG01 = "'.addslashes(JText::_('tmpl_msg01')).'";' );
    $document->addScriptDeclaration( 'var MQM_NAME = "'.addslashes(JText::_('name_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_EMAIL = "'.addslashes(JText::_('email_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_CATEGORY = "'.addslashes(JText::_('category_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG02 = "'.addslashes(JText::_('tmpl_msg02')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG04 = "'.addslashes(JText::_('tmpl_msg04')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG05 = "'.addslashes(JText::_('tmpl_msg05')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG06 = "'.addslashes(JText::_('tmpl_msg06')).'";' );
    $document->addScriptDeclaration( 'var MQM_QUESTION = "'.addslashes(JText::_('REQUIRED_QUESTION')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL = "'.addslashes(JText::_('tmpl_ticket_cancelquestion')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL_LINK = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my', false).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MONTH = "'.addslashes(JText::_('invalid_month')).'";' );
    $document->addScriptDeclaration( 'var MQM_YEAR1 = "'.(HelpdeskDate::DateOffset("%Y") + 1).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_YEAR = "'.addslashes(JText::_('invalid_year')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_DAY = "'.addslashes(JText::_('invalid_day')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MINUTES = "'.addslashes(JText::_('invalid_minutes')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_HOURS = "'.addslashes(JText::_('invalid_hours')).'";' );
    $document->addScriptDeclaration( 'var MQM_VALCALC = "'.addslashes(JText::_('message_calc_validation')).'";' );
    HelpdeskUtility::AppendResource('helpdesk.tickets.new.customer.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

    // Sets the page title
    HelpdeskUtility::PageTitle('newTicket');

    // Get Custom Fields for the Workgroup
    $sql = "SELECT c.id, w.id_workgroup, w.id_field, w.required, w.support_only, w.new_only, c.caption, c.ftype, c.value, c.size, c.maxlength, c.tooltip, w.section, w.id_category 
			FROM #__support_wk_fields as w, #__support_custom_fields as c 
			WHERE w.id_field=c.id AND w.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' 
			ORDER BY w.section, w.ordering";
    $database->setQuery($sql);
    $customfields = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';

    // Build Priority select list
    $sql = "SELECT `id` AS value, `description` AS text 
			FROM #__support_priority 
			WHERE `show`=1 
			ORDER BY description";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
    $lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'id_priority', ' id="id_priority" onchange="DueDatePonderado();"', 'value', 'text', HelpdeskPriority::GetDefault());

    $clientrate = 0;
    $clientvalue = 0;
    $clienttravel = '00:00';

    // Build workgroup categories select list
    $lists['category'] = HelpdeskForm::BuildCategories($id_category, false, true, false, false);

    // Custom fields
    $i = 1;
    $cfields_hiddenfield = "";
    $j = 1;

    foreach ($customfields as $key2 => $value2)
    {
        $fid = 0;
        $ftype = '';
        $fvalue = '';
        $fsize = '';
        $flength = '';

        if (is_object($value2))
        {
            foreach ($value2 as $key3 => $value3)
            {
                $cfields_rows[$i][$key3] = $value3;
                if ($key3 == 'id')
                    $fid = $value3;
                if ($key3 == 'ftype')
                    $ftype = $value3;
                if ($key3 == 'value')
                    $fvalue = $value3;
                if ($key3 == 'size')
                    $fsize = $value3;
                if ($key3 == 'maxlength')
                    $flength = $value3;
                if ($key3 == 'support_only')
                    $fsupportonly = $value3;
                if ($key3 == 'tooltip')
                    $ftooltip = $value3;
            }
        }

        if ((!$is_support) && ($fsupportonly > 0))
        {
            if ($fsupportonly == 2)
            {
                $cfields_hiddenfield .= HelpdeskForm::WriteField(0, $fid, 'hidden', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                unset($cfields_rows[$i]); // remove from array
            }
            else
            { // 0 or 1
                $cfields_rows[$i]['field'] = HelpdeskForm::WriteField(0, $fid, 'readonly', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                if ($j / 2 == round($j / 2)) {
                    $cfields_rows[$i]['case'] = 1;
                } else {
                    $cfields_rows[$i]['case'] = 0;
                }
                $j++;
            }
        }
        else
        {
            $exclude = (($fsupportonly && !$is_support)) ? 1 : 0;
            $cfields_rows[$i]['field'] = HelpdeskForm::WriteField(0, $fid, $ftype, $fvalue, $fsize, $flength, 0, 0, $exclude, 0, 0, $ftooltip);
            if ($j / 2 == round($j / 2))
            {
                $cfields_rows[$i]['case'] = 1;
            } else {
                $cfields_rows[$i]['case'] = 0;
            }
            $j++;
        }
        $i++;
    }

    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    // Attachments
    for ($i = 1; $i <= $supportConfig->attachs_num; $i++) {
        $attachs[$i]['number'] = $i;
        $attachs[$i]['available'] = '<input type="hidden" name="available' . $i . '" value="1" />';
    }

    // Writes the Custom Fields validation
    $html1 = "\n";
    $x = 0;
    for ($x; $x < count($customfields); $x++) {
        $cfrow = $customfields[$x];

        // Validate category
        $category_check = '';
        if ($cfrow->id_category != '') {
            if (strpos($cfrow->id_category, ',') === false) {
                $category_check = '&& $jMaQma(\'#id_category\').val()==' . $cfrow->id_category;
            } else {
                $category_check = '&& ( ';
                $categories = explode(",", $cfrow->id_category);
                for ($z = 0; $z < count($categories); $z++) {
                    $category_check .= '$jMaQma(\'#id_category\').val()==' . $categories[$z] . ' || ';
                }
                $category_check = JString::substr($category_check, 0, -3);
                $category_check .= ')';
            }
        }

        if ($cfrow->required == 1 && $cfrow->ftype != 'htmleditor' && $cfrow->ftype != 'radio' && $cfrow->ftype != 'checkbox') {
            $html1 .= "value2 = document.adminForm.custom" . $cfrow->id_field . ".value;\n";
            $html1 .= "if( value2 == '' " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    document.adminForm.custom" . $cfrow->id_field . ".focus();\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        } elseif ($cfrow->required == 1 && $cfrow->ftype == 'checkbox') {
            $fieldval = '';
            $fieldoptions = explode(',', $cfrow->value);
            for ($y = 0; $y < count($fieldoptions); $y++) {
                $fieldval .= '$jMaQma("#custom' . $cfrow->id_field . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', trim($fieldoptions[$y])) . '").is(":checked")==false && ';
            }
            $fieldval = JString::substr($fieldval, 0, strlen($fieldval) - 4);
            $html1 .= "if( " . $fieldval . " " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        } elseif ($cfrow->required == 1 && $cfrow->ftype == 'radio') {
            $fieldval = '';
            $fieldoptions = explode(',', $cfrow->value);
            for ($y = 0; $y < count($fieldoptions); $y++) {
                $fieldval .= '$jMaQma("#custom' . $cfrow->id_field . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', trim($fieldoptions[$y])) . '").is(":checked")==false && ';
            }
            $fieldval = JString::substr($fieldval, 0, strlen($fieldval) - 4);
            $html1 .= "if( " . $fieldval . " " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        }
    }

    $document->addScriptDeclaration("function CustomFieldsValidation() { $html1 return true; }");

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('tickets/add_ticket_anonymous');
    include $tmplfile;
}


function showAnonymousTickets()
{
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);

    HelpdeskUtility::PageTitle('anonymousTicketManager');

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('tickets/ticket_manager_anonymous');
    include $tmplfile;
}


function ticketAnalysis()
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $supportConfig = HelpdeskUtility::GetConfig();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
	$is_support = HelpdeskUser::IsSupport();

    HelpdeskUtility::PageTitle('analysisTicket', JText::_('tickets_analysis'));

    $limit = 20;
    $limitstart = JRequest::getInt('limitstart', 0);
    $page = JRequest::getInt('page', 0);

    // Filters
    $filter_workgroup = intval(JRequest::getVar('filter_workgroup', 0, 'REQUEST', 'int'));
    $filter_status = intval(JRequest::getVar('filter_status', 0, 'REQUEST', 'int'));
    $filter_status_group = JRequest::getVar('filter_status_group', 'O', 'REQUEST', 'string');
    $filter_category = intval(JRequest::getVar('filter_category', 0, 'REQUEST', 'int'));
    $filter_client = intval(JRequest::getVar('filter_client', 0, 'REQUEST', 'int'));
    $filter_priority = intval(JRequest::getVar('filter_priority', 0, 'REQUEST', 'int'));
    $filter_assign = intval(JRequest::getVar('filter_assign', 0, 'REQUEST', 'int'));
    $filter_year = intval(JRequest::getVar('filter_year', 0, 'REQUEST', 'int'));
    $filter_month = JRequest::getVar('filter_month', '0', 'REQUEST', 'string');
    $execute = intval(JRequest::getVar('execute', 0, 'REQUEST', 'int'));

    // Columns
    $col_ticketid = intval(JRequest::getVar('col_ticketid', 0, 'REQUEST', 'int'));
    $col_workgroup = intval(JRequest::getVar('col_workgroup', 0, 'REQUEST', 'int'));
    $col_subject = intval(JRequest::getVar('col_subject', 0, 'REQUEST', 'int'));
    $col_category = intval(JRequest::getVar('col_category', 0, 'REQUEST', 'int'));
    $col_client = intval(JRequest::getVar('col_client', 0, 'REQUEST', 'int'));
    $col_user = intval(JRequest::getVar('col_user', 0, 'REQUEST', 'int'));
    $col_duedate = intval(JRequest::getVar('col_duedate', 0, 'REQUEST', 'int'));
    $col_status = intval(JRequest::getVar('col_status', 0, 'REQUEST', 'int'));
    $col_assign = intval(JRequest::getVar('col_assign', 0, 'REQUEST', 'int'));
    $col_date_created = intval(JRequest::getVar('col_date_created', 0, 'REQUEST', 'int'));
    $col_message = intval(JRequest::getVar('col_message', 0, 'REQUEST', 'int'));
    $col_last_message = intval(JRequest::getVar('col_last_message', 0, 'REQUEST', 'int'));

    // Build Workgroup select list
    $sql = "SELECT w.`id` AS value, w.`wkdesc` AS text
            FROM #__support_workgroup AS w
                 INNER JOIN #__support_permission AS p ON p.id_workgroup=w.id AND p.id_user='" . $user->id . "'
            ORDER BY w.wkdesc";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['workgroup'] = JHTML::_('select.genericlist', $rows, 'filter_workgroup', 'style="width:300px;"', 'value', 'text', $filter_workgroup);

    // Build Statuses select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY `ordering`, `description`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['status'] = JHTML::_('select.genericlist', $rows, 'filter_status', 'style="width:300px;"', 'value', 'text', $filter_status);

    // Build Statuses Group select list
    $rows = null;
    $rows[] = JHTML::_('select.option', '', JText::_('all'));
    $rows[] = JHTML::_('select.option', 'O', JText::_('all_open'));
    $rows[] = JHTML::_('select.option', 'C', JText::_('all_closed'));
    $lists['status_group'] = JHTML::_('select.genericlist', $rows, 'filter_status_group', 'style="width:300px;"', 'value', 'text', $filter_status_group);

    // Build Categories select list
    $sql = "SELECT `id` AS value, `name` AS text FROM #__support_category ORDER BY `name`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['category'] = JHTML::_('select.genericlist', $rows, 'filter_category', 'style="width:300px;"', 'value', 'text', $filter_category);

    // Build Clients select list
    $sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER BY `clientname`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['client'] = JHTML::_('select.genericlist', $rows, 'filter_client', 'style="width:300px;"', 'value', 'text', $filter_client);

    // Build Priorities select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority ORDER BY `description`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['priority'] = JHTML::_('select.genericlist', $rows, 'filter_priority', 'style="width:300px;"', 'value', 'text', $filter_priority);

    // Build Assigned Users select list
    $sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text
            FROM #__support_permission AS p
                 INNER JOIN #__users AS u ON u.id=p.id_user
            ORDER BY u.`name`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $rows = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows);
    $lists['assign'] = JHTML::_('select.genericlist', $rows, 'filter_assign', 'style="width:300px;"', 'value', 'text', $filter_assign);

    // Build Years select list
    $years[] = JHTML::_('select.option', '0', JText::_('all'));
    for ($i = 2004; $i <= date("Y"); $i++) {
        $years[] = JHTML::_('select.option', $i, $i);
    }
    $lists['year'] = JHTML::_('select.genericlist', $years, 'filter_year', '', 'value', 'text', $filter_year);

    // Build Months select list
    $months[] = JHTML::_('select.option', '0', JText::_('all'));
    $months[] = JHTML::_('select.option', '01', JText::_('month01'));
    $months[] = JHTML::_('select.option', '02', JText::_('month02'));
    $months[] = JHTML::_('select.option', '03', JText::_('month03'));
    $months[] = JHTML::_('select.option', '04', JText::_('month04'));
    $months[] = JHTML::_('select.option', '05', JText::_('month05'));
    $months[] = JHTML::_('select.option', '06', JText::_('month06'));
    $months[] = JHTML::_('select.option', '07', JText::_('month07'));
    $months[] = JHTML::_('select.option', '08', JText::_('month08'));
    $months[] = JHTML::_('select.option', '09', JText::_('month09'));
    $months[] = JHTML::_('select.option', '10', JText::_('month10'));
    $months[] = JHTML::_('select.option', '11', JText::_('month11'));
    $months[] = JHTML::_('select.option', '12', JText::_('month12'));
    $lists['month'] = JHTML::_('select.genericlist', $months, 'filter_month', '', 'value', 'text', $filter_month);

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('reports/analysis');
    include $tmplfile;

    if ($execute)
    {
        $where = '';
        if ($filter_workgroup) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_workgroup='" . $filter_workgroup . "'";
        }
        if ($filter_client) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_client='" . $filter_client . "'";
        }
        if ($filter_status) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_status='" . $filter_status . "'";
        }
        if ($filter_status_group) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "s.status_group='" . $filter_status_group . "'";
        }
        if ($filter_assign) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.assign_to='" . $filter_assign . "'";
        }
        if ($filter_priority) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_priority='" . $filter_priority . "'";
        }
        if ($filter_category) {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_category='" . $filter_category . "'";
        }
        if ($filter_year)
        {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "YEAR(t.date)=" . $database->quote($filter_year);
        }
        if ($filter_month)
        {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "MONTH(t.date)=" . $database->quote($filter_month);
        }
        if ($is_support)
        {
            $where .= ($where == '' ? 'WHERE ' : ' AND ') . "t.id_user=" . $user->id;
        }

        $sql = "SELECT t.id, t.id_workgroup, t.ticketmask, t.subject, w.wkdesc, s.description AS status, ca.name AS category, u2.name AS supportuser, t.date, t.id_priority, t.duedate, c.clientname, u1.name, t.message, (SELECT tr.message FROM #__support_ticket_resp AS tr WHERE tr.id_ticket=t.id ORDER BY tr.id DESC LIMIT 0, 1) AS last_message
				FROM #__support_ticket AS t 
					 LEFT JOIN #__support_workgroup AS w ON w.id=t.id_workgroup 
					 LEFT JOIN #__support_client AS c ON c.id=t.id_client 
					 LEFT JOIN #__users AS u1 ON u1.id=t.id_user
					 LEFT JOIN #__support_status AS s ON s.id=t.id_status
					 LEFT JOIN #__users AS u2 ON u2.id=t.assign_to
					 LEFT JOIN #__support_category AS ca ON ca.id=t.id_category
				" . $where . " ORDER BY t.id ASC LIMIT " . $limitstart . ", " . $limit;
        $database->setQuery($sql);
        $rows = null;
        $rows = $database->loadObjectList();

        //print "<p><b>SQL:</b> $sql</p>";

        $sql = "SELECT COUNT(*)
				FROM #__support_ticket AS t 
				     LEFT JOIN #__support_workgroup AS w ON w.id=t.id_workgroup 
					 LEFT JOIN #__support_client AS c ON c.id=t.id_client 
					 LEFT JOIN #__users AS u1 ON u1.id=t.id_user
					 LEFT JOIN #__support_status AS s ON s.id=t.id_status
					 LEFT JOIN #__users AS u2 ON u2.id=t.assign_to
					 LEFT JOIN #__support_category AS ca ON ca.id=t.id_category
				" . $where;
        $database->setQuery($sql);
        $total = $database->loadResult(); ?>

	    <div class="maqmahelpdesk">
		    <table class="table table-striped table-bordered">
		        <thead>
		        <tr>
		            <?php if ($col_ticketid) { ?>
		            <th><?php echo JText::_('ticketid');         ?></th><?php } ?>
		            <?php if ($col_workgroup) { ?>
		            <th><?php echo JText::_('workgroup');        ?></th><?php } ?>
		            <?php if ($col_subject) { ?>
		            <th><?php echo JText::_('subject');          ?></th><?php } ?>
		            <?php if ($col_category) { ?>
		            <th><?php echo JText::_('category');         ?></th><?php } ?>
		            <?php if ($col_client) { ?>
		            <th><?php echo JText::_('client_name');      ?></th><?php } ?>
		            <?php if ($col_user) { ?>
		            <th><?php echo JText::_('user');             ?></th><?php } ?>
		            <?php if ($col_duedate) { ?>
		            <th><?php echo JText::_('duedate');          ?></th><?php } ?>
		            <?php if ($col_status) { ?>
		            <th><?php echo JText::_('status');           ?></th><?php } ?>
		            <?php if ($col_assign) { ?>
		            <th><?php echo JText::_('tpl_assignedto');   ?></th><?php } ?>
		            <?php if ($col_date_created) { ?>
		            <th><?php echo JText::_('date_created');     ?></th><?php } ?>
		            <?php if ($col_message) { ?>
		            <th><?php echo JText::_('message');          ?></th><?php } ?>
		            <?php if ($col_last_message) { ?>
		            <th><?php echo JText::_('last_message');     ?></th><?php } ?>
		        </tr>
		        </thead>
			    <tbody><?php
		        for ($i = 0; $i < count($rows); $i++)
		        {
		            $row = $rows[$i];
		            $link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->id; ?>
		            <tr class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>">
		                <?php if ($col_ticketid) { ?>
		                <td><a href="<?php echo $link;?>"><?php echo $row->ticketmask; ?></a></td><?php } ?>
		                <?php if ($col_workgroup) { ?>
		                <td><?php echo $row->wkdesc;       ?></td><?php } ?>
		                <?php if ($col_subject) { ?>
		                <td><?php echo $row->subject;      ?></td><?php } ?>
		                <?php if ($col_category) { ?>
		                <td><?php echo $row->category;     ?></td><?php } ?>
		                <?php if ($col_client) { ?>
		                <td><?php echo $row->clientname;   ?></td><?php } ?>
		                <?php if ($col_user) { ?>
		                <td><?php echo $row->name;         ?></td><?php } ?>
		                <?php if ($col_duedate) { ?>
		                <td><?php echo $row->duedate;      ?></td><?php } ?>
		                <?php if ($col_status) { ?>
		                <td><?php echo $row->status;       ?></td><?php } ?>
		                <?php if ($col_assign) { ?>
		                <td><?php echo $row->supportuser;  ?></td><?php } ?>
		                <?php if ($col_date_created) { ?>
		                <td><?php echo $row->date;         ?></td><?php } ?>
		                <?php if ($col_message) { ?>
		                <td><?php echo $row->message;      ?></td><?php } ?>
		                <?php if ($col_last_message) { ?>
		                <td><?php echo $row->last_message; ?></td><?php } ?>
		            </tr><?php
		        } ?>
			    </tbody>
		    </table>
	    </div><?php

	    // Takes care of pagination
	    $pages = ceil($total / $limit);
		$start = (($page + 1) - 15) < 2 ? 1 : (($page + 1) - 15);
        $end = (($page + 1) + 15) <= $pages ? (($page + 1) + 15) : $pages;
        if ($pages > 1): ?>
	        <div class="pagination pagination-right">
		        <ul>
			        <li><a href="#" onclick="$jMaQma('#page').val(0);$jMaQma('#adminForm').submit();"><?php echo JText::_('table_fpage');?></a></li>
			        <?php for ($i = $start; $i <= $end; $i++): ?>
			            <li class="<?php echo ($i - 1) == $page ? 'active' : '';?>"><a href="#" onclick="$jMaQma('#page').val(<?php echo ($i - 1);?>);$jMaQma('#adminForm').submit();"><?php echo $i;?></a></li>
			        <?php endfor;?>
			        <li><a href="#" onclick="$jMaQma('#page').val(<?php echo ($pages - 1);?>);$jMaQma('#adminForm').submit();"><?php echo JText::_('table_lpage');?></a>
			        </li>
		        </ul>
	        </div><?php
        endif;
    }
}


function setWorkgroup()
{
    $mainframe = JFactory::getApplication();
    $is_support = HelpdeskUser::IsSupport();
    $Itemid = JRequest::getInt('Itemid', 0);
    $database = JFactory::getDBO();

    $id = intval(JRequest::getVar('id', 0, 'REQUEST', 'int'));
    $from = intval(JRequest::getVar('from', 0, 'REQUEST', 'int'));
    $to = intval(JRequest::getVar('to', 0, 'REQUEST', 'int'));

    if ($id && $from && $to && $is_support)
    {
        $database->setQuery("update #__support_ticket SET id_workgroup='" . $to . "' WHERE id='" . $id . "'");
        $database->query();

        $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $from . '&task=ticket_my&msg=' . sprintf(JText::_('wk_change_complete'), HelpdeskDepartment::GetName($to)), false);
        $mainframe->redirect($url);
    }
}


function QuickReply()
{
    global $supportOptions, $CONFIG, $is_manager, $usertype;

    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();

    $errors = 0;
    $ticket = intval(JRequest::getVar('ticket_reply_id', 0, 'REQUEST', 'int'));
    $summary = JRequest::getVar('summary', '', 'REQUEST', 'string');
    $message = JRequest::getVar('message', '', 'REQUEST', 'string', 2);

    // Get Ticket Details
    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket . "'");
    $ticket = $database->loadObject();

    // Get Ticket Users Details
    $database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->id_user . "'");
    $userinfo = $database->loadObject();

    // Get Ticket Users Client ID
    $client_id = HelpdeskClient::GetIDByUser($ticket->id_user);

    // Get Ticket Users Client Name
    $client_name = HelpdeskClient::GetName($ticket->id_user);

    // Get Assigned User
    $database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->assign_to . "'");
    $assigned = $database->loadObject();

    // Set Ticket URL Link
    $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);

    // Set Email Notify Template variables
    $var_set = array('[duedate]' => $ticket->duedate,
        '[duedate_old]' => '',
        '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
        '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
        '[number]' => $ticket->ticketmask,
        '[assign]' => HelpdeskUtility::String2HTML((isset($assigned) ? $assigned->name : '')),
        '[assign_email]' => (isset($assigned) ? $assigned->email : ''),
        '[unassigned]' => '',
        '[unassigned_email]' => '',
        '[subject]' => HelpdeskUtility::String2HTML($ticket->subject),
        '[message]' => HelpdeskUtility::String2HTML($ticket->message),
        '[summary]' => HelpdeskUtility::String2HTML(($summary)),
        '[author]' => HelpdeskUser::GetName($user->id),
        '[recipient]' => HelpdeskUtility::String2HTML($ticket->an_name),
        '[email]' => $ticket->an_mail,
        '[client]' => HelpdeskUtility::String2HTML($client_name),
        '[url]' => $ticket_url,
        '[department]' => HelpdeskUtility::String2HTML($workgroupSettings->wkdesc),
        '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($ticket->id_priority)),
        '[priority_old]' => '',
        '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($ticket->id_status)),
        '[status_old]' => '',
        '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($ticket->id_category)),
        '[category_old]' => '',
        '[source]' => HelpdeskUtility::String2HTML($ticket->source),
        '[helpdesk]' => JURI::root()
    );

    if ($message != "" || $summary != "") {
        // If it's from support and it's the first then update DATE_SUPPORT field
        $database->setQuery("SELECT COUNT(r.id) FROM #__support_ticket_resp r INNER JOIN #__support_permission p ON p.id_user=r.id_user WHERE r.id_ticket='" . $ticket->id . "'");
        if ($is_support && $database->loadResult() == 0) {
            $database->setQuery("UPDATE #__support_ticket SET date_support='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "' WHERE id='" . $ticket->id . "'");
            $database->query();
        }

        // Save Ticket Reply
        if (HelpdeskTicket::Reply($ticket->id, $summary, $message)) {
            // Notify Assigned User
            if (isset($assigned) && $assigned->id > 0 && $user->id != $assigned->id) {
                SendMailNotification($ticket->id, $var_set, $assigned->email, 'reply', null, null, 'tkt_notification_reply');
            }

            // Notify Customer User
            if ($supportConfig->notify_user && $user->id != $userinfo->id) {
                SendMailNotification($ticket->id, $var_set, $userinfo->email, 'reply', null, null, 'tkt_notification_customer_reply');
            }

            // Add ticket log message
            HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('posted_reply')), JText::_('posted_reply_customer'), $ticket->id_status . '', 0, 0, 'add_message.png');
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors = 1;
        }
    }

    if (!$errors) {
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('reply_saved_ok'), $ticket_url, $ticket->ticketmask), 'i');
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&filter_search=' . JRequest::getVar('filter_search', '', 'REQUEST', 'string') . '&filter_client=' . JRequest::getVar('filter_client', 0, 'REQUEST', 'int') . '&filter_user=' . JRequest::getVar('filter_user', 0, 'REQUEST', 'int'), false);
    $mainframe->redirect($url);
}


function setStatus()
{
    global $supportOptions, $CONFIG, $is_manager, $usertype;

    $Itemid = JRequest::getInt('Itemid', 0);
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
	$ids = JRequest::getVar('ids', '', '', 'string');
	$ticket = intval(JRequest::getVar('ticket', 0, 'REQUEST', 'int'));
	$status = intval(JRequest::getVar('status', 0, 'REQUEST', 'int'));
	$id_workgroup = intval(JRequest::getVar('$id_workgroup', 0, 'REQUEST', 'int'));
	$redirect = ($ids!='' ? true : false);

	if ($ids == '')
	{
		$ids = $ticket . ',';
	}

	$ids = JString::substr($ids,0,strlen($ids)-1);
	$ids = explode(',', $ids);

	for ($i=0; $i<count($ids); $i++)
	{
		$ticket = $ids[$i];

	    // Get Ticket Details
	    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket . "'");
	    $ticket = $database->loadObject();

	    if ($is_support || $ticket->id_user == $user->id)
	    {
	        // Get Ticket Status
	        $status_id_old = $ticket->id_status;
	        $status_id_new = $status;

	        // Get Ticket Users Client ID
	        $client_id = HelpdeskClient::GetIDByUser($ticket->id_user);

	        // Get Ticket Users Client Name
	        $client_name = HelpdeskClient::GetName($ticket->id_user);

	        // Get Assigned User
	        $database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->assign_to . "'");
	        $assigned = $database->loadObject();

	        // Set Ticket URL Link
	        $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);

	        // Set Email Notify Template variables
	        $var_set = array('[duedate]' => $ticket->duedate,
	            '[duedate_old]' => '',
	            '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
	            '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
	            '[number]' => $ticket->ticketmask,
	            '[assign]' => HelpdeskUtility::String2HTML((isset($assigned) ? $assigned->name : '')),
	            '[assign_email]' => isset($assigned) ? $assigned->email : '',
	            '[unassigned]' => '',
	            '[unassigned_email]' => '',
	            '[subject]' => HelpdeskUtility::String2HTML(($ticket->subject)),
	            '[message]' => HelpdeskUtility::String2HTML($ticket->message),
	            '[summary]' => '',
	            '[author]' => HelpdeskUser::GetName($user->id),
	            '[recipient]' => HelpdeskUtility::String2HTML($ticket->an_name),
	            '[email]' => $ticket->an_mail,
	            '[client]' => '',
	            '[url]' => $ticket_url,
	            '[department]' => HelpdeskUtility::String2HTML($workgroupSettings->wkdesc),
	            '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($ticket->id_priority)),
	            '[priority_old]' => '',
	            '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
	            '[status_old]' => $status_id_old ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_old)) . '</b>' : '',
	            '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($ticket->id_category)),
	            '[category_old]' => '',
	            '[source]' => HelpdeskUtility::String2HTML($ticket->source),
	            '[helpdesk]' => JURI::root()
	        );

	        if ($status_id_old != $status_id_new && ($is_support || $user->id == $ticket->id_user)) {
	            // Update Ticket Status
		        $database->setQuery("UPDATE #__support_ticket SET id_status='" . $status_id_new . "', last_update='" . HelpdeskDate::DateOffset() . "' WHERE id='" . $ticket->id . "'");
	            if ($database->query()) {
	                // Ticket log
	                $ticketLogMsg = JText::_('changed_status');
	                $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), $ticketLogMsg);
	                $ticketLogMsg = str_replace('%2', HelpdeskStatus::GetName($status_id_old), $ticketLogMsg);
	                $ticketLogMsg = str_replace('%3', HelpdeskStatus::GetName($status_id_new), $ticketLogMsg);
	                HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_status_hidden'), $status_id_new, 'status', $status_id_new, 0, 'change.png');
	                if ($ticket->id_user != $user->id) {
	                    //SendMailNotification( $ticket->id, $var_set, $ticket->an_mail, 'updated', null, null, 'tkt_notification_customer_reply' );
	                } elseif (isset($assigned) && $user->id != $ticket->assign_to) {
	                    //SendMailNotification( $ticket->id, $var_set, $assigned->email, 'updated', null, null, 'tkt_notification_reply' );
	                }
	            } else {
	                HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
	            }
	        }
	    }
	}

	if ($redirect)
	{
		$url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . JText::_('changed_status_hidden') . '&msgtype=i', false);
		$mainframe->redirect($url);
	}
	else
	{
		echo HelpdeskStatus::GetName($status_id_new) . '|' . JText::_('changed_status_hidden');
	}
}


function reportTickets()
{
    global $supportOptions, $CONFIG, $is_manager, $usertype;

    $Itemid = JRequest::getInt('Itemid', 0);
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $filter_client = JRequest::getInt('filter_client', 0);
    $filter_user = JRequest::getInt('filter_user', 0);

    // Set title
    HelpdeskUtility::PageTitle('showReportTickets');

    if (!get_cfg_var('safe_mode')) {
        $time_limit = ini_get('max_execution_time');
        set_time_limit(0);
    }

    if ($is_support && $usertype > 5) {
        if ((!isset($_GET["filter_workgroup"]) || ($_GET["filter_workgroup"] == '0')) && (!isset($_GET["filter_client"]) || ($_GET["filter_client"] == '0'))) {
            $filters = " 1 = 1 ";
        } else {
            $filters = "";
            if (isset($_GET["filter_workgroup"]) && $_GET["filter_workgroup"] != '0') {
                $filters .= " t.`id_workgroup` = '" . $_GET["filter_workgroup"] . "' AND ";
            } else {
                $filters .= " 2 = 2 AND ";
            }

            if (isset($_GET["filter_client"]) and $_GET["filter_client"] != '0') {
                $filters .= " t.`id_client` = '" . $_GET["filter_client"] . "' ";
            } else {
                $filters .= " 3 = 3 ";
            }
        }
        $condition = "$filters AND ( t.`assign_to` = '" . $user->id . "' OR t.`id_workgroup` = '" . $id_workgroup . "' )";
    } else if ($is_client && $is_manager) {
        $_client = " (SELECT cu.id_client FROM #__support_client_users cu WHERE cu.id_user = '" . $user->id . "' )";
        $_client_users = " (SELECT cu.id_user FROM #__support_client_users cu WHERE cu.id_client = " . $_client . " ) ";
        $condition = " ( t.`id_user` IN (" . $_client_users . ") AND  t.`id_workgroup` = '" . $id_workgroup . "' ) ";
    }

    $sql_client_users_list = "SELECT DISTINCT(u.`id_user`) AS clients FROM #__support_client_users u";
    $sql_support_users_list = "SELECT DISTINCT(su.`id_user`) AS supporters FROM #__support_users su";

    $sql = "SELECT 
				t.id AS ticketid,
				t.ticketmask AS ticketmask, 
				t.subject AS subject,
				( SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(log.`date_time`)) FROM `#__support_log` log WHERE log.id_ticket = ticketid AND log.time_elapse = 0 AND log.`field` = 'status' ORDER BY log.date_time DESC LIMIT 1 ) AS last_log_time,
				( SELECT s.ticket_side FROM `#__support_log` log, #__support_status s  WHERE log.id_status = s.id AND log.id_ticket = ticketid AND log.time_elapse = 0 AND log.`field` = 'status' ORDER BY log.date_time DESC LIMIT 1 ) AS last_log_who,
				( SELECT SUM(log.time_elapse) FROM #__support_log log, #__support_status s WHERE log.id_status = s.id AND log.id_ticket = ticketid AND log.field = 'status' AND s.ticket_side = 1) AS support_time,
				(SELECT SUM(log.time_elapse) FROM #__support_log log, #__support_status s WHERE log.id_status = s.id AND log.id_ticket = ticketid AND log.field = 'status' AND s.ticket_side = 2) AS customer_time,
				s.description AS current_status,
				ju.name AS customer_user,
				ju2.name AS support_user,
				t.duedate AS due_date,
				TIMESTAMPDIFF(SECOND,t.duedate,NOW()) AS overdue_time,	
				log.date_time AS create_date,
				s.description AS description, 
				u.id_user AS user,
				log.id_status AS status, 
				t.last_update AS last_update,
				log.value AS value,
				c.clientname AS clientname
			FROM #__support_ticket t
				 INNER JOIN #__support_log log ON log.id_ticket = t.id
				 LEFT JOIN #__support_status s ON t.id_status = s.id
				 LEFT JOIN #__support_client_users u ON t.id_user = u.id_user
				 LEFT JOIN #__support_client c ON c.id = u.id_client
				 INNER JOIN #__users ju ON ju.id = t.id_user 
				 INNER JOIN #__users ju2 ON ju2.id = t.assign_to
			WHERE $condition 
			  AND log.`field` = 'status' 
			  AND s.status_group = 'O'
			GROUP BY t.id, t.id_status";
    $database->setQuery($sql);
    $tickets = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';

    $i = 1;

    foreach ($tickets as $key2 => $value2)
    {
        if (is_object($value2))
        {
            foreach ($value2 as $key3 => $value3)
            {
                $tickets_rows[$i][$key3] = $value3;
                if ($key3 == 'ticketid')
                {
                    $tickets_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $value3, false);
                }
            }
        }

        if ($tickets_rows[$i]['support_time'] == '') $tickets_rows[$i]['support_time'] = 0;
        if ($tickets_rows[$i]['customer_time'] == '') $tickets_rows[$i]['customer_time'] = 0;

        // alternativa: calculo retirado da query para n�o ficar muito pessado
        if ($tickets_rows[$i]['last_log_who'] == 1)
        {
            $tickets_rows[$i]['support_time'] += $tickets_rows[$i]['last_log_time'];
        } else {
            $tickets_rows[$i]['customer_time'] += $tickets_rows[$i]['last_log_time'];
        }

        // alternativa: calculo retirado da query para n�o ficar muito pessado
        $tickets_rows[$i]['total_time'] = $tickets_rows[$i]['customer_time'] + $tickets_rows[$i]['support_time'];

        // alternativa: calculo retirado da query para n�o ficar muito pessado
        $tickets_rows[$i]['support_time_percentage'] = number_format($tickets_rows[$i]['support_time'] / $tickets_rows[$i]['total_time'] * 100, 1, '.', '');
        $tickets_rows[$i]['customer_time_percentage'] = number_format($tickets_rows[$i]['customer_time'] / $tickets_rows[$i]['total_time'] * 100, 1, '.', '');

        // formatacao de valores (para permitir os calculos entre os mesmos, os valores de tempo da base de dados s�o retitados como timstamp ou unixtime
        $tickets_rows[$i]['customer_time2'] = HelpdeskDate::SecondsToHours($tickets_rows[$i]['customer_time'], true);
        $tickets_rows[$i]['support_time2'] = HelpdeskDate::SecondsToHours($tickets_rows[$i]['support_time'], true);
        $tickets_rows[$i]['total_time2'] = HelpdeskDate::SecondsToHours($tickets_rows[$i]['total_time'], true);

        $support_perc = round($tickets_rows[$i]['support_time_percentage']);
        $customer_perc = round($tickets_rows[$i]['customer_time_percentage']);
        $customer_name = $tickets_rows[$i]['customer_user'];
        $support_name = $tickets_rows[$i]['support_user'];
        $customer_value = $tickets_rows[$i]['customer_time2'];
        $support_value = $tickets_rows[$i]['support_time2'];
        $customer_width = round((100 * $customer_perc) / 100);
        $support_width = round((100 * $support_perc) / 100);
        $tickets_rows[$i]['time_ratio'] = '<img src="components/com_maqmahelpdesk/images/bar-customer.png" width="' . $customer_width . '" style="height:10px;" alt="' . $customer_name . ' (' . $customer_value . ')" title="' . $customer_name . ' (' . $customer_value . ')" /><img src="components/com_maqmahelpdesk/images/bar-support.png" width="' . $support_width . '" style="height:10px;" alt="' . $support_name . ' (' . $support_value . ')" title="' . $support_name . ' (' . $support_value . ')" />';

        if ($tickets_rows[$i]['overdue_time'] > 0) {
            $tickets_rows[$i]['overdue'] = " style='color: red;' ";
        } else {
            $tickets_rows[$i]['overdue'] = " style='color: green;' ";
        }

        $i++;
    }

    if (!get_cfg_var('safe_mode')) {
        set_time_limit($time_limit);
    }

    $imgpath = JURI::root() . 'components/com_maqmahelpdesk/images';

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('reports/ticket_reports');
    include $tmplfile;
}


function showMyTickets()
{
    global $supportOptions, $CONFIG, $is_manager, $usertype;

    $session = JFactory::getSession();
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $editor = JFactory::getEditor();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $format = JRequest::getVar('format', '', '', 'str');
    $page = JRequest::getVar('page', 0, '', 'int');
    $limit = JRequest::getVar('limit', 25, '', 'int');
    $orderby = JRequest::getVar('orderby', 't.last_update', '', 'str');
    $order = JRequest::getVar('order', 'DESC', '', 'str');
    $limitstart = ($page * $limit);
	$query = '';

    if ($format != 'raw')
    {
        HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
        HelpdeskUtility::AppendResource('highlight.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
    }

    // Set title
    HelpdeskUtility::PageTitle('showTicketsManager');

    // Views manager
    // STATUS GROUP
    $status_groups[] = JHTML::_('select.option', '', JText::_('selectlist'));
    $status_groups[] = JHTML::_('select.option', 'O', JText::_('open'));
    $status_groups[] = JHTML::_('select.option', 'C', JText::_('closed'));
    $lists['views_status_group'] = JHTML::_('select.genericlist', $status_groups, 'value[]', '', 'value', 'text', '');
    // STATUS
    $sql = "SELECT `id` AS value, `description` AS text 
			FROM #__support_status 
			ORDER BY status_group";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $lists['views_status'] = JHTML::_('select.genericlist', $rows, 'value[]', '', 'value', 'text', '');
    // ASSIGNMENT
    $assignlist[] = JHTML::_('select.option', '0', JText::_('select_assign'));
    switch ($usertype)
    {
        case 7 : // Support manager, can view own, unassigned tickets and other support queues
            // Get the list of other support staff for the assignment selection list
            $sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text
					FROM #__support_permission as p, #__users as u 
					WHERE p.id_user=u.id
					ORDER BY u.name";
            $database->setQuery($sql);
            $rows = $database->loadObjectList();
            $rows = array_merge(array(JHTML::_('select.option', 0, JText::_('ticket_unassigned_status'))), $rows);
            $lists['views_assign'] = JHTML::_('select.genericlist', $rows, 'value[]', '', 'value', 'text', '');
            break;
        case 6 : // Support team leader, can view own, unassigned tickets
            $assignlist[] = JHTML::_('select.option', $user->id, $user->name);
            $assignlist[] = JHTML::_('select.option', 0, JText::_('ticket_unassigned_status'));
            $lists['views_assign'] = JHTML::_('select.genericlist', $assignlist, 'value[]', '', 'value', 'text', '');
            break;
        case 5 : // Basic support user, can only view own tickets
            $assignlist[] = JHTML::_('select.option', $user->id, $user->name);
            $lists['views_assign'] = JHTML::_('select.genericlist', $assignlist, 'value[]', '', 'value', 'text', '');
            break;
    }
    // CATEGORY
    $lists['views_category'] = HelpdeskForm::BuildCategories(0, true, false, false, false);
	$lists['views_category'] = str_replace('filter_category', 'value[]', $lists['views_category']);
    // WORKGROUP
    $wkids = '';
    $sql = "SELECT p.`id_workgroup` AS value, w.`wkdesc` AS text
			FROM `#__support_permission` AS p
				 INNER JOIN `#__support_workgroup` AS w ON w.`id`=p.`id_workgroup`
			WHERE p.`id_user`=" . (int)$user->id;
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    $lists['views_workgroup'] = JHTML::_('select.genericlist', $rows, 'value[]', '', 'value', 'text', '');

	// Get filters
	$ac_me = JRequest::getVar('ac_me', $session->get('ac_me', '', 'maqmahelpdesk'));
	$filter_overdued = JRequest::getInt('filter_overdued', $session->get('filter_overdued', 0, 'maqmahelpdesk'));
	$filter_today = JRequest::getInt('filter_today', $session->get('filter_today', 0, 'maqmahelpdesk'));
	$filter_opened = JRequest::getInt('filter_opened', $session->get('filter_opened', 0, 'maqmahelpdesk'));
	$filter_client = JRequest::getInt('filter_client', $session->get('filter_client', 0, 'maqmahelpdesk'));
	$filter_user = JRequest::getInt('filter_user', $session->get('filter_user', 0, 'maqmahelpdesk'));
	$filter_search = JRequest::getVar('filter_search', $session->get('filter_search', '', 'maqmahelpdesk'));
	$filter_category = JRequest::getInt('filter_category', $session->get('filter_category', 0, 'maqmahelpdesk'));
	$filter_status = JRequest::getVar('filter_status', $session->get('filter_status', 'WIP', 'maqmahelpdesk'));

	// Set filters session
	$session->set('ac_me', $ac_me, 'maqmahelpdesk');
	$session->set('filter_overdued', $filter_overdued, 'maqmahelpdesk');
	$session->set('filter_today', $filter_today, 'maqmahelpdesk');
	$session->set('filter_opened', $filter_opened, 'maqmahelpdesk');
	$session->set('filter_client', $filter_client, 'maqmahelpdesk');
	$session->set('filter_user', $filter_user, 'maqmahelpdesk');
	$session->set('filter_search', $filter_search, 'maqmahelpdesk');
	$session->set('filter_category', $filter_category, 'maqmahelpdesk');
	$session->set('filter_status', $filter_status, 'maqmahelpdesk');

    // Get view
    $ticket_view = JRequest::getInt('tview', 0);
    if (!$ticket_view)
    {
        $ticket_view = $session->get('tview', 0, 'maqmahelpdesk');
    }
    else
    {
        $session->set('tview', $ticket_view, 'maqmahelpdesk');
	    $filter_today = 0;
	    $filter_opened = 0;
	    $filter_overdued = 0;
	    $session->set('filter_today', $filter_today, 'maqmahelpdesk');
	    $session->set('filter_opened', $filter_opened, 'maqmahelpdesk');
	    $session->set('filter_overdued', $filter_overdued, 'maqmahelpdesk');
    }
    if ($filter_overdued || $filter_today || $filter_opened) {
        $ticket_view = 0;
        $session->set('tview', $ticket_view, 'maqmahelpdesk');
    }

    // VIEWS ----------------------------------------------------------------------------------------
    if ($is_support && $ticket_view > -1) {
        // If there isn't a default yet get it
        $tview_default = $session->get('tview_default', 0, 'maqmahelpdesk');
        if (!$tview_default) {
            if ($supportConfig->common_ticket_views) {
                $sql = "SELECT `id` 
						FROM `#__support_views`
						WHERE `default` = 1 AND `id_user` = 0";
            } else {
                $sql = "SELECT `id` 
						FROM `#__support_views`
						WHERE `default` = 1 AND `id_user` = " . (int)$user->id;
            }
            $database->setQuery($sql);
            $tview_default = $database->loadResult();
            $session->set('tview_default', $tview_default, 'maqmahelpdesk');
        }

        // If no view selected set thed default
        if (!$ticket_view) {
            $ticket_view = $tview_default;
            $session->set('tview', $tview_default, 'maqmahelpdesk');
        }

        // Get the parameters from the View
        $sql = "SELECT `name`, `viewtype`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `ordering`, `orderby`
				FROM `#__support_views`
				WHERE `id` = " . (int)$ticket_view;
        $database->setQuery($sql);
        $tview = $database->loadObject();

        // Build the query
        $have_category = 0;
        if ($tview != null) {
            $operators = explode('|', $tview->operator);
            $fields = explode('|', $tview->field);
            $arithmetics = explode('|', $tview->arithmetic);
            $values = explode('|', $tview->value);

            for ($i = 0; $i < count($operators); $i++) {
                $query .= ' ' . $operators[$i] . ' ';

                // Check if it's category field for possible restrictions
                if ($fields[$i] == 't.id_category') {
                    $have_category = 1;
                }

                // Open brackets if this is OR and there's no previous or if the previous is AND
                if ($operators[$i] == 'AND' && (isset($operators[$i + 1]) && $operators[$i + 1] == 'OR')) {
                    $query .= '(';
                }

                $query .= $fields[$i] . ' ';
                $query .= $arithmetics[$i] . ' ';
                // Check if it's not numeric to place between '
                // Check it arithmetic is LIKE to place %
                $query .= (!is_numeric($values[$i]) ? "'" : "");
                $query .= ($arithmetics[$i] == 'LIKE' ? "%" : "");
                $query .= $values[$i];
                $query .= ($arithmetics[$i] == 'LIKE' ? "%" : "");
                $query .= (!is_numeric($values[$i]) ? "'" : "");

                // Close brackets if previous is OR and there's no next or if the next is AND
                if ($operators[$i] == 'OR' && (!isset($operators[$i + 1]) || (isset($operators[$i + 1]) && $operators[$i + 1] == 'AND'))) {
                    $query .= ')';
                }
            }
        }

        // Check if agent have category restrictions
        $categories = '';
        if ($have_category) {
            $sql = "SELECT `id_category`
					FROM `#__support_permission_category`
					WHERE `id_workgroup`=$id_workgroup AND `id_user`=" . $user->id;
            $database->setQuery($sql);
            $categories = $database->loadObjectList();
            $catpermissions = null;
            foreach ($categories as $rowcat) {
                $catpermissions[] = $rowcat->id_category;
            }
            $categories = (count($catpermissions) ? ' AND c.id IN (' . implode(',', $catpermissions) . ')' : '');
        }

        // Set the ordering to the default in case the user didn't changed 
        $orderby = $orderby;
        $orderbysql = 't.`sticky` DESC, ' . ($orderby == '' ? $tview->ordering : $orderby);
        $order = ($order == '' ? $tview->orderby : $order);
    } else {
        $orderby = ($orderby == '' ? '' : $orderby);
        $orderbysql = $orderby;
    }

    // FILTER OPTIONS ----------------------------------------------------------------------------------------
    // VIEWS
    $sql = "SELECT `id` AS value, `name` AS text 
			FROM `#__support_views` 
			WHERE `id_user` = " . ($supportConfig->common_ticket_views ? 0 : (int)$user->id) . "
			ORDER BY `name`";
    $database->setQuery($sql);
    $views = $database->loadObjectList();
    $views = array_merge(array(JHTML::_('select.option', '-1', JText::_('all'))), $views);
    $lists['tview'] = JHTML::_('select.genericlist', $views, 'tview', '', 'value', 'text', $ticket_view);
    // STATUS
    $clause = !$is_support ? "WHERE user_access = '1'" : "";
    $sql = "SELECT `id` AS value, `description` AS text 
			FROM #__support_status 
			$clause
			ORDER BY `ordering`, `description`";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $rows_wk = array_merge(array(JHTML::_('select.option', 'WIP', JText::_('wip_status'))), $rows_wk);
    $rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all'))), $rows_wk);
    $lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'filter_status', '', 'value', 'text', $filter_status);
    // USERS FOR CLIENT MANAGERS
    if ($usertype == 2) {
        $sql = "SELECT t1.`id` AS value, t1.`name` AS text 
				FROM #__users as t1 
				WHERE t1.id IN (SELECT id_user FROM #__support_client_users WHERE id_client='" . $is_client . "') 
				ORDER BY t1.`name`";
        $database->setQuery($sql);
        $rows_users = $database->loadObjectList();
        $rows_users = array_merge(array(JHTML::_('select.option', '0', JText::_('select_user'))), $rows_users);
        $lists['users'] = JHTML::_('select.genericlist', $rows_users, 'filter_user', '', 'value', 'text', $filter_user);
    }
    // ASSIGNMENT
    $filter_assign_sql = '';
    if ($is_support) {
        $assignlist[] = JHTML::_('select.option', '0', JText::_('select_assign'));
        switch ($usertype) {
            case 7 : // Support manager, can view own, unassigned tickets and other support queues
                // Get the list of other support staff for the assignment selection list
                $sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text, p.level
              			FROM #__support_permission as p, #__users as u 
              			WHERE p.id_user=u.id AND u.id<>" . (int)$user->id . " 
              			ORDER BY p.level, u.name";
                $database->setQuery($sql);
                $rows_staff = $database->loadObjectList();
                for ($staff = 0; $staff < count($rows_staff); $staff++) {
                    $row_staff = $rows_staff[$staff];
                    $assignlist[] = JHTML::_('select.option', $row_staff->value, $row_staff->text . ($row_staff->level ? ' - ' . sprintf(JText::_('SUPPORT_LEVEL_LABEL'), $row_staff->level) : ''));
                }
                break;
            case 6 : // Support team leader, can view own, unassigned tickets
                $filter_assign_sql = ' t.`assign_to`=0 AND AND t.`assign_to`=' . (int)$user->id;
                break;
            case 5 : // Basic support user, can only view own tickets
                $filter_assign_sql = ' AND t.`assign_to`=' . (int)$user->id;
                break;
        }
    }
    // CATEGORY
    $lists['category'] = HelpdeskForm::BuildCategories($filter_category, true, false, false, false);
    // PRIORITY
    // SUPPORT USER WORKGROUPS RESTRICTION
    $filter_workgroup_sql = '';
    if ($supportConfig->support_workgroup_only && $is_support) {
        $wkids = '';
        $sql = "SELECT `id_workgroup` 
				FROM `#__support_permission` 
				WHERE `id_user`=" . (int)$user->id;
        $database->setQuery($sql);
        $support_user_workgroups = $database->loadObjectList();
        for ($w = 0; $w < count($support_user_workgroups); $w++) {
            $wkids .= $support_user_workgroups[$w]->id_workgroup . ',';
        }
        $wkids = JString::substr($wkids, 0, strlen($wkids) - 1);
        $filter_workgroup_sql = ' AND t.`id_workgroup` IN (' . $wkids . ')';
    }

    // FILTERS SQL ----------------------------------------------------------------------------------------
    // Free search
    $filter_search_sql = '';
    if ($filter_search != '')
    {
        if ($supportConfig->customfields_search)
        {
            $filter_search_sql = " AND (t.duedate like '%" . $database->escape($filter_search) . "%' OR t.ticketmask like '%" . $database->escape($filter_search) . "%' OR t.subject like '%" . $database->escape($filter_search) . "%' OR t.message LIKE '%" . $database->escape($filter_search) . "%' OR t.id IN (SELECT tr.id_ticket FROM #__support_ticket_resp AS tr WHERE tr.message LIKE '%" . $database->escape($filter_search) . "%' OR tr.reply_summary LIKE '%" . $database->escape($filter_search) . "%') OR f.newfield LIKE '%" . $database->escape($filter_search) . "%')";
        }
        else
        {
            $filter_search_sql = " AND (t.duedate like '%" . $database->escape($filter_search) . "%' OR t.ticketmask like '%" . $database->escape($filter_search) . "%' OR t.subject like '%" . $database->escape($filter_search) . "%' OR t.message LIKE '%" . $database->escape($filter_search) . "%')";
        }
    }
    // Status
    $filter_status_sql = '';
    if ($filter_status != '' && $filter_status != 'WIP') {
        $filter_status_sql = " AND t.id_status='" . $filter_status . "'";
    } elseif ($filter_status == 'WIP' && !$is_support) {
        $filter_status_sql = " AND s.status_group='O'";
    }
    // Category
    $filter_category_sql = '';
    if ($filter_category != '')
    {
        $filter_category_sql = " AND t.id_category='" . $filter_category . "'";
    }
    // Client - If the user belongs to a client filter automatically
    $filter_client_sql = '';
    if ($is_client)
    {
        $filter_client_sql = ' AND t.`id_client`=' . (int) $is_client;
    }
    // User - If it's a regular user filter automatically
    $filter_user_sql = '';
    if ($usertype == 1)
    {
        $filter_user_sql = ' AND t.`id_user`=' . (int) $user->id;
    }
    elseif ($filter_user /*|| $ac_me == ''*/)
    {
	    $filter_user_sql = ' AND t.`id_user`=' . (int) $filter_user;
    }
    /*elseif ($ac_me != '')
    {
	    $filter_user_sql = " AND (t.an_name LIKE '%" . ($ac_me) . "%' OR u1.username LIKE '%" . ($ac_me) . "%' OR t.an_mail LIKE '%" . ($ac_me) . "%')";
    }*/
    // Overdued
    $filter_overdued_sql = '';
    if ($filter_overdued)
    {
        $query = ''; // Don't make use of the view
        $filter_overdued_sql = " AND t.duedate <= '" . HelpdeskDate::DateOffset() . "'";
    }
    // Today
    $filter_today_sql = '';
    if ($filter_today)
    {
        $query = ''; // Don't make use of the view
        $filter_today_sql = " AND MONTH(t.date) = '" . HelpdeskDate::DateOffset("%m") . "'";
        $filter_today_sql.= " AND DAY(t.date) = '" . HelpdeskDate::DateOffset("%d") . "'";
        $filter_today_sql.= " AND YEAR(t.date) = '" . HelpdeskDate::DateOffset("%Y") . "'";
    }
    // Opened
    $filter_opened_sql = '';
    if ($filter_opened)
    {
        $query = ''; // Don't make use of the view
        $filter_opened_sql = " AND s.`status_group` = 'O'";
    }
    // Support user workgroup filter
    $filter_wks_permission_sql = '';
    if ($is_support)
    {
	    if ($session->get('wkids_notsupport', '', 'maqmahelpdesk') == '')
	    {
		    $filter_wks_permission_sql = ' AND t.`id_workgroup` IN (' . $session->get('wkids', '', 'maqmahelpdesk') . ')';
	    }
	    else
	    {
		    $filter_wks_permission_sql = ' AND t.`id_workgroup`=' . (int) $id_workgroup;
	    }
    }
	if ($supportConfig->tickets_per_department)
	{
		$filter_wks_permission_sql = ' AND t.`id_workgroup`=' . (int) $id_workgroup;
	}
	// Internal tickets filter
	$filter_internal_sql = '';
	if (!$is_support)
	{
		$filter_internal_sql = " AND t.`internal`=0";
	}

    // Tickets SQL 
    $sql = "SELECT
			DISTINCT t.id AS dbid,
			CASE
				WHEN s.status_group = 'C'
				THEN '3'
				WHEN t.assign_to =0
				THEN '0'
				WHEN t.assign_to > 1
				AND t.duedate < '" . HelpdeskDate::DateOffset() . "'
				THEN '1'
				WHEN t.assign_to > 1
				AND t.duedate >= '" . HelpdeskDate::DateOffset() . "'
				THEN '2'
			END AS iconfield,
			t.ticketmask AS ticketid,
			t.subject,
			t.id_status,
			t.assign_to,
			t.id_priority,
			t.date,
			t.duedate,
			t.id_user,
			t.last_update,
			t.id_workgroup,
			t.message,
			w.wkdesc as workgroup,
			s.description as status,
			u1.name as user,
			u2.name as assigned,
			p.description as priority,
			cy.name as category,
			c.clientname as client,
			t.id as key1,
			s.id as key2,
			p.id as key3,
			w.id as key4,
			cy.id as key5,
			t.an_name,
			t.an_mail,
			t.`date_support`,
			t.`sticky`,
			s.`status_group`,
			s.`color`,
			t.`queue`,
			c.`approval`,
			t.`approved`,
			w.`logo`,
			t.`internal`
		FROM #__support_ticket AS t
			INNER JOIN #__support_status AS s ON t.id_status=s.id
			INNER JOIN #__support_priority AS p ON t.id_priority=p.id
			INNER JOIN #__support_workgroup AS w ON t.id_workgroup=w.id
			LEFT JOIN #__users AS u1 ON t.id_user=u1.id
			LEFT JOIN #__users AS u2 ON t.assign_to=u2.id
			LEFT JOIN #__support_client_users AS cu ON t.id_user=cu.id_user AND cu.id_client=t.id_client
			LEFT JOIN #__support_client AS c ON c.id=cu.id_client AND c.id=t.id_client
			LEFT JOIN #__support_category AS cy ON t.id_category=cy.id " .
        ($supportConfig->customfields_search ? "LEFT JOIN #__support_field_value AS f ON f.id_ticket=t.id" : "") . "
		WHERE 1=1 ";
    $sql .= $filter_search_sql . $filter_category_sql . $filter_client_sql . $filter_user_sql . $filter_status_sql . $query . $filter_overdued_sql . $filter_today_sql . $filter_opened_sql . $filter_wks_permission_sql . $filter_internal_sql;
    $sql .= " ORDER BY " . $orderbysql . " " . $order;
    $sql .= " LIMIT " . $limitstart . ", " . $limit;
    $database->setQuery($sql);
    $tickets = $database->loadObjectList();

    // Tickets Total SQL 
    $sql = "SELECT COUNT(*)
			FROM #__support_ticket AS t
				INNER JOIN #__support_status AS s ON t.id_status=s.id
				INNER JOIN #__support_priority AS p ON t.id_priority=p.id
				INNER JOIN #__support_workgroup AS w ON t.id_workgroup=w.id
				LEFT JOIN #__users AS u1 ON t.id_user=u1.id
				LEFT JOIN #__users AS u2 ON t.assign_to=u2.id
				LEFT JOIN #__support_client_users AS cu ON t.id_user=cu.id_user AND cu.id_client=t.id_client
				LEFT JOIN #__support_client AS c ON c.id=cu.id_client AND c.id=t.id_client
				LEFT JOIN #__support_category AS cy ON t.id_category=cy.id
			WHERE 1=1 ";
    $sql .= $filter_search_sql . $filter_category_sql . $filter_client_sql . $filter_user_sql . $filter_status_sql . $query . $filter_overdued_sql . $filter_today_sql . $filter_opened_sql . $filter_wks_permission_sql . $filter_internal_sql;
    $database->setQuery($sql);
    $total = $database->loadResult();

    // Ordering & Pagination
    $lorder_ticketid = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=t.ticketmask&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_subject = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=t.subject&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_user = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=t.an_name&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_updated = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=t.last_update&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_duedate = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=t.duedate&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_status = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=s.description&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $lorder_workgroup = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=w.wkdesc&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
    $plink = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&tview=' . $ticket_view . '&orderby=' . str_replace(',', '', $orderby) . '&order=' . $order;
    $pages = ceil($total / $limit);

    // No views warning
    if ($is_support && !count($views))
    {
        HelpdeskUtility::ShowSCMessage(JText::_('WARNING_NO_VIEWS'), 'e', JText::_('WARNING'));
    }

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('tickets/ticket_manager_' . ($is_support ? 'support' : 'customer'));
    include $tmplfile;
}


function viewTicket($id, $print)
{
    global $supportOptions, $usertype, $is_manager;

	$session = JFactory::getSession();
    $database = JFactory::getDBO();
    $document = JFactory::getDocument();
	$uri = JURI::getInstance();
    $editor = JFactory::getEditor();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $format = JRequest::getVar('format', '', '', 'string');
    $id = JRequest::getVar('id', 0, '', 'int');
	$orderby = JRequest::getVar('orderby', 't.last_update', '', 'str');
	$order = JRequest::getVar('order', 'DESC', '', 'str');

	// Get ticket details
	$sql = "SELECT t.id, t.id_workgroup, t.id_status, t.id_user, t.id_category, t.date, t.subject, t.message, t.last_update, t.assign_to,
				   t.id_priority, t.id_kb, t.source, t.ticketmask, t.an_name, t.an_mail, t.duedate, t.id_export, t.date_support,
				   t.day_week, t.id_client, t.ipaddress, u.phone, u.fax, u.mobile, u.address1, u.address2, u.zipcode, u.location, u.city,
				   u.country, u.avatar, t.id_directory, t.`approved`, t.`internal`
			FROM #__support_ticket AS t
				 LEFT JOIN #__support_users AS u ON u.id_user=t.id_user
			WHERE t.id=" . (int) $id;
	$database->setQuery($sql);
	$row = null;
	$row = $database->loadObject();
	!$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';

    if ($format != 'raw')
    {
        $document->addScriptDeclaration( 'var MQM_IS_ANONYMOUS = false;' );
        $document->addScriptDeclaration( 'var MQM_USERNAME_EXISTS = "'.addslashes(JText::_('USERNAME_EXISTS')).'";' );
        $document->addScriptDeclaration( 'var MQM_USER_MAIL_EXISTS = "'.addslashes(JText::_('USER_MAIL_EXISTS')).'";' );
        $document->addScriptDeclaration( 'var MQM_LOADING = "'.addslashes(JText::_('loading')).'";' );
        $document->addScriptDeclaration( 'var MQM_SAVE = "'.addslashes(JText::_('save')).'";' );
        $document->addScriptDeclaration( 'var MQM_MSG01 = "'.addslashes(JText::_('tmpl_msg01')).'";' );
        $document->addScriptDeclaration( 'var MQM_NAME = "'.addslashes(JText::_('name_required')).'";' );
        $document->addScriptDeclaration( 'var MQM_EMAIL = "'.addslashes(JText::_('email_required')).'";' );
        $document->addScriptDeclaration( 'var MQM_CATEGORY = "'.addslashes(JText::_('category_required')).'";' );
        $document->addScriptDeclaration( 'var MQM_MSG02 = "'.addslashes(JText::_('tmpl_msg02')).'";' );
        $document->addScriptDeclaration( 'var MQM_MSG04 = "'.addslashes(JText::_('tmpl_msg04')).'";' );
        $document->addScriptDeclaration( 'var MQM_MSG05 = "'.addslashes(JText::_('tmpl_msg05')).'";' );
        $document->addScriptDeclaration( 'var MQM_MSG06 = "'.addslashes(JText::_('tmpl_msg06')).'";' );
        $document->addScriptDeclaration( 'var MQM_CANCEL = "'.addslashes(JText::_('tmpl_ticket_cancelquestion')).'";' );
        $document->addScriptDeclaration( 'var MQM_CANCEL_LINK = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&order=' . $order . '&orderby=' . $orderby, false).'";' );
        $document->addScriptDeclaration( 'var MQM_INV_MONTH = "'.addslashes(JText::_('invalid_month')).'";' );
        $document->addScriptDeclaration( 'var MQM_YEAR1 = "'.(HelpdeskDate::DateOffset("%Y") + 1).'";' );
        $document->addScriptDeclaration( 'var MQM_INV_YEAR = "'.addslashes(JText::_('invalid_year')).'";' );
        $document->addScriptDeclaration( 'var MQM_INV_DAY = "'.addslashes(JText::_('invalid_day')).'";' );
        $document->addScriptDeclaration( 'var MQM_INV_MINUTES = "'.addslashes(JText::_('invalid_minutes')).'";' );
        $document->addScriptDeclaration( 'var MQM_INV_HOURS = "'.addslashes(JText::_('invalid_hours')).'";' );
        $document->addScriptDeclaration( 'var MQM_LABOUR_NEGATIVE = "'.addslashes(JText::_('labour_negative')).'";' );
        $document->addScriptDeclaration( 'var MQM_WK_TO_WK = "'.addslashes(JText::_('from_wk_to_wk')).'";' );
        $document->addScriptDeclaration( 'var MQM_DELETE = "'.addslashes(JText::_('delete_ticket_confirm')).'";' );
        $document->addScriptDeclaration( 'var MQM_ASREPLY_URL = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ajax_asreply&format=raw', false) . '";' );
        $document->addScriptDeclaration( 'var MQM_PARENT_URL = "'.JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=ticket_parent&format=raw", false) . '";' );
        $document->addScriptDeclaration( 'var MQM_DELETE_URL = "'.JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=ticket_delete&id=" . $id, false) . '";' );
        $document->addScriptDeclaration( 'var MQM_STATUS_GROUP = "'.HelpdeskTicket::GetStatusGroup($row->id_status).'";' );
        $document->addScriptDeclaration( 'var MQM_SCREENR_RECORDER_TITLE = "'.addslashes(JText::_('SCREENR_RECORDER_TITLE')).'";' );
        $document->addScriptDeclaration( 'var MQM_SCREENR_MSG_RECORDING = "'.addslashes(JText::_('SCREENR_MSG_RECORDING')).'";' );
        $document->addScriptDeclaration( 'var MQM_SCREENR_MSG_COMPLETE = "'.addslashes(JText::_('SCREENR_MSG_COMPLETE')).'";' );
        $document->addScriptDeclaration( 'var MQM_USER_EMAIL = "'.addslashes($user->email).'";' );
        $document->addScriptDeclaration( 'var MQM_USER_NAME = "'.addslashes($user->name).'";' );
        $document->addScriptDeclaration( 'var MQM_SCREENR_ACCOUNT = "'.$supportConfig->screenr_account.'";' );
        $document->addScriptDeclaration( 'var MQM_SCREENR_API = "'.$supportConfig->screenr_api_id.'";' );
        if ($is_support) {
	        if ($supportConfig->editor == 'builtin')
	        {
		        $document->addScriptDeclaration('function CheckHTMLEditor() { return true; }');
	        }
	        else
	        {
		        $document->addScriptDeclaration('function CheckHTMLEditor() { ' . $editor->save('reply') . ' }');
	        }
            HelpdeskUtility::AppendResource('helpdesk.tickets.edit.support.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
        }else{
            if ($supportConfig->screenr_account!='' && $supportConfig->screenr_api_id!='') {
                $document->addScript($uri->getScheme() . "://imaqma.viewscreencasts.com/api/recorder");
            }
            HelpdeskUtility::AppendResource('helpdesk.tickets.edit.customer.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
        }
        HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
        HelpdeskUtility::AppendResource('rating.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
        HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
		HelpdeskUtility::AppendResource('highlight.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
    }

    // If it's the print version shows icon to print and to close
    if ($print) {
        $img_src = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/';
        echo '<style type="text/css" media="print">';
        echo '.exclude {';
        echo '	visibility: hidden;';
        echo '  display: none;';
        echo '}';
        echo '</style>';
        echo '<div align="right" class="exclude">';
        echo '<img src="' . $img_src . '16px/print.png" border="0" onClick="javascript:window.print();" style="cursor: pointer;" title="' . JText::_('print') . '">';
        echo '&nbsp;';
        echo '<img src="' . $img_src . '16px/close.png" border="0" onClick="javascript:window.close();" style="cursor: pointer;" title="' . JText::_('close') . '">';
        echo '</div>';
    }

    // Workgroups
	$wkids = $session->get('wkids', '', 'maqmahelpdesk');
    $sql = "SELECT id, wkdesc
            FROM #__support_workgroup
            WHERE id IN (" . $wkids . ")
              AND `show`='1'
            ORDER BY wkdesc";
    $database->setQuery($sql);
    $workgroups_change = $database->loadObjectList();
    $wkchange_html = '';
    for ($i = 0; $i < count($workgroups_change); $i++) {
        $wkrow = $workgroups_change[$i];
        $wkchange_html .= '<p><img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/link.png" border="0" align="absmiddle" /> <a href="javascript:ChangeWorkgroup(' . $wkrow->id . ');" title="">' . $wkrow->wkdesc . '</a></p>';
    }

    $imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/';

    // Sets the page title
    HelpdeskUtility::PageTitle('viewTicket', $row->subject);

	// Internal ticket
	$lists['internal'] = '<input type="radio" id="internal0" name="internal" value="0" class="inputbox" ' . (!$row->internal ? 'checked' : '') . ' /> ' . JText::_('MQ_NO') . ' <input type="radio" id="internal1" name="internal" value="1" class="inputbox" ' . ($row->internal ? 'checked' : '') . ' /> ' . JText::_('MQ_YES');

    $database->setQuery("SELECT s.status_group AS status_group
						  FROM #__support_ticket AS t
							   LEFT JOIN #__support_status AS s ON t.id_status = s.id
						  WHERE t.id=" . (int)$id);
    $ticket_closed = null;
    $ticket_closed = $database->loadResult();

    // Check Mosets Tree and Sobi Pro integration
    $directory = null;
    if ($row->id_directory && ($supportConfig->integrate_mtree || $supportConfig->integrate_sobi)) {
        if ($supportConfig->integrate_mtree) {
            $sql = "SELECT `link_id` AS directory_id, `link_name` AS directory_name
					FROM `#__mt_links`
					WHERE `link_id` = " . $row->id_directory;
        } else {
            $sql = "SELECT s.`sid` AS directory_id, s.`baseData` AS directory_name
					FROM `#__sobipro_field_data` AS s
						 INNER JOIN `#__sobipro_field_data` AS f ON f.`fid` = s.`fid`
					WHERE f.`filter` = 'title'
					  AND s.`sid` = " . $row->id_directory . "
					LIMIT 0, 1";
        }
        $database->setQuery($sql);
        $directory = $database->loadObject();
    }

    // for non support users: this ticket is close by a manager
    $closed_by_manager = 0;
    if (count($ticket_closed) > 0) {
        if (!$is_support && $ticket_closed == 'C' && !$is_manager) {
            $closed_by_manager = 1;
        }
    }

    $database->setQuery("SELECT id_status FROM #__support_log WHERE id_ticket='" . $row->id . "' AND id_status <> '" . $row->id_status . "' AND id_status <> '' AND id_status <> '0' ORDER BY date_time DESC LIMIT 1");
    $old_id_status = $database->loadResult();
    $old_status = ($old_id_status > 0) ? HelpdeskStatus::GetName($old_id_status) : "<i>" . JText::_('no_last_status_logged') . "</i>";

    // Get client id if the connected user if from support
    $sql = "SELECT cu.id_client, c.approval, c.`manager`
			FROM #__support_client_users AS cu 
				 INNER JOIN #__support_client AS c ON c.id = cu.id_client
			WHERE cu.id_user='" . $row->id_user . "'";
    $database->setQuery($sql);
    $client_details = $database->loadObject();

    // Get Ticket Messages
	$ticketMsgs = array();
	$ticketMsgs[0] = new stdClass();
    $ticketMsgs[0]->user = $row->an_name;
    $ticketMsgs[0]->date = $row->date;
    $ticketMsgs[0]->message = $row->message;
    $ticketMsgs[0]->timeused = 0;
    $ticketMsgs[0]->travel_time = 0;
    $ticketMsgs[0]->tickettravel = 0;
    $ticketMsgs[0]->acttype = '';
    $ticketMsgs[0]->actrate = '';
    $ticketMsgs[0]->multiplier = 0;
    $ticketMsgs[0]->user_rate = 0;
    $ticketMsgs[0]->start_time = 0;
    $ticketMsgs[0]->end_time = 0;
    $ticketMsgs[0]->break_time = 0;
    $ticketMsgs[0]->id_user = $row->id_user;
    $ticketMsgs[0]->id = $row->id;
    $ticketMsgs[0]->id_activity_type = 0;
    $ticketMsgs[0]->id_activity_rate = 0;
    $ticketMsgs[0]->reply_summary = '';
    $ticketMsgs[0]->id_msg = 0;
    $ticketMsgs[0]->msgtype = 'message';
    $ticketMsgs[0]->avatar = HelpdeskUser::GetAvatar($row->id_user);
    $ticketMsgs[0]->customerview = 1;

    $sql = "(SELECT r.`id` AS dbid, u.name as user, r.date, r.message, r.timeused, r.travel_time, r.tickettravel, t.description as acttype, ra.description as actrate, ra.multiplier, r.user_rate, r.start_time, r.end_time, r.break_time, r.id_user, r.id, r.id_activity_type, r.id_activity_rate, r.reply_summary, r.id as id_msg, su.avatar, 1 AS customerview, 'message' AS msgtype
    		FROM #__support_ticket_resp as r 
    			 LEFT JOIN #__users as u ON r.id_user=u.id 
    			 LEFT JOIN #__support_activity_type as t ON t.id=r.id_activity_type 
    			 LEFT JOIN #__support_activity_rate as ra ON ra.id=r.id_activity_rate 
    			 LEFT JOIN #__support_users AS su ON su.id_user=u.id 
    		WHERE r.id_ticket=" . (int)$row->id . ") 
    		
    		UNION
    		
    		(SELECT n.`id` AS dbid, u.name as user, n.date_time AS date, n.note, 0, 0, 0, '', '', 0, 0, 0, 0, 0,  n.id_user, n.id, 0, 0, '', 0, su.avatar, n.show AS customerview, 'note' AS msgtype
    		FROM #__support_note AS n
    			 INNER JOIN #__users AS u ON u.id=n.id_user
    			 LEFT JOIN #__support_users AS su ON su.id_user=u.id
    		WHERE n.id_ticket=" . (int)$row->id . (!$is_support ? " AND n.show=1" : '') . ")
    		
    		ORDER BY `date` ASC, dbid ASC";
    $database->setQuery($sql);
    $ticketMsgs = array_merge($ticketMsgs, $database->loadObjectList());

    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    if (!$is_support && ($supportConfig->support_only_show_assign == "1")) {
        $temp = 0;
        foreach ($ticketMsgs as $k => $val) {
            if ($k == 'user') {
                if ($ticketMsgs[$temp]->user == '')
                    $ticketMsgs[$temp]->user = $row->an_name;
                    $v = $ticketMsgs[$temp]->user;
                if ($v == HelpdeskTicket::GetAssign($row->assign_to)) {
                    $ticketMsgs[$temp]->user = JText::_('support_user');
                } else if ($v == 'Administrator') {
                    $ticketMsgs[$temp]->user = JText::_('administrator');
                } else if ($v == HelpdeskTicket::GetAssign($row->id_user)) {
                    $ticketMsgs[$temp]->user = JText::_('user');
                } else {
                    $ticketMsgs[$temp]->user = JText::_('other_user');
                }
            }
            $temp++;
        }
    }

    // Get Ticket Rating
    $database->setQuery("SELECT * FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
    $ticketRate = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Attachments
    $database->setQuery("SELECT * FROM #__support_file WHERE id='" . $row->id . "' AND source='T' " . (!$is_support ? "AND `public`=1" : "") . " ORDER BY `date` DESC ");
    $ticketAttachs = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';
	
	// Check if there are attachments user didn't saw yet
	$newattachs = 0;
	if($user->id) {
		$sql = "SELECT COUNT(*)
				FROM `#__support_file` AS f
				WHERE f.`source`='T' AND f.`id`=".$row->id." AND f.`id_file` NOT IN (SELECT n.`id_file` FROM `#__support_file_notify` AS n WHERE n.`id_user`=".$user->id.")";
		$database->setQuery($sql);
		$newattachs = $database->loadResult();
	}

    // Get Ticket Notes
    $database->setQuery("SELECT n.id, n.date_time as `date`, n.note, n.show, u.name as user FROM #__support_note as n, #__users as u WHERE n.id_ticket='" . $row->id . "' AND n.id_user=u.id " . ($is_support ? '' : " AND n.show='1'") . " ORDER BY n.id");
    $ticketNotes = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Log
    $log_column = (!$is_support && ($supportConfig->support_only_show_assign == "1")) ? 'log_reserved' : 'log';
    $database->setQuery("SELECT l.id, l.date_time as `date`, l." . $log_column . " as message, u.name as user, l.image FROM #__support_log as l LEFT JOIN #__users as u ON l.id_user=u.id WHERE l.id_ticket='" . $row->id . "' ORDER BY l.id DESC");
    $ticketLogs = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Tasks of all users because we are on administration
    $database->setQuery("SELECT t.id, t.date_time as `date`, t.task, t.status, u.name as user, t.start_time, t.end_time, t.break_time, t.traveltime, t.timeused, t.id_activity_type, t.id_activity_rate, t.end_date FROM #__support_task as t LEFT JOIN #__support_activity_type as y ON y.id=t.id_activity_type LEFT JOIN #__support_activity_rate as ra ON ra.id=t.id_activity_rate, #__users as u WHERE t.id_ticket='" . $row->id . "' AND t.id_user=u.id AND u.id='" . $user->id . "' ORDER BY t.id");
    $ticketTasks = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Custom Fields for the Workgroup
    $sql = "SELECT v.id_ticket, c.id, w.id_workgroup, w.id_field, w.required, w.support_only, w.new_only, c.caption, c.ftype, c.value, c.size, c.maxlength, v.newfield, c.tooltip, w.section, w.id_category 
			FROM #__support_wk_fields as w, #__support_custom_fields as c, #__support_field_value as v 
			WHERE w.id_field=c.id AND v.id_field=c.id AND v.id_ticket='" . $id . "' AND w.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' 
			ORDER BY w.section, w.ordering";
    $database->setQuery($sql);
    $customfields = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Rate
    $rate = 0;
    $database->setQuery("SELECT rate FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
    $rate = $database->loadResult();
    if ($rate == "") {
        $rate = 0;
    }
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Replies and Tasks Travel Sum Value
    $database->setQuery("SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(k.traveltime,'.', ':' )))),'%H:%i') FROM #__support_task as k, #__support_ticket as t WHERE t.id=k.id_ticket AND t.id='" . $row->id . "'");
    $traveltime = $database->loadResult();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    $database->setQuery("SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':' )))),'%H:%i') FROM #__support_ticket_resp as r, #__support_ticket as t WHERE t.id=r.id_ticket AND t.id='" . $row->id . "'");
    $traveltime = HelpdeskDate::ConvertHoursMinutesToDecimal($traveltime) + HelpdeskDate::ConvertHoursMinutesToDecimal($database->loadResult());
    $traveltime = HelpdeskDate::ConvertDecimalsToHoursMinutes($traveltime);
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Travel Time on Tickets and Tasks Value
    $database->setQuery("SELECT c.travel_time FROM #__support_client as c, #__support_client_users as u, #__support_ticket as t WHERE c.id=u.id_client AND t.id_user=u.id_user AND t.id='" . $row->id . "'");
    $clienttravel = $database->loadResult();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';
    $document->addScriptDeclaration( 'var MQM_CLIENT_TRAVEL = "'.$clienttravel.'";' );

    // Get Client Rate Value
    $database->setQuery("SELECT c.rate FROM #__support_client as c, #__support_client_users as u, #__support_ticket as t WHERE c.id=u.id_client AND t.id_user=u.id_user AND t.id='" . $row->id . "'");
    $clientvalue = $database->loadResult();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Messages and Tasks Values by Activity Type
    $sql = "(SELECT r.id, r.timeused, r.user_rate as rate, r.travel_time, r.tickettravel, ra.multiplier, t.description AS acttype, ra.description AS actrate 
			 FROM #__support_ticket_resp as r
				  LEFT JOIN #__support_activity_type as t ON t.id=r.id_activity_type
				  INNER JOIN #__support_activity_rate as ra ON ra.id=r.id_activity_rate
			 WHERE r.id_ticket='" . $row->id . "' 
			 ORDER BY t.description, ra.description) 
			
			UNION 
			
			(SELECT r.id, r.timeused, r.rate, ra.multiplier, r.traveltime as travel_time, r.travel as tickettravel, t.description AS acttype, ra.description AS actrate 
			 FROM #__support_task as r
				  LEFT JOIN #__support_activity_type as t ON t.id=r.id_activity_type
				  INNER JOIN #__support_activity_rate as ra ON ra.id=r.id_activity_rate
			 WHERE r.id_ticket='" . $row->id . "' 
			 ORDER BY t.description, ra.description)";
    $database->setQuery($sql);
    $ticketValues = $database->loadObjectList();

    $ticket_values = array();
    $prevact = '';
    $prevrate = '';
    $prevind = 0;
    $total_values = 0;
    $z = 0;

    for ($i = 0; $i < count($ticketValues); $i++)
    {
        $xpto = $ticketValues[$i];

        // Reset travel time value to 0 as its not chargeable
        if ($xpto->travel_time == '0')
        {
            $xpto->tickettravel = '0';
        }

        $totaltime_decim = HelpdeskDate::AddHoursMinutes($xpto->timeused, $xpto->tickettravel, 'decim');
        $totalvalue = number_format(str_replace(':', '.', $totaltime_decim) * $xpto->rate * $xpto->multiplier, 2);
        $total_values = $total_values + $totalvalue;

	    if (!isset($ticket_values[$prevind]['value']))
	    {
		    $ticket_values[$prevind]['value'] = 0;
	    }

        if ($xpto->acttype == $prevact)
        {
            $ticket_values[$prevind]['description'] = $xpto->acttype;
            $ticket_values[$prevind]['value'] = number_format($ticket_values[$prevind]['value'] + $totalvalue, 2);
        }
        else
        {
            $prevact = $xpto->acttype;
            $prevind = $z;
            $ticket_values[$z]['description'] = $xpto->acttype;
            $ticket_values[$z]['value'] = $totalvalue;
            $z++;
        }
    }

    // Get Ticket Messages and Tasks Times by Activity Type
    $sql = "(SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(r.timeused,'.', ':' )))),'%H:%i')AS total, t.description AS acttype, ra.description AS actrate 
			FROM #__support_ticket_resp as r
				 LEFT JOIN #__support_activity_type AS t ON t.id=r.id_activity_type
				 LEFT JOIN #__support_activity_rate AS ra ON ra.id=r.id_activity_rate
			WHERE r.id_ticket='" . $row->id . "'
			GROUP BY t.description, ra.description 
			ORDER BY t.description, ra.description) 
			
			UNION 
			
			(SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(r.timeused,'.', ':' )))),'%H:%i') AS total, t.description AS acttype, ra.description AS actrate 
			FROM #__support_task as r
				 LEFT JOIN #__support_activity_type AS t ON t.id=r.id_activity_type
				 LEFT JOIN #__support_activity_rate AS ra ON ra.id=r.id_activity_rate
			WHERE r.id_ticket='" . $row->id . "'
			GROUP BY t.description, ra.description 
			ORDER BY t.description, ra.description)";
    $database->setQuery($sql);
    $ticketTimes = $database->loadObjectList();

    $ticketTimes2 = array();
    $prevact = '';
    $prevrate = '';
    $prevind = 0;
    $z = 0;

    for ($i = 0; $i < count($ticketTimes); $i++) {
        $xpto = $ticketTimes[$i];

	    $ticketTimes2[$prevind] = new stdClass();

	    if (!isset($ticketTimes2[$prevind]->total))
	    {
		    $ticketTimes2[$prevind]->total = 0;
	    }

        if ($xpto->acttype == $prevact) {
            $ticketTimes2[$prevind]->acttype = $xpto->acttype;
            $ticketTimes2[$prevind]->total = HelpdeskDate::AddHoursMinutes($ticketTimes2[$prevind]->total, $xpto->total, 'hhmm');
        } else {
            $prevact = $xpto->acttype;
            $prevind = $z;

            $xpto->total = str_replace('.', ':', $xpto->total);
            $total_decim = HelpdeskDate::ConvertHoursMinutesToDecimal($xpto->total);

            $ticketTimes2[$z]->acttype = $xpto->acttype;
            $ticketTimes2[$z]->total = HelpdeskDate::ConvertDecimalsToHoursMinutes($total_decim);
            $z++;
        }
    }

    // Build the Travel yes/no list
    $lists['travel'] = '<input type="radio" id="travel_time0" name="travel_time" value="0" class="inputbox" onclick="SetTravelTime();" checked /> ' . JText::_('MQ_NO') . ' <input type="radio" id="travel_time1" name="travel_time" value="1" class="inputbox" onclick="SetTravelTime();" /> ' . JText::_('MQ_YES');
    $lists['task_travel'] = '<input type="radio" id="tasktravel0" name="tasktravel" value="0" class="inputbox" onclick="SetTravelTime();" checked /> ' . JText::_('MQ_NO') . ' <input type="radio" id="tasktravel1" name="tasktravel" value="1" class="inputbox" onclick="SetTravelTime();" /> ' . JText::_('MQ_YES');

    // Get Activity Rate Default
    $database->setQuery("SELECT id FROM #__support_activity_rate WHERE isdefault='1'");
    $default_rate = $database->loadResult();

    // Build Activity Rate select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_rate WHERE published='1' ORDER BY description";
    $database->setQuery($sql);
    $rows_actrate = $database->loadObjectList();
    $lists['activity_rate'] = JHTML::_('select.genericlist', $rows_actrate, 'id_activity_rate', '', 'value', 'text', $default_rate);
    $lists['task_rate'] = JHTML::_('select.genericlist', $rows_actrate, 'activity_rate', '', 'value', 'text', $default_rate);

    // Get Activity Type Default
    $database->setQuery("SELECT id FROM #__support_activity_type WHERE isdefault='1'");
    $default_type = $database->loadResult();

    // Build Activity Type select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_type WHERE published='1' ORDER BY description";
    $database->setQuery($sql);
    $rows_acttype = $database->loadObjectList();

    $lists['activity_type'] = JHTML::_('select.genericlist', $rows_acttype, 'id_activity_type', '', 'value', 'text', $default_type);
    $lists['task_type'] = JHTML::_('select.genericlist', $rows_acttype, 'activity_type', '', 'value', 'text', $default_type);

    // Build User Task select list
    $sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__users as u, #__support_permission as p WHERE u.id=p.id_user ORDER BY u.name";
    $database->setQuery($sql);
    $usertask = $database->loadObjectList();
    $usertask = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $usertask);
    $lists['usertask'] = JHTML::_('select.genericlist', $usertask, 'usertask', '', 'value', 'text', $user->id);

    $lists['taskstatus'] = '<input type="radio" name="status" id="status" value="C" class="inputbox" />' . JText::_('MQ_NO') . ' <input type="radio" name="status" id="status" value="O" checked="checked" class="inputbox" />' . JText::_('MQ_YES');

    // Build Assign To select list
    if ($is_support)
    {
        $sql = "SELECT DISTINCT(u.`id`) AS value, CONCAT(u.`name`, ' ', IF(p.`level` > 0, '(', ''), IF(p.`level` > 0, '" . JText::_('support_level') . " ', ''), IF(p.`level` > 0, p.`level`, ''), IF(p.`level` > 0, ')', '')) AS text 
				FROM #__users as u, #__support_permission as p 
				WHERE u.id=p.id_user
				  AND p.id_workgroup='" . $id_workgroup . "' " . ((int) $supportOptions->manager > 5 ? '' : 'AND u.id=' . $user->id) . "
				ORDER BY u.name";
        $database->setQuery($sql);
        $rows_assign = $database->loadObjectList();
        $assign_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_assign);
        $lists['assign'] = JHTML::_('select.genericlist', $assign_wk, 'assign_to', 'id="assign_to" ', 'value', 'text', ($row->assign_to ? $row->assign_to : $user->id));
    }

    // Build Status select list
    $lists['status'] = HelpdeskTicket::BuildStatusList($row->id_status, $old_id_status);

    // Build Priority select list
    $sql = "SELECT `id` AS value, concat(`description`,' (',timevalue,' ',timeunit,')') AS text FROM #__support_priority WHERE `show`=1 ORDER BY description";
    $database->setQuery($sql);
    $rows_priority = $database->loadObjectList();
    $lists['priority'] = JHTML::_('select.genericlist', $rows_priority, 'id_priority', 'id="id_priority" onchange="DueDatePonderado();"', 'value', 'text', $row->id_priority);

    // Build workgroup categories select list
    $lists['category'] = HelpdeskForm::BuildCategories($row->id_category, false, true, false, false);

    $old_duedate_date = date("Y-m-d", (strtotime(JString::substr($row->duedate, 1, 10))));
    $old_duedate_hour = date("H:i", (strtotime(JString::substr($row->duedate, 11, 5))));

    // ***********************************************************************
    // Times
    $ticket_times = array();
    $ticketTimeTotal = 0;
    if (count($ticketTimes2) > 0) {
        $i = 1;
        $total = 0;
        foreach ($ticketTimes2 as $key2 => $value2) {
            if (is_object($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    $ticket_times[$i][$key3] = $value3;

                    if ($key3 == 'acttype')
                        $ticket_times[$i]['description'] = $value3;

                    if ($key3 == 'total') {
                        $total = $total + HelpdeskDate::ConvertHoursMinutesToDecimal($value3);
                        $ticket_times[$i]['value'] = $value3;
                    }
                }
            }

            $i++;
        }

        $ticket_times[$i]['description'] = JText::_('travel_time');
        $ticket_times[$i]['value'] = $traveltime;
        $total = $total + HelpdeskDate::ConvertHoursMinutesToDecimal($traveltime);
        $ticketTimeTotal = HelpdeskDate::ConvertDecimalsToHoursMinutes($total);
    }

    // ***********************************************************************
    // Activities
    $i = 1;
    foreach ($ticketMsgs as $key2 => $value2) {
        $acttype = '';
        $actrate = '';
        $start_time = '';
        $end_time = '';
        $break_time = '';
        $timeused = '';
        $tickettravel = '';
        $travel_time = '';
        $id_user = '';
        $message = '';
        $id = '';

        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $activities_rows[$i][$key3] = $value3;

                if ($key3 == 'acttype') {
                    $acttype = $value3;
                }
                if ($key3 == 'actrate') {
                    $actrate = $value3;
                }
                if ($key3 == 'start_time') {
                    $start_time = $value3;
                }
                if ($key3 == 'end_time') {
                    $end_time = $value3;
                }
                if ($key3 == 'break_time') {
                    $break_time = $value3;
                }
                if ($key3 == 'timeused') {
                    $timeused = $value3;
                }
                if ($key3 == 'tickettravel') {
                    $tickettravel = $value3;
                }
                if ($key3 == 'travel_time') {
                    $travel_time = $value3;
                }
                if ($key3 == 'id_user') {
                    $id_user = $value3;
                }
                if ($key3 == 'avatar') {
                    $activities_rows[$i]['avatar'] = HelpdeskUser::GetAvatar($id_user);
                }
                if ($key3 == 'message') {
                    $message = str_replace('\"', "", str_replace("\'", "'", $value3));
                }
                if ($key3 == 'id') {
                    $id = $value3;
                }
                if ($key3 == 'id_msg') {
                    $id_msg = $value3;
                }
                if ($key3 == 'date') {
                    $value3 = explode(' ', $value3);
                    $activities_rows[$i]['date_only'] = $value3[0];
                    $activities_rows[$i]['hours_only'] = $value3[1];
                }
            }
        }

        // Builds the tooltip
        $status_group = HelpdeskTicket::GetStatusGroup($row->id_status);

        if ($workgroupSettings->use_activity) {
            $tmp_tooltip_msg = ((''
                . '<b>' . JText::_('tmpl_msg12') . '</b> ' . ($acttype == "" ? JText::_('empty') : $acttype) . '<br />'
                . '<b>' . JText::_('tmpl_msg13') . '</b> ' . ($actrate == "" ? "0.00" : $actrate) . '<br />'
                . '<b>' . JText::_('tmpl_msg14') . '</b> ' . ($start_time == "" ? JText::_('empty') : $start_time == "0" ? '0:00' : $start_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg15') . '</b> ' . ($end_time == "" ? JText::_('empty') : $end_time == "0" ? '0:00' : $end_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg16') . '</b> ' . ($break_time == "" ? JText::_('empty') : $break_time == "0" ? '0:00' : $break_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg19') . '</b> ' . str_replace('.', ':', $timeused == "0" ? '0:00' : $timeused) . '<br />'
                . '<b>' . JText::_('tmpl_msg22') . '</b> ' . str_replace('.', ':', $tickettravel)
                . ' (' . JText::_('chargeable') . ': ' . ($travel_time == 1 ? JText::_('MQ_YES') : JText::_('MQ_NO')) . ')' . '<br />'));
        } else {
            $tmp_tooltip_msg = '';
        }

        $tmp_tooltip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('activity_details') . '::' . $tmp_tooltip_msg) . '"> <img src="' . $imgpath . '16px/info.png" align="absmiddle" border="0" hspace="5" style="cursor:help;"/></span>';

        $activities_rows[$i]['tooltip'] = $tmp_tooltip;

        // Checks the lines and chars limits
        if ($workgroupSettings->lim_actmsgs)
        {
            $workgroupSettings->lim_actmsgs_chars ? $char_limit = $workgroupSettings->lim_actmsgs_chars : $char_limit = 300;
            $workgroupSettings->lim_actmsgs_lines ? $line_limit = $workgroupSettings->lim_actmsgs_lines : $line_limit = 5;
            $line_count = substr_count($message, "\n") + 1;
            $char_count = strlen($message);

            if (($char_count > $char_limit) || ($line_count > $line_limit))
            {
                $more_link = '<p><img class="alglft" src="' . $imgpath . '16px/view+.png" border="0" alt="more" style="cursor:pointer;" onclick="$jMaQma(\'#' . $id_msg . '_short\').toggle(); $jMaQma(\'#' . $id_msg . '_all\').toggle(); return false;" /></p>';
                $less_link = '<p><img class="alglft" src="' . $imgpath . '16px/view-.png" border="0" alt="less" style="cursor:pointer;" onclick="$jMaQma(\'#' . $id_msg . '_short\').toggle(); $jMaQma(\'#' . $id_msg . '_all\').toggle(); return false;" /></p>';
                $msg_short_header = '<div id="' . $id_msg . '_short" style="display:all;"><div style="width:100%;">';
                $msg_short_footer = '</div></div>';
                $msg_all_header = '<div id="' . $id_msg . '_all" style="display:none;"><div style="width:100%;">';
                $msg_all_footer = '</div></div>';

                if ($line_count > $line_limit && $char_count < $char_limit)
                {
                    $linebr_char_num = 0;
                    for ($linebr = 0; ($linebr < $line_limit); $linebr++)
                    {
                        $linebr_char_num = strpos($message, "\n", $linebr_char_num + 1);
                    }
                    $msg_short = JString::substr($message, 0, $linebr_char_num);

                }
                else
                {
                    $msg_short = JString::substr($message, 0, $char_limit);
                }

                $msg_short = rtrim($msg_short, "\r \n");
                $msg_all = $message;

                if ($workgroupSettings->hyper_links)
                {
                    $msg_all = HelpdeskUtility::TextHyperlinks($msg_all);
                    $msg_short = HelpdeskUtility::TextHyperlinks($msg_short);
                }
                $message = $msg_short_header . $msg_short . '...' . $more_link . $msg_short_footer . $msg_all_header . $msg_all . $less_link . $msg_all_footer;
                /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                    $message = nl2br($message);
                }*/
            }
            else
            {
                if ($workgroupSettings->hyper_links)
                {
                    $message = HelpdeskUtility::TextHyperlinks($message);
                }
                /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                    $message = nl2br($message);
                }*/
            }
        } else {
            if ($workgroupSettings->hyper_links) {
                $message = HelpdeskUtility::TextHyperlinks($message);
            }
            /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                $message = nl2br($message);
            }*/
        }

        $activities_rows[$i]['message_original'] = $activities_rows[$i]['message'];
        $activities_rows[$i]['message'] = $message;

        $i++;
    }

    // ***********************************************************************
    // Custom fields
    $i = 1;
    $cfields_hiddenfield = "";
    $j = 1;

    foreach ($customfields as $key2 => $value2) {
        $fid = 0;
        $ftype = '';
        $fvalue = '';
        $fsize = '';
        $flength = '';

        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $cfields_rows[$i][$key3] = $value3;
                if ($key3 == 'id')
                    $fid = $value3;
                if ($key3 == 'ftype')
                    $ftype = $value3;
                if ($key3 == 'value')
                    $fvalue = $value3;
                if ($key3 == 'size')
                    $fsize = $value3;
                if ($key3 == 'maxlength')
                    $flength = $value3;
                if ($key3 == 'support_only')
                    $fsupportonly = $value3;
                if ($key3 == 'new_only')
                    $fnewonly = $value3;
                if ($key3 == 'tooltip')
                    $ftooltip = $value3;
            }
        }

        if (((!$is_support) && ($fsupportonly > 0)) || ($fnewonly)) {
            if ($fsupportonly == 2) {
                $cfields_hiddenfield .= HelpdeskForm::WriteField($row->id, $fid, 'hidden', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                unset($cfields_rows[$i]); // remove from array
            } else { // 0 or 1
                $cfields_rows[$i]['field'] = HelpdeskForm::WriteField($row->id, $fid, 'readonly', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                $j++;
            }
        } else {
            $exclude = (($fsupportonly && !$is_support) || ($fnewonly)) ? 1 : 0;
            $cfields_rows[$i]['field'] = HelpdeskForm::WriteField($row->id, $fid, $ftype, $fvalue, $fsize, $flength, 0, 0, $exclude, 0, 0, $ftooltip);
            $j++;
        }
        $i++;
    }

    // ***********************************************************************
    // Attachments
    for ($i = 1; $i <= $supportConfig->attachs_num; $i++) {
        $attachs[$i]['number'] = $i;

        if ($is_support) {
            $attachs[$i]['available'] = '<input type="radio" id="available' . $i . '0" name="available' . $i . '" value="0" class="inputbox" /> ' . JText::_('MQ_NO') . ' <input type="radio" id="available' . $i . '1" name="available' . $i . '" value="1" class="inputbox" checked /> ' . JText::_('MQ_YES');
        } else {
            $attachs[$i]['available'] = '<input type="hidden" name="available' . $i . '" value="1" />';
        }
    }

    // ***********************************************************************
    // Tasks
    $i = 1;
    foreach ($ticketTasks as $key2 => $value2) {
        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $ticket_tasks[$i][$key3] = $value3;
            }
        }

        $i++;
    }

    // ***********************************************************************
    // Notes
    $i = 1;
    foreach ($ticketNotes as $key2 => $value2) {
        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $ticket_notes[$i][$key3] = $value3;

                if (!$is_support && ($supportConfig->support_only_show_assign == "1") && ($key3 == 'user')) {
                    if ($value3 == HelpdeskTicket::GetAssign($row->assign_to)) {
                        $ticket_notes[$i][$key3] = JText::_('support_user');
                    } else if ($value3 == 'Administrator') {
                        $ticket_notes[$i][$key3] = JText::_('administrator');
                    } else if ($value3 == HelpdeskTicket::GetAssign($row->id_user)) {
                        $ticket_notes[$i][$key3] = JText::_('user');
                    } else {
                        $ticket_notes[$i][$key3] = JText::_('other_user');
                    }
                }

                if ($key3 == 'show')
                    $ticket_notes[$i]['available'] = '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($value3 ? 'ok' : 'no') . '.png" />';
            }
        }

        $i++;
    }

    // ***********************************************************************
    // Attachments
    $i = 1;
    if (count($ticketAttachs) > 0) { // JP 23.04.2009
        foreach ($ticketAttachs as $key2 => $value2) {
            $id_file = '';
            $id = '';

            if (is_object($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    $ticket_attachs[$i][$key3] = $value3;

                    if ($key3 == 'id')
                        $id = $value3;
                    if ($key3 == 'id_file')
                        $id_file = $value3;
                    if ($key3 == 'id_user')
                        $id_user = $value3;
                    if ($key3 == 'public')
                        $public = $value3;
                }
            }

            $ticket_attachs[$i]['info'] = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_("attachment") . '::<b>' . JText::_('date') . '</b>:' . $ticket_attachs[$i]['date'] . '<br /><b>' . JText::_('description') . '</b>:' . $ticket_attachs[$i]['description']) . '"><img src="' . $imgpath . 'info.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /></span>';

            $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_download&id=' . $id_file . '&extid=' . $id, false);
            $tools = '<a href="' . $link . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/down.png" title="' . JText::_('download') . '" border="0" /></a>';

            if ($is_support || $id_user == $user->id) {
                $link2 = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_delattach&id=' . $id_file . '&extid=' . $id, false);
                $tools .= '&nbsp;<a href="' . $link2 . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/delete.png" title="' . JText::_('delete') . '" border="0" /></a>';
            }

            $available = '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($public == 1 ? 'ok' : 'no') . '.png" />';
            $ticket_attachs[$i]['available'] = $available;

            $ticket_attachs[$i]['tools'] = $tools;
            $ticket_attachs[$i]['link'] = $link;

            $i++;
        }

        if (!$is_support) {
            for ($z = 1; $z < count($ticket_attachs); $z++) {
                if ($ticket_attachs[$z]["public"] == 0) {
                    unset ($ticket_attachs[$z]);
                }
            }
        }
    }

    // Variables
    if ($row->duedate < date("Y-m-d")) {
        $status_color = 'important';
    } elseif (JString::substr($row->duedate, 0, 10) == date("Y-m-d")) {
        $status_color = 'warning';
    } elseif (JString::substr($row->duedate, 0, 10) > date("Y-m-d")) {
        $status_color = 'default';
    }

    $acttyperate_hover_tip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('acttyperate_hover_subj') . '::' . JText::_('acttyperate_hover_tip')) . '"> <img src="' . $imgpath . '16px/config.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('acttyperate_hover_subj') . '</b></span>';
    $start_times_hover_tip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('start_times_hover_subj') . '::' . JText::_('start_times_hover_tip')) . '"> <img src="' . $imgpath . '16px/time.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('start_times_hover_subj') . '</b></span>';
    $tmpl_msg21 = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('tmpl_msg22') . '::' . JText::_('tmpl_msg21')) . '"> <img src="' . $imgpath . '16px/car.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('tmpl_msg22') . '</b></span>';
    $status_group = HelpdeskTicket::GetStatusGroup($row->id_status);

    $duedate_date = date("Y-m-d", (strtotime(JString::substr($row->duedate, 1, 10))));
    $duedate_hour = date("H:i", (strtotime(JString::substr($row->duedate, 11, 5))));

    if (((!$row->id_export && !$print) && (($usertype >= $supportConfig->support_change_status) || ($supportConfig->client_change_status == 1 && (int) $is_client) || ($supportConfig->register_user_change_status == 1)))) {
        $status = $lists['status'];
    } else {
        $status = HelpdeskStatus::GetName($row->id_status);
    }

    /*if ($closed_by_manager) {
        $status = HelpdeskStatus::GetName($row->id_status);
    }*/

    if (!$is_support) {
        if ($supportConfig->support_only_show_assign != 1) {
            $assign = HelpdeskTicket::GetAssign($row->assign_to);
        } else {
            $assign = '<i>' . JText::_('assigned_hidden') . '</i>';
        }
        $category = HelpdeskCategory::GetName($row->id_category);
        if ($status_group != 'C' && !$print) {
            $priority = $lists['priority'];
        } else {
            $priority = HelpdeskPriority::GetName($row->id_priority);
        }
    } else {
        if ($status_group != 'C' && !$print) {
            $assign = $lists['assign'];
            $priority = $lists['priority'];
            $category = $lists['category'];
        } else {
            $assign = HelpdeskTicket::GetAssign($row->assign_to);
            $priority = HelpdeskPriority::GetName($row->id_priority);
            $category = HelpdeskCategory::GetName($row->id_category) . '<input type="hidden" id="id_category" name="id_category" value="' . $row->id_category . '" />';
        }
    }

    $source_desc = '';
    if ($row->source == "M") {
        $source_desc = JText::_('email');
    } elseif ($row->source == "F") {
        $source_desc = JText::_('fax');
    } elseif ($row->source == "O") {
        $source_desc = JText::_('other');
    } elseif ($row->source == "W") {
        $source_desc = JText::_('website');
    } elseif ($row->source == "P") {
        $source_desc = JText::_('phone');
    }

    if (count($ticketAttachs) > 0) {
        if (!$is_support) {
            $count_ticketAttachs = count($ticket_attachs);
        } else {
            $count_ticketAttachs = count($ticketAttachs);
        }
    } else {
        $count_ticketAttachs = '0';
    }

    // BBB Integration
    $sql = "SELECT b.`id`, b.`id_user`, b.`date_created`, b.`meeting_date`, b.`meeting_hours`
			FROM `#__support_bbb` AS b
			WHERE b.`id_ticket`=" . $row->id . "
			ORDER BY b.`meeting_date` DESC, b.`meeting_hours` DESC";
    $database->setQuery($sql);
    $ticketMeetings = $database->loadObjectList();

    // Mosets Tree Integration
    $mtree_links = null;
    if ($supportConfig->integrate_mtree && $is_support && $row->id_user) {
        $sql = "SELECT `link_id`, `link_name`, `link_published`, `link_approved`, `link_hits`, `link_featured`, `link_votes`, `internal_notes`, `website`
				FROM `#__mt_links`
				WHERE `user_id`=" . $row->id_user . "
				ORDER BY `link_name`";
        $database->setQuery($sql);
        $mtree_links = $database->loadObjectList();
    }

    // SobiPro Integration
    $sobipro_links = null;
    if ($supportConfig->integrate_sobi && $is_support && $row->id_user) {
        $sql = "SELECT s.`sid`, s.`section`, s.`baseData`
				FROM `#__sobipro_field_data` AS s
					 INNER JOIN `#__sobipro_field` AS f ON f.`fid` = s.`fid`
				WHERE f.`filter` = 'title'
				  AND s.`createdBy` = " . $row->id_user . "
				GROUP BY s.`sid`
				ORDER BY f.`position`, s.`baseData`";
        $database->setQuery($sql);
        $sobipro_links = $database->loadObjectList();
    }

    // ArtOfUser Integration
    $artofuser_notes = null;
    if ($supportConfig->integrate_artofuser && $is_support && $row->id_user) {
        $sql = "SELECT `subject`, `created_time`, `id`, `body`
				FROM `#__artofuser_notes` 
				WHERE `user_id` = " . $row->id_user . "
				ORDER BY `created_time`";
        $database->setQuery($sql);
        $artofuser_notes = $database->loadObjectList();
    }

    // Possible relations engine
    $row->subject = trim($row->subject);
	$relatedsearch = str_replace('?', '', $row->subject);
	$relatedsearch = str_replace("+", '', $relatedsearch);
	$relatedsearch = str_replace("/", '', $relatedsearch);
	$relatedsearch = str_replace("\\", '', $relatedsearch);
	$relatedsearch = str_replace("'", '', $relatedsearch);
	$relatedsearch = str_replace(',', ' ', $relatedsearch);
	$relatedsearch = str_replace('.', ' ', $relatedsearch);
	$relatedsearch = str_replace('    ', ' ', $relatedsearch);
	$relatedsearch = str_replace('   ', ' ', $relatedsearch);
	$relatedsearch = str_replace('  ', ' ', $relatedsearch);
	$relatedsearch = str_replace('  ', ' ', $relatedsearch);
    $relatedsearch = trim($relatedsearch);
    $relatedsearch = explode(' ', $relatedsearch);
    $relatedsearch = array_unique($relatedsearch);
    $relatedsearch = array_filter($relatedsearch, "HelpdeskTicket::IgnoreSearch");
    $relatedsearch = array_values($relatedsearch);
    $filter_tickets = array();
    $filter_kb = array();
    $filter_discussions = array();
    $related_javascript = '';
    for ($i = 0; $i < count($relatedsearch); $i++)
    {
        $filter_tickets[] = "`subject` REGEXP '.*" . $relatedsearch[$i] . "*'";
        $filter_kb[] = "`kbtitle` REGEXP '.*" . $relatedsearch[$i] . "*'";
        $filter_discussions[] = "`title` REGEXP '.*" . $relatedsearch[$i] . "*'";
        $related_javascript.= "\$jMaQma('#related_discussions').highlight('".$relatedsearch[$i]."'); \n";
        $related_javascript.= "\$jMaQma('#related_kb').highlight('".$relatedsearch[$i]."'); \n";
        $related_javascript.= "\$jMaQma('#related_tickets').highlight('".$relatedsearch[$i]."'); \n";
    }
    $document->addScriptDeclaration("function HighlightRelatedTerms() { $related_javascript }");

    // Related tickets
    $sql = "SELECT `id`, `subject`, `id_workgroup`" . (count($filter_tickets) ? ', ' : '') . implode(', ', $filter_tickets) . " 
			FROM `#__support_ticket` 
			WHERE (" . implode(' OR ', $filter_tickets) . ") 
			ORDER BY " . implode(' DESC, ', $filter_tickets) . " DESC
			LIMIT 0, 50";
    $database->setQuery($sql);
    $related_tickets = $database->loadObjectList();

    // Related knowledge base
    $sql = "SELECT `id`, `kbtitle`" . (count($filter_kb) ? ', ' : '') . implode(', ', $filter_kb) . " 
			FROM `#__support_kb` 
			WHERE (" . implode(' OR ', $filter_kb) . ") 
			ORDER BY " . implode(' DESC, ', $filter_kb) . " DESC
			LIMIT 0, 50";
    $database->setQuery($sql);
    $related_kb = $database->loadObjectList();

    // Related discussions
    $sql = "SELECT `id`, `title`, `id_workgroup`" . (count($filter_discussions) ? ', ' : '') . implode(', ', $filter_discussions) . " 
			FROM `#__support_discussions` 
			WHERE (" . implode(' OR ', $filter_discussions) . ") 
			ORDER BY " . implode(' DESC, ', $filter_discussions) . " DESC
			LIMIT 0, 50";
    $database->setQuery($sql);
    $related_discussions = $database->loadObjectList();

    $database->setQuery("SELECT COUNT(*) FROM #__support_client_users WHERE id_client='" . $is_client . "' AND id_user='" . $user->id . "' AND manager='1'");
    if ($is_support || $database->loadResult() > 0 || $row->id_user == $user->id) {
        $tmplfile = ($is_support && !$print ? 'view_ticket_support' : (!$is_support && !$print ? 'view_ticket_customer' : 'print_version'));

        // Display toolbar
        HelpdeskToolbar::Create();

        $tmplfile = HelpdeskTemplate::GetFile('tickets/' . $tmplfile);
        include $tmplfile;
    } else {
        $msg = JText::_('no_permition');
        HelpdeskUtility::ShowTplMessage($msg, $id_workgroup);
    }
}


function bookmarkTicket($id)
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);

    $database->setQuery("SELECT COUNT(*) FROM #__support_bookmark WHERE id_user='" . $user->id . "' AND id_bookmark='" . $id . "' AND source='T'");
    $exists = $database->loadResult();

    if ($exists == 0) {
        $database->setQuery("INSERT INTO #__support_bookmark(id_user, id_bookmark, source) VALUES('" . $user->id . "', '" . $id . "', 'T')");
        $database->query();
        HelpdeskUtility::AddGlobalMessage(JText::_('ticket_bookmark'), 'i', JText::_('ticket_bookmark'), $user->id, $user->username);
    } else {
        HelpdeskUtility::AddGlobalMessage(JText::_('bookmark_ticket'), 'w', JText::_('bookmark_ticket'), $user->id, $user->username);
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $id, false);
    $mainframe->redirect($url);
}


function newTicket($duplicate=0)
{
    global $supportOptions;

    $database = JFactory::getDBO();
    $document = JFactory::getDocument();
	$uri = JURI::getInstance();
    $editor = JFactory::getEditor();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getInt('id_category', 0);
    $id_directory = JRequest::getInt('id_directory', 0);

    HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
    HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

    $document->addScriptDeclaration( 'var MQM_IS_ANONYMOUS = false;' );
    $document->addScriptDeclaration( 'var MQM_LOADING = "'.addslashes(JText::_('loading')).'";' );
    $document->addScriptDeclaration( 'var MQM_CATEGORY = "'.addslashes(JText::_('category_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG01 = "'.addslashes(JText::_('tmpl_msg01')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG02 = "'.addslashes(JText::_('tmpl_msg02')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG03 = "'.addslashes(JText::_('tmpl_msg03')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG04 = "'.addslashes(JText::_('tmpl_msg04')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG05 = "'.addslashes(JText::_('tmpl_msg05')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG06 = "'.addslashes(JText::_('tmpl_msg06')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL = "'.addslashes(JText::_('tmpl_ticket_cancelquestion')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL_LINK = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my', false).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MONTH = "'.addslashes(JText::_('invalid_month')).'";' );
    $document->addScriptDeclaration( 'var MQM_YEAR1 = "'.(HelpdeskDate::DateOffset("%Y") + 1).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_YEAR = "'.addslashes(JText::_('invalid_year')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_DAY = "'.addslashes(JText::_('invalid_day')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MINUTES = "'.addslashes(JText::_('invalid_minutes')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_HOURS = "'.addslashes(JText::_('invalid_hours')).'";' );
    $document->addScriptDeclaration( 'var MQM_NO_USER = "'.addslashes(JText::_('no_user')).'";' );
    $document->addScriptDeclaration( 'var MQM_SCREENR_RECORDER_TITLE = "'.addslashes(JText::_('SCREENR_RECORDER_TITLE')).'";' );
    $document->addScriptDeclaration( 'var MQM_SCREENR_MSG_RECORDING = "'.addslashes(JText::_('SCREENR_MSG_RECORDING')).'";' );
    $document->addScriptDeclaration( 'var MQM_SCREENR_MSG_COMPLETE = "'.addslashes(JText::_('SCREENR_MSG_COMPLETE')).'";' );
    $document->addScriptDeclaration( 'var MQM_USER_EMAIL = "'.addslashes($user->email).'";' );
    $document->addScriptDeclaration( 'var MQM_USER_NAME = "'.addslashes($user->name).'";' );
    $document->addScriptDeclaration( 'var MQM_SCREENR_ACCOUNT = "'.$supportConfig->screenr_account.'";' );
    $document->addScriptDeclaration( 'var MQM_SCREENR_API = "'.$supportConfig->screenr_api_id.'";' );
    if ($is_support) {
	    if ($supportConfig->editor == 'builtin')
	    {
		    $document->addScriptDeclaration('function CheckHTMLEditor() { return true; }');
	    }
	    else
	    {
		    $document->addScriptDeclaration('function CheckHTMLEditor() { '.$editor->save('problem') . ' ' . $editor->save('reply') . ' }');
	    }
        HelpdeskUtility::AppendResource('helpdesk.tickets.new.support.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
    }else{
	    if ($supportConfig->screenr_account!='' && $supportConfig->screenr_api_id!='') {
		    $document->addScript($uri->getScheme() . "://imaqma.viewscreencasts.com/api/recorder");
	    }
        HelpdeskUtility::AppendResource('helpdesk.tickets.new.customer.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
    }

    $id_client = JRequest::getVar('id_client', 0, '', 'int');

    // Sets the page title
    HelpdeskUtility::PageTitle('newTicket');

    // Check if it's a OK to duplicate
    if (!$is_support && $duplicate)
    {
        $duplicate = 0;
    }

    // Get Custom Fields for the Workgroup
    $sql = "SELECT c.id, w.id_workgroup, w.id_field, w.required, w.support_only, w.new_only, c.caption, c.ftype, c.value, c.size, c.maxlength, c.tooltip, w.section, w.id_category 
			FROM #__support_wk_fields as w, #__support_custom_fields as c 
			WHERE w.id_field=c.id AND w.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' 
			ORDER BY w.section, w.ordering";
    $database->setQuery($sql);
    $customfields = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';

    // Build Status select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY `ordering`, `description`";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
    $lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'id_status', ' style="z-index:900;"', 'value', 'text', HelpdeskStatus::GetDefault());

    // Build Priority select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority WHERE `show`=1 ORDER BY description";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
    $lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'id_priority', ' id="id_priority" onchange="DueDatePonderado();"', 'value', 'text', ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'id_priority') : HelpdeskPriority::GetDefault()));

    // Build Client select list
    $sql = "SELECT c.`id` AS value, c.`clientname` AS text FROM `#__support_client` AS c LEFT JOIN `#__support_client_wk` AS w ON w.id_client=c.id AND (w.id_workgroup=0 OR w.id_workgroup='" . $id_workgroup . "') WHERE block='0' ORDER by clientname";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);

    // Build the Travel yes/no list
    $lists['travel'] = '<input type="radio" id="travel_time0" name="travel_time" value="0" class="inputbox" onclick="SetTravelTime();" checked /> ' . JText::_('MQ_NO') . ' <input type="radio" id="travel_time1" name="travel_time" value="1" class="inputbox" onclick="SetTravelTime();" /> ' . JText::_('MQ_YES');
    $lists['task_travel'] = '<input type="radio" id="tasktravel0" name="tasktravel" value="0" class="inputbox" onclick="SetTravelTime();" checked /> ' . JText::_('MQ_NO') . ' <input type="radio" id="tasktravel1" name="tasktravel" value="1" class="inputbox" onclick="SetTravelTime();" /> ' . JText::_('MQ_YES');

    // Get Activity Rate Default
    $database->setQuery("SELECT id FROM #__support_activity_rate WHERE isdefault='1'");
    $default_rate = $database->loadResult();

    // Build Activity Rate select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_rate WHERE published='1' ORDER BY description";
    $database->setQuery($sql);
    $rows_actrate = $database->loadObjectList();

    $lists['activity_rate'] = JHTML::_('select.genericlist', $rows_actrate, 'id_activity_rate', '', 'value', 'text', $default_rate);
    $lists['task_rate'] = JHTML::_('select.genericlist', $rows_actrate, 'activity_rate', '', 'value', 'text', $default_rate);

    // Get Activity Type Default
    $database->setQuery("SELECT id FROM #__support_activity_type WHERE isdefault='1'");
    $default_type = $database->loadResult();

    // Build Activity Type select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_type WHERE published='1' ORDER BY description";
    $database->setQuery($sql);
    $rows_acttype = $database->loadObjectList();

    $lists['activity_type'] = JHTML::_('select.genericlist', $rows_acttype, 'id_activity_type', '', 'value', 'text', $default_type);
    $lists['task_type'] = JHTML::_('select.genericlist', $rows_acttype, 'activity_type', '', 'value', 'text', $default_type);

    // Build Assign To select list
    if ($is_support) {
        $sql = "SELECT DISTINCT(u.`id`) AS value, CONCAT(u.`name`, ' ', IF(p.`level` > 0, '(', ''), IF(p.`level` > 0, '" . JText::_('support_level') . " ', ''), IF(p.`level` > 0, p.`level`, ''), IF(p.`level` > 0, ')', '')) AS text 
				FROM #__users as u, #__support_permission as p 
				WHERE u.id=p.id_user AND p.id_workgroup='" . $id_workgroup . "' " . ($supportOptions->manager ? '' : 'AND u.id=' . $user->id) . " 
				ORDER BY u.name";
        $database->setQuery($sql);
        $assign_wk = $database->loadObjectList();
        $assign_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $assign_wk);
        $lists['assign'] = JHTML::_('select.genericlist', $assign_wk, 'assign_to', 'id="assign_to"', 'value', 'text', ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'source') : $user->id));

        // Build the Source select list
        $sourcelist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
        $sourcelist[] = JHTML::_('select.option', 'F', JText::_('fax'));
        $sourcelist[] = JHTML::_('select.option', 'P', JText::_('phone'));
        $sourcelist[] = JHTML::_('select.option', 'M', JText::_('email'));
        $sourcelist[] = JHTML::_('select.option', 'W', JText::_('website'));
        $sourcelist[] = JHTML::_('select.option', 'O', JText::_('other'));
        $lists['source'] = JHTML::_('select.genericlist', $sourcelist, 'source', '', 'value', 'text', ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'source') : $supportConfig->default_source));
    } else {
        $lists['source'] = '';
        $lists['assign'] = '';
    }

    $clientrate = 0;
    $clientvalue = 0;
    $clienttravel = '00:00';

    // Build workgroup categories select list
    $lists['category'] = HelpdeskForm::BuildCategories(($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'id_category') : $id_category), false, true, false, false);

	// Internal ticket
	$lists['internal'] = '<input type="radio" id="internal0" name="internal" value="0" class="inputbox" checked /> ' . JText::_('MQ_NO') . ' <input type="radio" id="internal1" name="internal" value="1" class="inputbox" /> ' . JText::_('MQ_YES');

    // Custom fields
    $i = 1;
    $cfields_hiddenfield = "";
    $j = 1;

    foreach ($customfields as $key2 => $value2) {
        $fid = 0;
        $ftype = '';
        $fvalue = '';
        $fsize = '';
        $flength = '';

        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $cfields_rows[$i][$key3] = $value3;
                if ($key3 == 'id')
                    $fid = $value3;
                if ($key3 == 'ftype')
                    $ftype = $value3;
                if ($key3 == 'value')
                    $fvalue = $value3;
                if ($key3 == 'size')
                    $fsize = $value3;
                if ($key3 == 'maxlength')
                    $flength = $value3;
                if ($key3 == 'support_only')
                    $fsupportonly = $value3;
                if ($key3 == 'tooltip')
                    $ftooltip = $value3;
            }
        }

        if ((!$is_support) && ($fsupportonly > 0)) {
            if ($fsupportonly == 2) {
                $cfields_hiddenfield .= HelpdeskForm::WriteField(0, $fid, 'hidden', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                unset($cfields_rows[$i]); // remove from array
            } else { // 0 or 1
                $cfields_rows[$i]['field'] = HelpdeskForm::WriteField(0, $fid, 'readonly', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                if ($j / 2 == round($j / 2)) {
                    $cfields_rows[$i]['case'] = 1;
                } else {
                    $cfields_rows[$i]['case'] = 0;
                }
                $j++;
            }
        } else {
            $exclude = (($fsupportonly && !$is_support)) ? 1 : 0;
            $cfields_rows[$i]['field'] = HelpdeskForm::WriteField(0, $fid, $ftype, $fvalue, $fsize, $flength, 0, 0, $exclude, 0, 0, $ftooltip);
            if ($j / 2 == round($j / 2)) {
                $cfields_rows[$i]['case'] = 1;
            } else {
                $cfields_rows[$i]['case'] = 0;
            }
            $j++;
        }
        $i++;
    }

    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    // Attachments
    for ($i = 1; $i <= $supportConfig->attachs_num; $i++) {
        $attachs[$i]['number'] = $i;

        if ($is_support) {
            $attachs[$i]['available'] = '<input type="radio" id="available' . $i . '0" name="available' . $i . '" value="0" class="inputbox" /> ' . JText::_('MQ_NO') . ' <input type="radio" id="available' . $i . '1" name="available' . $i . '" value="1" class="inputbox" checked /> ' . JText::_('MQ_YES');
        } else {
            $attachs[$i]['available'] = '<input type="hidden" name="available' . $i . '" value="1" />';
        }
    }

    // Writes the Custom Fields validation
    $html1 = "\n";
    $x = 0;
    for ($x; $x < count($customfields); $x++) {
        $cfrow = $customfields[$x];

        // Validate category
        $category_check = '';
        if ($cfrow->id_category != '') {
            if (strpos($cfrow->id_category, ',') === false) {
                $category_check = '&& $jMaQma(\'#id_category\').val()==' . $cfrow->id_category;
            } else {
                $category_check = '&& ( ';
                $categories = explode(",", $cfrow->id_category);
                for ($z = 0; $z < count($categories); $z++) {
                    $category_check .= '$jMaQma(\'#id_category\').val()==' . $categories[$z] . ' || ';
                }
                $category_check = JString::substr($category_check, 0, -3);
                $category_check .= ')';
            }
        }

        if ($cfrow->required == 1 && $cfrow->ftype != 'htmleditor' && $cfrow->ftype != 'radio' && $cfrow->ftype != 'checkbox') {
            $html1 .= "value2 = document.adminForm.custom" . $cfrow->id_field . ".value;\n";
            $html1 .= "if( value2 == '' " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    document.adminForm.custom" . $cfrow->id_field . ".focus();\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        } elseif ($cfrow->required == 1 && $cfrow->ftype == 'checkbox') {
            $fieldval = '';
            $fieldoptions = explode(',', $cfrow->value);
            for ($y = 0; $y < count($fieldoptions); $y++) {
                $fieldval .= '$jMaQma("#custom' . $cfrow->id_field . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', $fieldoptions[$y]) . '").is(":checked")==false && ';
            }
            $fieldval = JString::substr($fieldval, 0, strlen($fieldval) - 4);
            $html1 .= "if( " . $fieldval . " " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        } elseif ($cfrow->required == 1 && $cfrow->ftype == 'radio') {
            $fieldval = '';
            $fieldoptions = explode(',', $cfrow->value);
            for ($y = 0; $y < count($fieldoptions); $y++) {
                $fieldval .= '$jMaQma("#custom' . $cfrow->id_field . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', $fieldoptions[$y]) . '").is(":checked")==false && ';
            }
            $fieldval = JString::substr($fieldval, 0, strlen($fieldval) - 4);
            $html1 .= "if( " . $fieldval . " " . $category_check . " ) {\n";
            $html1 .= "    alert('" . $cfrow->caption . JText::_('tmpl_msg07') . "');\n";
            $html1 .= "    return false;\n";
            $html1 .= "}\n";
        }
    }

    $document->addScriptDeclaration("function CustomFieldsValidation() { $html1 return true; }");

    $acttyperate_hover_tip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('acttyperate_hover_subj') . '::' . JText::_('acttyperate_hover_tip')) . '"> <img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/config.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('acttyperate_hover_subj') . '</b></span>';
    $start_times_hover_tip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('start_times_hover_subj') . '::' . JText::_('start_times_hover_tip')) . '"> <img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/time.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('start_times_hover_subj') . '</b></span>';
    $tmpl_msg21 = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('tmpl_msg22') . '::' . JText::_('tmpl_msg21')) . '"> <img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/car.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /><b>' . JText::_('tmpl_msg22') . '</b></span>';

    $imgpath = JURI::root() . 'components/com_maqmahelpdesk/images/';
    $duedate_default = HelpdeskTicket::ReturnDueDate(date("Y"), date("m"), date("d"), date("H"), date("i"), HelpdeskPriority::GetDefault());
    $duedate_date = JString::substr($duedate_default, 0, 10);
    $duedate_hour = JString::substr($duedate_default, 11, 4) . '0';

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('tickets/add_ticket_' . ($is_support ? 'support' : 'customer'));
    include $tmplfile;
}

function createUser($name, $email, $password)
{
    $database = JFactory::getDBO();
    $CONFIG = new JConfig();
    $mailer = JFactory::getMailer();

    $salt = md5(rand(100, 999));
    $pass = md5($password . $salt) . ':' . $salt;

    $sql = sprintf("INSERT INTO `#__users`(`name`, `username`, `email`, `password`, `registerDate`)
                    VALUES('%s', '%s', '%s', '%s', '" . date("Y-m-d H:i:s") . "')",
        $name,
        $email,
        $email,
        $pass);
    $database->setQuery($sql);
    $database->query();
    $id_user = $database->insertid();

    $sql = "INSERT INTO `#__user_usergroup_map`(`user_id`, `group_id`)
            VALUES('" . $id_user . "', '2')";
    $database->setQuery($sql);
    $database->query();

    $body_html = "<p>Welcome " . $name . ",</p>
	<p>Your account has been activated with the following details:</p>
	<p>Username : " . $email . " <br />Password : " . $password . " </p>
	<p>Kind Regards, <br />
	" . $CONFIG->sitename;

    $subject = 'New User Details';
    $adminName2 = $CONFIG->fromname;
    $adminEmail2 = $CONFIG->mailfrom;

    $mailer->addRecipient($email);
    $mailer->setSender($adminEmail2, $adminName2);
    $mailer->setSubject($subject);
    $mailer->setBody($body_html);
    $mailer->IsHTML(true);
    $sendmail = $mailer->Send();

    return $id_user;
}

function saveTicket($problem, $reply)
{
    global $task, $filter_client, $filter_user, $filter_search, $is_manager;

    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $userregister = JRequest::getVar('userregister', 0, '', 'int');
    $internal = JRequest::getVar('internal', 0, '', 'int');
    JRequest::checkToken() or jexit('FALSE|Invalid Token');

    $CONFIG = new JConfig();
    $supportConfig = HelpdeskUtility::GetConfig();

    // Initialise variables
    $wkoptions = null;

    // Get Workgroup Options
    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    // Gather minimum required values
    HelpdeskStatus::GetDefault() ? $status_id = HelpdeskStatus::GetDefault() : $status_id = JRequest::getVar('id_status', '', 'POST', 'string');
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $id_user = JRequest::getInt('id_user', 0);
    $id_client = JRequest::getInt('id_client', 0);
    $username = JRequest::getVar('username', '', 'POST', 'string');
    $usermail = JRequest::getVar('usermail', '', 'POST', 'string');
    $userpassword = JRequest::getVar('userpassword', '', 'POST', 'string');

    // Check if the user is to be created or not
    if (!$id_user && $userregister && $username != '' && $usermail != '' && $userpassword != '') {
        $id_user = createUser($username, $usermail, $userpassword);
    } elseif (!$id_user && !$userregister && $username != '' && $usermail != '') {
        $an_name = $username;
        $an_mail = $usermail;
    }

	$now = HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S");
	$id_category = JRequest::getInt('id_category', 0);
    $id_directory = JRequest::getInt('id_directory', 0);
    $date = $now;
    $subject = stripslashes(JRequest::getVar('subject', '', 'POST', 'string'));
    $message = stripslashes($_POST['problem']);
    /*if (!$is_support) {
        $message = nl2br($message);
    }*/
    $last_update = $now;
    $wkoptions->auto_assign ? $assign_to = $wkoptions->auto_assign : $assign_to = 0;
    $id_priority = intval(JRequest::getVar('id_priority', '', 'POST', 'string'));
    $source = HelpdeskTicket::VerifySource(JRequest::getVar('source', '', 'POST', 'string'));
    if ($id_user)
    {
        $an_name = HelpdeskUser::GetName($id_user);
        $an_mail = HelpdeskUser::GetEmail($id_user);
    }
    $duedate = JRequest::getVar('duedate_date', '', 'POST', 'string') . ' ' . JRequest::getVar('duedate_hours', '', 'POST', 'string');
    !$duedate ? $duedate = HelpdeskTicket::ReturnDueDate(JString::substr($date, 0, 4), JString::substr($date, 5, 2), JString::substr($date, 8, 2), JString::substr($date, 11, 2), JString::substr($date, 14, 2), $id_priority) : '';

    // Get Ticket Categories
	//$category_id_new = intval( mosGetParam( $_POST, 'id_category', $ticket->id_category ) );
	$category_id_new = intval(JRequest::getVar('id_category', $ticket->id_category, 'POST', 'int'));
	$category_id_old = $ticket->id_category;

	// Get Ticket Status
	$status_id_old = $ticket->id_status;
	$status_id_new = intval(JRequest::getVar('id_status', $ticket->id_status, 'POST', 'int'));

	// Get Assigned New and Old User
	$assigned_id_old = 0;
	$assigned_name_old = '';
	if ($is_support)
	{
		// By default it's the user itself but with permissions can be other
		$assigned_id_new = intval(JRequest::getVar('assign_to', 0, 'POST', 'int'));
		// Get support agent details
		$database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_new'");
		$assigned_new = $database->loadObject();
	}
	else
	{
		// Verify if there is a client assignment
		$sql = "SELECT `autoassign`
				FROM `#__support_client`
				WHERE `id`=" . $id_client;
		$database->setQuery($sql);
		$assigned_id_new = (int) $database->loadResult();
		// Verify if there's a category + city assignment
		$sql = "SELECT `city`
				FROM `#__support_users`
				WHERE `id_user`=" . $id_user;
		$database->setQuery($sql);
		$city = $database->loadResult();
		if (!$assigned_id_new) {
			$sql = "SELECT `id_user`
					FROM `#__support_workgroup_category_assign`
					WHERE `id_workgroup`=$id_workgroup
					  AND `id_category`=$id_category
					  AND `city`='$city'";
			$database->setQuery($sql);
			$assigned_id_new = (int) $database->loadResult();
		}
		// Verify if there's a category only assignment
		if (!$assigned_id_new) {
			$sql = "SELECT `id_user`
					FROM `#__support_workgroup_category_assign`
					WHERE `id_workgroup`=$id_workgroup
					  AND `id_category`=$id_category
					  AND `city`=''";
			$database->setQuery($sql);
			$assigned_id_new = (int) $database->loadResult();
		}
		// Verify if there's a city only assignment
		if (!$assigned_id_new && $city != '') {
			$sql = "SELECT `id_user`
					FROM `#__support_workgroup_category_assign`
					WHERE `id_workgroup`=$id_workgroup
					  AND `id_category`=0
					  AND `city`='$city'";
			$database->setQuery($sql);
			$assigned_id_new = (int) $database->loadResult();
		}
		// Verify if there's a workgroup default assignment
		if (!$assigned_id_new) {
			$assigned_id_new = JRequest::getInt('assign_to', 0);
		}
		// Get support agent details
		$database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_new'");
		$assigned_new = $database->loadObject();
	}
	$assign_to = $assigned_id_new ? $assigned_id_new : $wkoptions->auto_assign;

    // avoid double ticket creation
    $sql = "SELECT id 
			FROM #__support_ticket 
			WHERE subject=" . $database->quote($subject) . "
			  AND SUBSTRING(date,1,10)='" . JString::substr($date, 0, 10) . "'
			  AND ipaddress='" . HelpdeskUser::GetIP() . "'
			  AND assign_to='" . $assign_to . "'
			  AND id_workgroup='" . $id_workgroup . "'
			LIMIT 1";
    $database->setQuery($sql);
    $check_double_submition = $database->loadResult();
    if ($check_double_submition > 0)
    {
        HelpdeskUtility::AddGlobalMessage(JText::_('alert_double_form_submition'), 'e');
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
        exit();
    }

    // If ticket created by support agent add note about it
    if ($is_support)
    {
        $message .= '<p class="text-info"><i>' . sprintf(JText::_('IN_BEHALF_USER'), $user->name) . '</i></p>';
    }

	// Get client id if the connected user if from support
	$sql = "SELECT cu.id_client, c.approval, c.`manager`
			FROM #__support_client_users AS cu
				 INNER JOIN #__support_client AS c ON c.id = cu.id_client
			WHERE cu.id_user=" . $id_user;
	$database->setQuery($sql);
	$client_details = $database->loadObject();

	// Mark if approved
	$approved = 1;
	if ($is_client && $client_details->approval && !$is_manager)
	{
		$approved = 0;
	}

    //////////////////////////////////////////////////
    // Insert basic ticket and set ticket number	//
    //////////////////////////////////////////////////
    $sql = "INSERT INTO #__support_ticket(id_workgroup, id_status, id_user, id_category, date, subject, message, last_update, assign_to, id_priority, source, an_name, an_mail, duedate, day_week, id_client, ipaddress, id_directory, approved, internal)
            VALUES('" . $id_workgroup . "', '" . $status_id . "', '" . $id_user . "', '" . $id_category . "', '" . $date . "', " . $database->quote($subject) . ", " . $database->quote($message) . ", '" . $last_update . "', '" . $assign_to . "', '" . $id_priority . "', '" . $source . "', " . $database->quote($an_name) . ", " . $database->quote($an_mail) . ", '$duedate', '" . HelpdeskDate::DateOffset("%w") . "', '" . $id_client . "', '" . HelpdeskUser::GetIP() . "', " . $id_directory . ", " . $approved . ", " . $internal . ")";
    $database->setQuery($sql);
    $database->query();

    if ($database->getErrorMsg() != '') {
        HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
        exit();
    } else {
        $ticket_id = $database->insertid();

        $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'");
        $ticket = $database->loadObject();

        if ($supportConfig->tickets_numbers) {
            $ticketmask = rand(10, 99) . $ticket_id . rand(1000, 9999);
        } else {
            $ticketmask = $ticket_id;
        }
        $database->setQuery("UPDATE #__support_ticket SET ticketmask='" . $ticketmask . "' WHERE id='" . $ticket_id . "'");
        if (!$database->query()) {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
            exit();
        }

        $url_view_ticket = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket_id, false);
        $url_new_ticket = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_new', false);
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('ticket_create_sucess'), $ticketmask, $url_view_ticket, $url_new_ticket), 'i');
    }

    //////////////////////////////////////
    //	Update Tickets Custom Fields	//
    //////////////////////////////////////
    $database->setQuery("SELECT f.id_field, c.caption, c.ftype FROM #__support_wk_fields AS f INNER JOIN #__support_custom_fields AS c ON c.id=f.id_field WHERE f.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' ORDER BY f.ordering");
    $customfields = $database->loadObjectList();
    /*if( !$database->query() ) {
		HelpdeskUtility::AddGlobalMessage( JText::_('tkt_dberror').'<br />'.$database->getErrorMsg() , 'e', $database->stderr(1) );
	}*/

    $x = 0;
    $database->setQuery("DELETE FROM #__support_field_value WHERE id_ticket='" . $ticket_id . "'");
    $database->query();
    $cfields_array = array();
    for ($x = 0; $x < count($customfields); $x++) {
        $ticketField = $customfields[$x];

        if (($ticketField->ftype == "checkbox")) {
            $custom_val2 = serialize(JRequest::getVar('custom' . $ticketField->id_field, '', '', 'array'));
            $custom_val = unserialize($custom_val2);
            if (is_array($custom_val)) {
                $tmp_custom_val = "";
                for ($t = 0; $t < sizeof($custom_val); $t++) {
                    $tmp_custom_val .= $custom_val[$t] . ",";
                }
                $custom_val = JString::substr($tmp_custom_val, 0, strlen($tmp_custom_val) - 1);
            }
        } else {
            $custom_val = JRequest::getVar('custom' . $ticketField->id_field, '', '', 'string');
	        $custom_val = str_replace('"', '', $custom_val);
	        $custom_val = stripslashes($custom_val);
        }

        $cfields_array_tmp = array('[cfield' . $ticketField->id_field . '_caption]' => $ticketField->caption,
            '[cfield' . $ticketField->id_field . '_value]' => HelpdeskUtility::String2HTML(($is_support ? $custom_val : nl2br($custom_val))));
        $cfields_array = array_merge($cfields_array, $cfields_array_tmp);
        $database->setQuery("INSERT INTO #__support_field_value(id_field, id_ticket, newfield) VALUES('" . $ticketField->id_field . "', '" . $ticket_id . "', " . $database->quote($custom_val) . ")");

        if (!$database->query())
        {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        }
    }

    //////////////////////////////////////
    //	Full Ticket Creation Processing	//
    //////////////////////////////////////

    // Intialise variables
    $msg = '';
    $ticket = null;
    $userinfo = null;
    $managersInfo = null;
    $assigned = null;
	$attachments = null;

    // Get Ticket Details
    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'");
    $ticket = $database->loadObject();

    // Get Ticket Users Details
    $database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->id_user . "'");
    $userinfo = $database->loadObject();

    // Get Ticket Users Client ID
    JRequest::getVar('id_client', '', 'POST', 'string') ? $client_id = JRequest::getVar('id_client', '', 'POST', 'int') : $client_id = HelpdeskClient::GetIDByUser($ticket->id_user);

    // Get Ticket Users Client Name
    $client_name = HelpdeskClient::GetName($ticket->id_user);

    // Get Ticket Client Manager
    if ($client_id > 0) {
        $database->setQuery("SELECT u.email FROM #__support_client_users c, #__users u WHERE c.id_user=u.id AND c.id_client='" . $client_id . "' AND c.manager='1'");
        $managersInfo = $database->loadObjectList();
    }

    // Get Ticket Source Name
    $source_name = HelpdeskTicket::SwitchSource($ticket->source);

    // Set URL for this ticket
    $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);

    // Get Other Form Variables
    $replytime = JRequest::getVar('replytime', '0:00', 'POST', 'string');
    $travel_time = JRequest::getVar('travel_time', '0:00', 'POST', 'string');
    $tickettravel = JRequest::getVar('tickettravel', '0:00', 'POST', 'string');
    //$clientrate   		= JRequest::getVar( 'clientrate', '0', 'POST', 'string' );
    $user_rate = JRequest::getVar('clientrate', '0', 'POST', 'string');
    $id_activity_rate = JRequest::getVar('id_activity_rate', '0', 'POST', 'string');
    $id_activity_type = JRequest::getVar('id_activity_type', '', 'POST', 'string');
    $start_time = JRequest::getVar('start_time', '0:00', 'POST', 'string');
    $end_time = JRequest::getVar('end_time', '0:00', 'POST', 'string');
    $break_time = JRequest::getVar('break_time', '0:00', 'POST', 'string');
	$now_date = HelpdeskDate::DateOffset("%Y-%m-%d");
	$now_hours = HelpdeskDate::DateOffset("%H:%M:%S");
	$reply_date = JRequest::getVar('reply_date', $now_date, '', 'string');
	$reply_hours = JRequest::getVar('reply_hours', $now_hours, '', 'string');
    $predefined_subject = stripslashes(JRequest::getVar('replysubject', '', 'POST', 'string'));
    $reply_summary_msg = stripslashes(JRequest::getVar('reply_summary', '', 'POST', 'string'));

    //////////////////////////
    //	Update Contracts	//
    //////////////////////////
    $contract = HelpdeskContract::Get($ticket->id_user);
    if (isset($contract)) {
        if ($contract != false) {
            switch ($contract->unit) {
                // If contract is by number of Tickets then only updates it at ticket creation
                case 'T' :
                    $database->setQuery("UPDATE #__support_contract SET actual_value=(actual_value+1) WHERE id='" . $contract->id . "'");
                    if (!$database->query()) {
                        HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
                        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
                        exit();
                    }
            }
            HelpdeskContract::MakeInactive($contract->id);
        }
    }

    //////////////////////////////////
    // Calls the add-on's engine	//
    //////////////////////////////////
    HelpdeskAddon::Execute(2, 1, $ticket_id);

    // Set Email Notify Template variables
    $var_set = array('[duedate]' => $ticket->duedate,
        '[duedate_old]' => '',
        '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
        '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
        '[number]' => $ticket->ticketmask,
        '[assign]' => HelpdeskUtility::String2HTML((isset($assigned_new) ? $assigned_new->name : '')),
        '[assign_email]' => (isset($assigned_new) ? $assigned_new->email : ''),
        '[unassigned]' => '',
        '[unassigned_email]' => '',
        '[subject]' => HelpdeskUtility::String2HTML($ticket->subject),
        '[message]' => HelpdeskUtility::String2HTML($ticket->message),
        '[summary]' => HelpdeskUtility::String2HTML($reply_summary_msg),
        '[author]' => HelpdeskUser::GetName($user->id),
        '[recipient]' => '',
        '[email]' => $userinfo->email,
        '[client]' => HelpdeskUtility::String2HTML($client_name),
        '[url]' => $ticket_url,
        '[department]' => HelpdeskUtility::String2HTML($wkoptions->wkdesc),
        '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($id_priority)),
        '[priority_old]' => '',
        '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
        '[status_old]' => '',
        '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_new)),
        '[category_old]' => '',
        '[source]' => HelpdeskUtility::String2HTML($source),
        '[helpdesk]' => JURI::root()
    );
    $var_set = array_merge($var_set, $cfields_array);

    // Add ticket log message
    HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('ticket_created')), JText::_('ticket_created_hidden'), $ticket->id_status, 'status', $status_id_new, 0, 'add_ticket.png');

    // Screenr integration
    if ($supportConfig->screenr_account!='' && $supportConfig->screenr_api_id!='')
    {
        $screenr_url = JRequest::getVar('screenr_url', '', '', 'string');
        $screenr_embedurl = JRequest::getVar('screenr_embedurl', '', '', 'string');
        $screenr_thumbnailurl = JRequest::getVar('screenr_thumbnailurl', '', '', 'string');
        $screenr_id = JRequest::getVar('screenr_id', '', '', 'string');

        if ($screenr_url!='' && $screenr_embedurl!='' && $screenr_thumbnailurl!='' && $screenr_id!='')
        {
            // Insert in database
            $sql = "INSERT INTO `#__support_ticket_screenr`(`id_user`, `id_ticket`, `id_reply`, `id_screen`, `url`, `embedurl`, `thumbnailurl`)
                        VALUES({$user->id}, $ticket_id, 0, " . $database->quote($screenr_id) . ", " . $database->quote($screenr_url) . ", " . $database->quote($screenr_embedurl) . ", " . $database->quote($screenr_thumbnailurl) . ")";
            $database->setQuery($sql);
            $database->query();

            // Insert into log
            HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('screencast_added')), JText::_('screencast_added_hidden'), 0, '', 0, 0, 'add_screencast.png');
        }
    }

    //	Save Ticket Reply
    if ($reply != '' || $reply_summary_msg != '') {
        if (HelpdeskTicket::Reply($ticket->id, $reply_summary_msg, $reply, $replytime, $travel_time, $tickettravel, $user_rate, $id_activity_rate, $id_activity_type, $start_time, $end_time, $break_time, $reply_date, $reply_hours)) {
            // Add ticket log message
            HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('posted_reply')), JText::_('posted_reply_customer'), $ticket->id_status, '', 0, 0, 'add_message.png');
        }
    }

    // Ticket Status
    if ($status_id_old != $status_id_new) {
        // Update Ticket Status
        $database->setQuery("UPDATE #__support_ticket SET id_status='" . $status_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query()) {
            // Ticket log
            $ticketLogMsg = JText::_('changed_status');
            $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), $ticketLogMsg);
            $ticketLogMsg = str_replace('%2', HelpdeskStatus::GetName($status_id_old), $ticketLogMsg);
            $ticketLogMsg = str_replace('%3', HelpdeskStatus::GetName($status_id_new), $ticketLogMsg);
            HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_status_hidden'), $status_id_new, 'status', $status_id_new, 0, 'change.png');
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        }
    }

    // Attached File
    for ($xx = 1; $xx <= $supportConfig->attachs_num; $xx++)
    {
        if (isset($_FILES['file' . $xx]))
        {
            if ($_FILES['file' . $xx]['name'] != '')
            {
                $fileupload = HelpdeskFile::Upload($ticket->id, 'T', "file$xx", $supportConfig->docspath, $_POST['desc' . $xx], 0, (int) $_POST['available' . $xx]);
                if ($fileupload)
                {
	                $attachments[] = $fileupload;
                    $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('attached_file'));
                    $ticketLogMsg = str_replace('%2', $_FILES['file' . $xx]['name'], $ticketLogMsg);
                    HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('attached_file_hidden'), '', 'attachfile', 0, 0, 'add_attach.png');
                    HelpdeskUtility::AddGlobalMessage(JText::_('upload_ok'), 'i');
                }
            }
        }
    }

    // Send notifications
    $cc_report = JRequest::getVar('cc_email_address', null, 'POST', 'array');
    $bcc_report = JRequest::getVar('bcc_email_address', null, 'POST', 'array');

    /*echo "<p>CC como veio do formulario: ";print_r($cc_report);
echo "<p>BCC como veio do formulario: ";print_r($bcc_report);

echo "<hr>";
echo "<p>Utilizador do suporte: ".($is_support?'SIM':'NAO');
echo "<p>Tem CCs: ".(count($cc_report)?'SIM':'NAO');
echo "<p>Tem BCCs: ".(count($bcc_report)?'SIM':'NAO');*/

    // Notify Workgroup Administrator
    if ($wkoptions->wkemail && $wkoptions->wkadmin_email) {
        //echo "<p>Admin do workgroup vai em BCC: ".$wkoptions->wkemail." / ".$wkoptions->wkadmin_email;
        $bcc_report[] = $wkoptions->wkadmin_email;
        //echo "<p>Lista BCC: ";
        //print_r($bcc_report);
    }
    // Notify System Administrator
    if ($supportConfig->receive_mail) {
        //echo "<p>Admin do site vai em BCC: ".$CONFIG->mailfrom;
        $bcc_report[] = $CONFIG->mailfrom;
        //echo "<p>Lista BCC: ";
        //print_r($bcc_report);
    }
    // Notify Client Manager
    if ($client_id > 0 && HelpdeskClient::isNotifyClientMgr($client_id) && isset($managersInfo) && $wkoptions->tkt_crt_nfy_mgr) {
        //echo "<p>Client manager coloca em CC: ".implode(',',$managersInfo);
        for ($i = 0; $i < count($managersInfo); $i++) {
            $cc_report[] = $managersInfo[$i]->email;
        }
        //$cc_report = array_merge( $cc_report, $managersInfo );
        //echo "<p>Lista CC: ";
        //print_r($cc_report);
    }
    // Extra client notification
    $database->setQuery("SELECT c.client_mail_notify FROM #__support_client_users u, #__support_client c WHERE u.id_user='" . $user->id . "' AND c.id=u.id_client");
    $contact_list_notify = $database->loadResult();
    if ($contact_list_notify != '') {
        //echo "<p>Extra notificaoes de cliente: ".$contact_list_notify;
        $client_notify_list = explode(";", $contact_list_notify);
        array_walk($client_notify_list, 'trim');
        foreach ($client_notify_list as $contact) {
            $cc_report[] = trim($contact[1]);
        }
        //echo "<p>Lista CC: ";
        //print_r($cc_report);
    }

    // Send email
    $var_set['[recipient]'] = $ticket->an_name;
	if (!$internal)
	{
		SendMailNotification($ticket->id, $var_set, $ticket->an_mail, 'created_mail_subject', 'created_mail_notify_confirmation', 'ticket_create_mail_notify_confirmation', $cc_report, $bcc_report, $attachments);
	}

    // Ticket Assignment
    //echo "<p>Assignment: $assigned_id_old / $assigned_id_new";
    if ($assigned_id_old != $assigned_id_new && $assigned_id_new != $user->id)
    {
        $database->setQuery("UPDATE #__support_ticket SET assign_to='" . $assigned_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query()) {
            $sql = "SELECT assign_report_users FROM `#__support_permission` WHERE id_user = '" . $assigned_id_new . "' AND id_workgroup = '" . $id_workgroup . "' LIMIT 1 ";
            $database->setQuery($sql);
            $additional_users_notify = $database->loadResult();
            $mailcc = array();
            if ($additional_users_notify != '') {
                $users_report_additional = explode('#', $additional_users_notify);
                $usercount = count($users_report_additional);
                if ($usercount > 0) {
                    for ($i = 0; $i < $usercount; $i++) {
                        $database->setQuery("SELECT name, email FROM #__users WHERE id = '" . $users_report_additional[$i] . "' ");
                        $additional_user = $database->loadObject();
                        $mailcc[] = $additional_user->email;
                    }
                }
            }
            if ($wkoptions->tkt_asgn_new_asgn)
            {
                $var_set['[recipient]'] = $assigned_new->name;
	            if (!$internal)
	            {
                    SendMailNotification($ticket->id, $var_set, $assigned_new->email, 'created_mail_subject', 'created_mail_notify_support', 'ticket_create_mail_notify_support', $mailcc, null, $attachments);
	            }
                if ($supportConfig->sms_assign)
                {
                    HelpdeskSMS::SendSMS(sprintf(JText::_('SMS_ASSIGN_MESSAGE'), $ticket->subject), JText::_('ASSIGNMENT'), $ticket->id);
                }
            }
        }

        HelpdeskTicket::Log($ticket->id, str_replace('%1', $assigned_new->name, JText::_('ticket_assigned')), JText::_('assigned_hidden'), $ticket->id_status, 'assign', $assigned_id_new, 0, 'add_assign.png'); // Assigned Ticket log msg
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . $msg . '&start=1', false);

    $mainframe->redirect($url);
}


function saveReply()
{
    global $filter_client, $filter_user, $filter_search;

    $CONFIG = new JConfig();
    $session = JFactory::getSession();
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $mailer = JFactory::getMailer();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $is_support = HelpdeskUser::IsSupport();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $id = JRequest::getInt('id', 0);
    $id_user = JRequest::getInt('id_user', 0);
    $internal = JRequest::getInt('internal', 0);
	$orderby = JRequest::getVar('orderby', 't.last_update', '', 'str');
	$order = JRequest::getVar('order', 'DESC', '', 'str');
    JRequest::checkToken() or jexit('FALSE|Invalid Token');

    // Initialise variables
    $msg = '';
    $wkoptions = null;
    $ticket = null;
    $userinfo = null;
    $managersInfo = null;
    $assigned = null;
    $errors = 0;
	$attachments = null;

	// Update internal
	$sql = "UPDATE `#__support_ticket`
			SET `internal`=$internal
			WHERE `id`=$id";
	$database->setQuery($sql);
	$database->query();

    // Get Workgroup Options
    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    // Verify if there was a client change / if client is to be created / if user is to be created
    if ($is_support)
    {
        // Verify is user is to be created
        $username = JRequest::getVar('username', '', '', 'string');
        $usermail = JRequest::getVar('usermail', '', '', 'string');
        $userpassword = JRequest::getVar('userpassword', '', '', 'string');
        if ($username != '' && $usermail != '' && $userpassword != '') {
            //echo "<p>Creates new user: ";
            $salt = md5(rand(100, 999));
            $pass = md5($userpassword . $salt) . ':' . $salt;

            $sql = sprintf("INSERT INTO `#__users`(`name`, `username`, `email`, `password`, `registerDate`)
							VALUES('%s', '%s', '%s', '%s', '" . date("Y-m-d H:i:s") . "')",
                $username,
                $usermail,
                $usermail,
                $pass);
            $database->setQuery($sql);
            $database->query();
            $id_user = $database->insertid();

            $sql = "INSERT INTO `#__user_usergroup_map`(`user_id`, `group_id`)
					VALUES('" . $id_user . "', '2')";
            $database->setQuery($sql);
            $database->query();

            // Update ticket
            $sql = "UPDATE `#__support_ticket`
					SET `id_user`=$id_user
					WHERE `id`=$id";
            $database->setQuery($sql);
            $database->query();

            // Send e-mail to user
            $body_html = "<p>Welcome " . $username . ",</p>
						  <p>Your account has been activated with the following details:</p>
						  <p>E-mail : " . $usermail . " <br />Username : " . $usermail . " <br />Password : " . $userpassword . " </p>
						  <p>Kind Regards, <br />" . $CONFIG->sitename;
            $mailer->addRecipient($usermail);
            $mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
            $mailer->setSubject('New User Details');
            $mailer->setBody($body_html);
            $mailer->IsHTML(true);
	        if (!$internal)
	        {
                $sendmail = $mailer->Send();
	        }
            //echo "<p>Email notification to user:<br>".$body_html;
        }

        // Verify if there was a client change
        $id_client = JRequest::getInt('id_client', 0);
        $old_client = JRequest::getInt('old_client', 0);
        //echo "<p>id_client: $id_client <br>old_client: $old_client";

        // Verify if client is to be created
        $clientname = JRequest::getVar('clientname', '', '', 'string');
        $clientaddress = JRequest::getVar('clientaddress', '', '', 'string');
        $clientcity = JRequest::getVar('clientcity', '', '', 'string');
        $clientzip = JRequest::getVar('clientzip', '', '', 'string');
        $clientphone = JRequest::getVar('clientphone', '', '', 'string');
        $clientwebsite = JRequest::getVar('clientwebsite', '', '', 'string');
        if ($clientname != '') {
            //echo "<p>Creates new client: ";
            $sql = "INSERT INTO `#__support_client`(`date_created`, `clientname`, `address`, `city`, `zipcode`, `phone`, `website`)
					VALUES('" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', '$clientname', '$clientaddress', '$clientcity', '$clientzip', '$clientphone', '$clientwebsite')";
            $database->setQuery($sql);
            $database->query();
            $id_client = $database->insertid();
            //echo $id_client;

            $sql = "INSERT INTO `#__support_client_wk`(`id_workgroup`, `id_client`)
					VALUES('0', '$id_client')";
            $database->setQuery($sql);
            $database->query();
        }
        if ($id_client != $old_client) {
            $sql = "UPDATE `#__support_ticket`
					SET `id_client`=$id_client
					WHERE `id`=$id";
            $database->setQuery($sql);
            $database->query();

            $sql = "INSERT INTO `#__support_client_users`(`id_client`, `id_user`, `manager`)
					VALUES('$id_client', '$id_user', '1')";
            $database->setQuery($sql);
            $database->query();
        }
    }

    // Get Ticket Details
    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $id . "'");
    $ticket = $database->loadObject();

    //////////////////////////////////////
    //	Update Tickets Custom Fields	//
    //////////////////////////////////////
    $database->setQuery("SELECT f.id_field, c.caption, c.ftype FROM #__support_wk_fields AS f INNER JOIN #__support_custom_fields AS c ON c.id=f.id_field WHERE f.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' ORDER BY f.ordering");
    $customfields = $database->loadObjectList();
    /*if( !$database->query() ) {
		HelpdeskUtility::AddGlobalMessage( JText::_('tkt_dberror').'<br />'.$database->getErrorMsg() , 'e', $database->stderr(1) );
		$errors++;
	}*/

    $x = 0;
    $database->setQuery("DELETE FROM #__support_field_value WHERE id_ticket='" . $ticket->id . "'");
    $database->query();
    $cfields_array = array();
    for ($x = 0; $x < count($customfields); $x++)
    {
        $ticketField = $customfields[$x];

        if (($ticketField->ftype == "checkbox")) {
            $custom_val2 = serialize(JRequest::getVar('custom' . $ticketField->id_field, '', '', 'array'));
            $custom_val = unserialize($custom_val2);
            if (is_array($custom_val)) {
                $tmp_custom_val = "";
                for ($t = 0; $t < sizeof($custom_val); $t++) {
                    $tmp_custom_val .= $custom_val[$t] . ",";
                }
                $custom_val = JString::substr($tmp_custom_val, 0, strlen($tmp_custom_val) - 1);
            }

        } else {
            $custom_val = JRequest::getVar('custom' . $ticketField->id_field, '', '', 'string');
	        $custom_val = str_replace('"', '', $custom_val);
	        $custom_val = stripslashes($custom_val);
        }

        $cfields_array_tmp = array('[cfield' . $ticketField->id_field . '_caption]' => $ticketField->caption,
            '[cfield' . $ticketField->id_field . '_value]' => HelpdeskUtility::String2HTML(($is_support ? $custom_val : nl2br($custom_val))));
        $cfields_array = array_merge($cfields_array, $cfields_array_tmp);
        $sql = "INSERT INTO #__support_field_value(id_field, id_ticket, newfield)
                VALUES('" . $ticketField->id_field . "', '" . $ticket->id . "', " . $database->quote($custom_val) . ")";
	    $database->setQuery($sql);

        if (!$database->query()) {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Get Ticket Users Details
    if ($ticket->id_user) {
        $database->setQuery("SELECT id, name, email FROM #__users WHERE id='" . $ticket->id_user . "'");
        $userinfo = $database->loadObject();
    } else {
        $userinfo->id = 0;
        $userinfo->name = $ticket->an_name;
        $userinfo->email = $ticket->an_mail;
    }

    // Get Ticket Users Client Name
    $client_name = HelpdeskClient::GetName($ticket->id_user);

    // Get Ticket Client Manager
    if ($id_client > 0) {
        $database->setQuery("SELECT u.name, u.email FROM #__support_client_users c, #__users u WHERE c.id_user=u.id AND c.id_client='" . $id_client . "' AND c.manager='1'");
        $managersInfo = $database->loadObjectList();
    }

    // Get Ticket Source
    $source = HelpdeskTicket::SwitchSource($ticket->source);

    // Get Ticket Priority
    $priority_id_old = $ticket->id_priority;
    $priority_id_new = intval(JRequest::getVar('id_priority', $ticket->id_priority, '', 'int'));

    // Get Ticket Status
    $status_id_old = $ticket->id_status;
    $status_id_new = intval(JRequest::getVar('id_status', $ticket->id_status, 'POST', 'int'));

    // Check if auto-change status should be used
    if ($status_id_old == $status_id_new) {
        $sql = "SELECT `id`
				FROM `#__support_status`
				WHERE `auto_status_" . ($is_support ? 'agents' : 'users') . "`=1";
        $database->setQuery($sql);
        $auto_status = $database->loadResult();
        $status_id_new = ($auto_status ? $auto_status : $status_id_new);
    }

    // Get Ticket Categories
    $category_id_new = intval(JRequest::getVar('id_category', $ticket->id_category, 'POST', 'int'));
    $category_id_old = $ticket->id_category;

    // Get Ticket Reply Summary and Message
    $reply_msg = JRequest::getVar('reply', '', 'POST', 'string', 2);
    $reply_summary_msg = JRequest::getVar('reply_summary', '', 'POST', 'string');

    // Get Assigned User
    $assigned_id_old = $ticket->assign_to;
    $assigned_name_old = HelpdeskUser::GetName($assigned_id_old);
    $assigned_id_new = intval(JRequest::getVar('assign_to', $ticket->assign_to, 'POST', 'int'));

    // Auto assign if support reply to unassigned ticket
    if (($reply_msg || $reply_summary_msg) && !$assigned_id_old && !$assigned_id_new && $is_support) {
        $assigned_id_new = $user->id;
    }

    // Get new and old assigned users info
    $database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_new'");
    $assigned_new = $database->loadObject();
    $database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_old'");
    $assigned_old = $database->loadObject();

    // Get Ticket Reply Other details
    $replytime = JRequest::getVar('replytime', '00:00', '', 'string');
    $travel_time = JRequest::getVar('travel_time', '00:00', '', 'string');
    $tickettravel = JRequest::getVar('tickettravel', '00:00', '', 'string');
    $user_rate = JRequest::getVar('clientrate', '0', '', 'string');
    $id_activity_rate = JRequest::getVar('id_activity_rate', '0', '', 'string');
    $id_activity_type = JRequest::getVar('id_activity_type', '', '', 'string');
    $start_time = JRequest::getVar('start_time', '00:00', '', 'string');
    $end_time = JRequest::getVar('end_time', '00:00', '', 'string');
    $break_time = JRequest::getVar('break_time', '00:00', '', 'string');
	$now_date = HelpdeskDate::DateOffset("%Y-%m-%d");
	$now_hours = HelpdeskDate::DateOffset("%H:%M:%S");
    $reply_date = JRequest::getVar('reply_date', $now_date, '', 'string');
    $reply_hours = JRequest::getVar('reply_hours', $now_hours, '', 'string');
    $duedate_new = (JRequest::getVar('duedate_date', '', 'POST', 'string') . ' ' . JRequest::getVar('duedate_hours', '', 'POST', 'string') . ':00');
    $duedate_old = $ticket->duedate;

    // Set Ticket URL Link
    $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $wkoptions->id . '&task=ticket_view&id=' . $ticket->id, false);

    //////////////////////////////////
    // Calls the add-on's engine	//
    //////////////////////////////////
    HelpdeskAddon::Execute(2, 2, $ticket->id);

    // Ticket messages history
    $body = HelpdeskTemplate::Parse(array(), ($is_support ? 'ticket_reply_mail_notify_support' : 'ticket_reply_mail_notify_customer'));
    $messages_start = (stripos($body, '<!-- messages:start -->') - 1);
    $messages_end = stripos($body, '<!-- messages:end -->');
    $messages = '';
    if ($messages_start !== false && $messages_end !== false) {
        $messages_loop = JString::substr($body, $messages_start, ($messages_end - $messages_start));

        // Get Ticket Messages
        $ticketMsgs[0]->id_user = $ticket->id_user;
        $ticketMsgs[0]->user = $ticket->an_name;
        $ticketMsgs[0]->date = $ticket->date;
        $ticketMsgs[0]->message = $ticket->message;

        $sql = "(SELECT u.name as user, r.date, r.message, r.id_user
				FROM #__support_ticket_resp as r 
					 LEFT JOIN #__users as u ON r.id_user=u.id 
				WHERE r.id_ticket=" . (int)$ticket->id . ") 
				
				UNION
				
				(SELECT u.name as user, n.date_time AS date, n.note, n.id_user
				FROM #__support_note AS n
					 INNER JOIN #__users AS u ON u.id=n.id_user
				WHERE n.show=1 AND n.id_ticket=" . (int)$ticket->id . ")
				
				ORDER BY `date` DESC";
        $database->setQuery($sql);
        $ticketMsgs = array_merge($database->loadObjectList(), $ticketMsgs);

        for ($i = 0; $i < count($ticketMsgs); $i++)
        {
            $messages .= str_replace('[messages:date]', $ticketMsgs[$i]->date, str_replace('[messages:author]', $ticketMsgs[$i]->user, str_replace('[messages:message]', $ticketMsgs[$i]->message, str_replace('[messages:avatar]', HelpdeskUser::GetAvatar($ticketMsgs[$i]->id_user), $messages_loop))));
        }
    }

	// Ticket Reply
	$reply_id = 0;
	if (strip_tags($reply_msg) != "" || strip_tags($reply_summary_msg) != "")
	{
		// If it's from support and it's the first then update DATE_SUPPORT field
		$sql = "SELECT COUNT(r.id)
				FROM #__support_ticket_resp r
				     INNER JOIN #__support_permission p ON p.id_user=r.id_user
				WHERE r.id_ticket='" . $ticket->id . "'";
		$database->setQuery($sql);
		if ($is_support && $database->loadResult() == 0)
		{
			$sql = "UPDATE #__support_ticket
					SET date_support='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "'
					WHERE id=" . (int)$ticket->id;
			$database->setQuery($sql);
			$database->query();
		}

		$reply_msg = strip_tags($reply_msg, '<p><br><u><b><i><a><pre><code><ul><ol><li>');

		if ($reply_msg != '&nbsp;')
		{
			// Save Ticket Reply
			$reply_id = HelpdeskTicket::Reply($ticket->id, $reply_summary_msg, $reply_msg, $replytime, $travel_time, $tickettravel, $user_rate, $id_activity_rate, $id_activity_type, $start_time, $end_time, $break_time, $reply_date, $reply_hours);
			if ($reply_id)
			{
				// Add ticket log message
				HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('posted_reply')), JText::_('posted_reply_customer'), $ticket->id_status, '', 0, 0, 'add_message.png');
			}
			else
			{
				HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
				$errors++;
			}
		}
	}

	// Attached File
	for ($xx = 1; $xx <= $supportConfig->attachs_num; $xx++)
	{
		if (isset($_FILES['file' . $xx]))
		{
			if ($_FILES['file' . $xx]['name'] != '')
			{
				$fileupload = HelpdeskFile::Upload($ticket->id, 'T', "file$xx", $supportConfig->docspath, $_POST['desc' . $xx], 0, (int) $_POST['available' . $xx], '', $reply_id);
				if ($fileupload)
				{
					$attachments[] = $fileupload;
					$ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('attached_file'));
					$ticketLogMsg = str_replace('%2', $_FILES['file' . $xx]['name'], $ticketLogMsg);
					HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('attached_file_hidden'), $ticket->id_status, 'attachfile', '', 0, 'add_attach.png');
					HelpdeskUtility::AddGlobalMessage(JText::_('upload_ok'), 'i');
				}
			}
		}
	}

    // Set Email Notify Template variables
    $var_set = array('[duedate]' => $ticket->duedate,
        '[duedate_old]' => $duedate_old != $duedate_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . $duedate_old . '</b>' : '',
        '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
        '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
        '[number]' => $ticket->ticketmask,
        '[assign]' => isset($assigned_new) ? HelpdeskUtility::String2HTML($assigned_new->name) : '',
        '[assign_email]' => isset($assigned_new) ? $assigned_new->email : '',
        '[unassigned]' => isset($assigned_old) && isset($assigned_new) ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML($assigned_old->name) . '</b>' : '',
        '[unassigned_email]' => isset($assigned_old) ? $assigned_old->email : '',
        '[subject]' => HelpdeskUtility::String2HTML($ticket->subject),
        '[message]' => HelpdeskUtility::String2HTML($reply_msg),
        '[summary]' => $reply_summary_msg,
        '[author]' => HelpdeskUser::GetName($user->id),
        '[recipient]' => '',
        '[email]' => $ticket->an_mail,
        '[client]' => HelpdeskUtility::String2HTML($client_name),
        '[url]' => $ticket_url,
        '[department]' => HelpdeskUtility::String2HTML($wkoptions->wkdesc),
        '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_new)),
        '[priority_old]' => $priority_id_old != $priority_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_old)) . '</b>' : '',
        '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
        '[status_old]' => $status_id_old != $status_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_old)) . '</b>' : '',
        '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_new)),
        '[category_old]' => $category_id_old != $category_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_old)) . '</b>' : '',
        '[source]' => HelpdeskUtility::String2HTML($source),
        '[helpdesk]' => JURI::root(),
        '[messages]' => $messages
    );
    $var_set = array_merge($var_set, $cfields_array);

    // Ticket Assignment
    //echo "<p>Assignment: $assigned_id_old / $assigned_id_new";
	if ($assigned_id_old != $assigned_id_new)
	{
		$database->setQuery("UPDATE #__support_ticket SET assign_to='" . $assigned_id_new . "' WHERE id='" . $ticket->id . "'");
		$database->query();

		HelpdeskTicket::Log($ticket->id, str_replace('%1', $assigned_new->name, JText::_('ticket_assigned')), JText::_('assigned_hidden'), $ticket->id_status, 'assign', $assigned_id_new, 0, 'add_assign.png'); // Assigned Ticket log msg
	}

    //if ($assigned_id_old != $assigned_id_new && $assigned_id_new != $user->id && ($assigned_id_new || $assigned_id_old))
    if (($assigned_id_new || $assigned_id_old) && $assigned_id_new != $user->id)
    {
        // Update Ticket Assigned user
        $database->setQuery("UPDATE #__support_ticket SET assign_to='" . $assigned_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query())
        {
            // Aditional Suport Users Notify -- COLOCAR COMO CC NO MAIL DE CIMA
            $sql = "SELECT assign_report_users FROM `#__support_permission` WHERE id_user = '" . $assigned_id_new . "' AND id_workgroup = '" . $id_workgroup . "' LIMIT 1 ";
            $database->setQuery($sql);
            $additional_users_notify = $database->loadResult();
            $mailcc = NULL;
            if ($additional_users_notify != '')
            {
                $users_report_additional = explode('#', $additional_users_notify);
                $usercount = count($users_report_additional);
                if ($usercount > 0)
                {
                    for ($i = 0; $i < $usercount; $i++)
                    {
                        $database->setQuery("SELECT name, email FROM #__users WHERE id = '" . $users_report_additional[$i] . "' ");
                        $additional_user = $database->loadObject();
                        $mailcc[] = $additional_user->email;
                    }
                }
            }

            if ($wkoptions->tkt_asgn_new_asgn)
	        {
                //SendMailNotification( $ticket->id, $var_set, $assigned_new->email, (strip_tags($reply_msg)!='' ? 'reply' : 'updated'), $mailcc, null, 'tkt_notification_reply' );
                $var_set['[recipient]'] = $assigned_new->name;
		        //if (!$internal)
		        //{
                    SendMailNotification($ticket->id, $var_set, $assigned_new->email, 'updated_mail_subject', 'reply_mail_notify', 'ticket_reply_mail_notify_support', $mailcc, null, $attachments);
		        //}
                if( $supportConfig->sms_assign )
                {
                    HelpdeskSMS::SendSMS(sprintf(JText::_('SMS_ASSIGN_MESSAGE'), $ticket->subject), JText::_('ASSIGNMENT'), $ticket->id);
                }
            }
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Ticket Status
    if ($status_id_old != $status_id_new) {
        // Update Ticket Status
        $database->setQuery("UPDATE #__support_ticket SET id_status='" . $status_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query()) {
            // Ticket log
            $ticketLogMsg = JText::_('changed_status');
            $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), $ticketLogMsg);
            $ticketLogMsg = str_replace('%2', HelpdeskStatus::GetName($status_id_old), $ticketLogMsg);
            $ticketLogMsg = str_replace('%3', HelpdeskStatus::GetName($status_id_new), $ticketLogMsg);
            HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_status_hidden'), $status_id_new, 'status', $status_id_new, 0, 'change.png');
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Category Change
    if ($category_id_old != $category_id_new) {
        $database->setQuery("UPDATE #__support_ticket SET id_category='" . $category_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query()) {
            $ticketLogMsg = JText::_('changed_category');
            $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), $ticketLogMsg);
            $ticketLogMsg = str_replace('%2', HelpdeskCategory::GetName($category_id_old), $ticketLogMsg);
            $ticketLogMsg = str_replace('%3', HelpdeskCategory::GetName($category_id_new), $ticketLogMsg);
            HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_category_hidden'), $ticket->id_status, 'category', $category_id_new, 0, 'change.png');
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Due Date Change
    if ($duedate_old != $duedate_new)
    {
        $database->setQuery("UPDATE #__support_ticket SET duedate='$duedate_new' WHERE id='" . $ticket->id . "'");
        if ($database->query())
        {
            $duedate_old = date(HelpdeskDate::GetDateShortFormat(), HelpdeskDate::ParseDate($duedate_old, '%Y-%m-%d %H:%M:%S'));
            $duedate_new = date(HelpdeskDate::GetDateShortFormat(), HelpdeskDate::ParseDate($duedate_new, '%Y-%m-%d %H:%M:%S'));
            $ticketLogMsg = JText::_('changed_duedate');
            $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), $ticketLogMsg);
            $ticketLogMsg = str_replace('%2', $duedate_old, $ticketLogMsg);
            $ticketLogMsg = str_replace('%3', $duedate_new, $ticketLogMsg);
            HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_duedate_hidden'), $ticket->id_status, '', 0, 0, 'change.png');
        }
        else
        {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Priority Change
    if ($priority_id_old != $priority_id_new)
    {
        $database->setQuery("UPDATE #__support_ticket SET id_priority='" . $priority_id_new . "' WHERE id='" . $ticket->id . "'");
        if ($database->query()) {
            $ticketLogMsg = str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('changed_priority'));
            $ticketLogMsg = str_replace('%2', HelpdeskPriority::GetName($priority_id_old), $ticketLogMsg);
            $ticketLogMsg = str_replace('%3', HelpdeskPriority::GetName($priority_id_new), $ticketLogMsg);
            HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('changed_priority_hidden'), $ticket->id_status, 'priority', $priority_id_new, 0, 'change.png');
        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Screenr integration
    if ($supportConfig->screenr_account!='' && $supportConfig->screenr_api_id!='')
    {
        $screenr_url = JRequest::getVar('screenr_url', '', '', 'string');
        $screenr_embedurl = JRequest::getVar('screenr_embedurl', '', '', 'string');
        $screenr_thumbnailurl = JRequest::getVar('screenr_thumbnailurl', '', '', 'string');
        $screenr_id = JRequest::getVar('screenr_id', '', '', 'string');

        if ($screenr_url!='' && $screenr_embedurl!='' && $screenr_thumbnailurl!='' && $screenr_id!='')
        {
            // Insert into database
            $sql = "INSERT INTO `#__support_ticket_screenr`(`id_user`, `id_ticket`, `id_reply`, `id_screen`, `url`, `embedurl`, `thumbnailurl`)
                        VALUES({$user->id}, {$ticket->id}, $reply_id, " . $database->quote($screenr_id) . ", " . $database->quote($screenr_url) . ", " . $database->quote($screenr_embedurl) . ", " . $database->quote($screenr_thumbnailurl) . ")";
            $database->setQuery($sql);
            $database->query();

            // Insert into log
            HelpdeskTicket::Log($ticket->id, str_replace('%1', HelpdeskUser::GetName($user->id), JText::_('screencast_added')), JText::_('screencast_added_hidden'), 0, '', 0, 0, 'add_screencast.png');
        }
    }

    // Client notifications
    $cc_report = JRequest::getVar('cc_email_address', null, 'POST', 'array');
    $bcc_report = JRequest::getVar('bcc_email_address', null, 'POST', 'array');

    /*echo "<p>CC como veio do formulario: ";print_r($cc_report);
echo "<p>BCC como veio do formulario: ";print_r($bcc_report);*/

    //$cc_report = explode(',',$cc_report);
    //$bcc_report = explode(',',$bcc_report);

    /*echo "<hr>";
echo "<p>Existe uma nova mensagem: ".(strip_tags($reply_msg)!=""?'SIM':'NAO');
echo "<p>Utilizador do suporte: ".($is_support?'SIM':'NAO');
echo "<p>Tem CCs: ".(count($cc_report)?'SIM':'NAO');
echo "<p>Tem BCCs: ".(count($bcc_report)?'SIM':'NAO');
echo "<p>Duedate alterou: ".($duedate_old!=$duedate_new?'SIM':'NAO');
echo "<p>Prioridade alterou: ".($priority_id_old!=$priority_id_new?'SIM':'NAO');
echo "<p>Categoria alterou: ".($category_id_old!=$category_id_new?'SIM':'NAO');
echo "<p>Estado alterou: ".($status_id_old!=$status_id_new?'SIM':'NAO');*/

    if (strip_tags($reply_msg) != "" // Existe uma nova mensagem
        || ($duedate_old != $duedate_new) // Duedate alterou
        || ($category_id_old != $category_id_new) // Categoria alterou
        || ($status_id_old != $status_id_new) // Estado alterou
        || ($priority_id_old != $priority_id_new) // Prioridade alterou
        || count($cc_report) > 0 // Tem CCs
        || count($bcc_report) > 0 // Tem BCCs
    )
    {
	    if ($is_support)
	    {
		    // If it's anonymous user must change URL to the ticket
		    if (!$ticket->id_user)
		    {
			    $var_set['[url]'] = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&filter_ticketid=' . $ticket->ticketmask . '&filter_email=' . urlencode($ticket->an_mail), false);
		    }
		    $var_set['[recipient]'] = $ticket->an_name;
		    if (!$internal)
		    {
		        SendMailNotification($ticket->id, $var_set, $userinfo->email, 'updated_mail_subject', 'reply_mail_notify', 'ticket_reply_mail_notify_customer', $cc_report, $bcc_report, $attachments);
		    }
	    }
    }

    // Notify Support User is now unassigned
    /*if ( !$is_support && !$assigned_id_new && $user->id != $ticket->assign_to && $assigned_id_old ) {
		SendMailNotification( $ticket->id, $var_set, $assigned_old->email, 'reply', $cc_report, $bcc_report, 'tkt_notification_reply' );
	}*/

    // Update Tickets Last Updated Date
    $database->setQuery("UPDATE #__support_ticket SET last_update='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "' WHERE id='" . $_POST['id'] . "'");
    !$database->query() ? HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1)) : '';
    if (!$database->query()) $errors++;

    // Update Ticket Rating
    /*if (!$is_support && isset($_POST['rate']) && $_POST['rate'] > 0) {
        rateTicket($id_workgroup, $_POST['id'], $_POST['rate']);
        // Notify admin if notify is below the setted in configuration
        if ($supportConfig->notify_rate && $supportConfig->less_rate >= $_POST['rate']) {
            //SendMailNotification( $ticket->id, $var_set, $CONFIG->mailfrom, 'lowrate', '', 'tkt_notification_reply', null, null );
        }
    }*/

    // Redirect
    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . $msg . '&orderby=' . $orderby . '&order=' . $order, false);

    if ($errors == 0) {
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('reply_saved_ok'), $ticket_url, $ticket->ticketmask), 'i');
    } else {
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('reply_saved_ko'), $errors, $ticket_url, $ticket->ticketmask), 'e');
    }

    // Ticket saved, automatically the queue flag is removed
    $database->setQuery("UPDATE #__support_ticket SET queue='0' WHERE id='" . $ticket->id . "'");
    $database->query();

    $mainframe->redirect($url);
}


function saveNote($id)
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    JRequest::checkToken() or jexit('FALSE|Invalid Token');

    $database->setQuery("INSERT INTO #__support_note(id_ticket, id_user, date_time, note, `show`) VALUES('" . $id . "', '" . $user->id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', " . $database->quote($_POST['note']) . ", '" . $_POST['show'] . "')");
    if (!$database->query()) {
        HelpdeskUtility::AddGlobalMessage(JText::_('note_saved_ko') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
    } else {
        HelpdeskUtility::AddGlobalMessage(JText::_('note_saved_ok'), 'i');
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $id, false);
    $mainframe->redirect($url);
}


function saveTask($id)
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    JRequest::checkToken() or jexit('FALSE|Invalid Token');
    $CONFIG = new JConfig();
    $msg = '';
    $taskdate = JRequest::getVar('taskdate', '', 'POST', 'string') . " " . JRequest::getVar('taskstart', '', 'POST', 'string') . ":00";
    $timeused = JRequest::getVar('tasktime', '0.00', '', 'string');
    $timeused = str_replace(":", ".", $timeused);
    $traveltime = JRequest::getVar('traveltime', '0.00', '', 'string');
    $traveltime = str_replace(":", ".", $traveltime);
    $rate = JRequest::getVar('clientrate', '0.0', '', 'string');
    $database->setQuery("INSERT INTO #__support_task(id_ticket, id_creator, id_user, date_time, task, status, timeused, travel, traveltime, rate, start_time, end_time, break_time, id_activity_rate, id_activity_type) VALUES('" . $id . "', '" . $user->id . "', " . $database->quote(JRequest::getVar('usertask', '', 'POST', 'string')) . ", " . $database->quote($taskdate) . ", " . $database->quote(JRequest::getVar('taskfield', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('status', '', 'POST', 'string')) . ", " . $database->quote($timeused) . ", " . $database->quote(JRequest::getVar('tasktravel', '', 'POST', 'string')) . ", " . $database->quote($traveltime) . ", " . $database->quote($rate) . ", " . $database->quote(JRequest::getVar('taskstart', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('taskend', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('taskbreak', '', 'POST', 'string')) . ", '" . JRequest::getVar('activity_rate', '', 'POST', 'int') . "', '" . JRequest::getVar('activity_type', '', 'POST', 'int') . "')");

    if (!$database->query()) {
        HelpdeskUtility::AddGlobalMessage(JText::_('task_saved_ko') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
    } else {
        HelpdeskUtility::AddGlobalMessage(JText::_('task_saved_ok'), 'i');

        // Update Contracts
        $sql = "SELECT id_client 
				FROM #__support_ticket 
				WHERE id=" . $id;
        $database->setQuery($sql);
        $client_user = $database->loadResult();
        $contract = HelpdeskContract::Get($client_user, 'c');

        if (isset($contract))
        {
            switch ($contract->unit)
            {
                case 'H' :
	                // Get actual time
	                $sql = "SELECT actual_value
									FROM #__support_contract
									WHERE id=" . $contract->id;
	                $database->setQuery($sql);
	                $actual_value = $database->loadResult();

	                // Update actual time
	                $actual_value = HelpdeskDate::ConvertHoursMinutesToDecimal($timeused) + $actual_value;
	                $sql = "UPDATE `#__support_contract`
							SET `actual_value`=" . $actual_value . "
							WHERE `id`=" . (int)$contract->id;
                    $database->setQuery($sql);
                    if (!$database->query())
                    {
                        HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
                        echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
                        exit();
                    }
            }

            HelpdeskContract::MakeInactive($contract->id);
        }

        // Notify user if different from the user that is creating
        if ($user->id != JRequest::getVar('usertask', '', 'POST', 'string'))
        {
            // Get Workgroup Options
            $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
            $wkoptions = null;
            $wkoptions = $database->loadObject();

            // Get Assigned User
            $database->setQuery("SELECT name, email FROM #__users WHERE id='" . JRequest::getVar('usertask', '', 'POST', 'int') . "'");
            $assigned = null;
            $assigned = $database->loadObject();

            // Get Ticket Info
            $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . $id . "'");
            $ticket = null;
            $ticket = $database->loadObject();

            // ==================================================================================================================
            // Get the template for the page
            $tmpl_code->htmlcode = HelpdeskTemplate::Get($id_workgroup, 'mail/task_created');
            // ==================================================================================================================

            // Replaces message body variables for the values
            $msginfo->message = str_replace("%ticket", $ticket->ticketmask, $tmpl_code->htmlcode); // Replaces the %ticket
            $msginfo->message = str_replace("%staff", $assigned->name, $msginfo->message); // Replaces the %staff
            $msginfo->message = str_replace("%task", JRequest::getVar('taskfield', '', 'POST', 'string'), $msginfo->message); // Replaces the %message
            $msginfo->message = str_replace("%calendar", JURI::root(), $msginfo->message); // Replaces the %email
            $msginfo->message = str_replace("%url", JURI::root(), $msginfo->message); // Replaces the %url

            $sendmail1 = JUtility::sendMail($wkoptions->wkadmin_email, $CONFIG->sitename . " <" . $CONFIG->mailfrom . ">", $assigned->email, JText::_('tsk_notify_new_subj'), $msginfo->message, 1);

            if (!$sendmail1) {
                $msg .= "MAIL: An error ocurred while sending the mail to assigned user!";
            }
        }
    }

    $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $id . '&msg=' . $msg, false);
    $mainframe->redirect($link);
}


function anonymousViewTicket($id, $print)
{
    global $supportOptions, $usertype;

    $database = JFactory::getDBO();
    $document = JFactory::getDocument();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();
    $is_client = HelpdeskUser::IsClient();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);

    $document->addScriptDeclaration( 'var MQM_IS_ANONYMOUS = true;' );
    $document->addScriptDeclaration( 'var MQM_LOADING = "'.addslashes(JText::_('loading')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG01 = "'.addslashes(JText::_('tmpl_msg01')).'";' );
    $document->addScriptDeclaration( 'var MQM_NAME = "'.addslashes(JText::_('name_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_EMAIL = "'.addslashes(JText::_('email_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_CATEGORY = "'.addslashes(JText::_('category_required')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG02 = "'.addslashes(JText::_('tmpl_msg02')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG04 = "'.addslashes(JText::_('tmpl_msg04')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG05 = "'.addslashes(JText::_('tmpl_msg05')).'";' );
    $document->addScriptDeclaration( 'var MQM_MSG06 = "'.addslashes(JText::_('tmpl_msg06')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL = "'.addslashes(JText::_('tmpl_ticket_cancelquestion')).'";' );
    $document->addScriptDeclaration( 'var MQM_CANCEL_LINK = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my', false).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MONTH = "'.addslashes(JText::_('invalid_month')).'";' );
    $document->addScriptDeclaration( 'var MQM_YEAR1 = "'.(HelpdeskDate::DateOffset("%Y") + 1).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_YEAR = "'.addslashes(JText::_('invalid_year')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_DAY = "'.addslashes(JText::_('invalid_day')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_MINUTES = "'.addslashes(JText::_('invalid_minutes')).'";' );
    $document->addScriptDeclaration( 'var MQM_INV_HOURS = "'.addslashes(JText::_('invalid_hours')).'";' );
    HelpdeskUtility::AppendResource('helpdesk.tickets.edit.anonymous.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
    HelpdeskUtility::AppendResource('rating.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

    // If it's the print version shows icon to print and to close
    if ($print) {
        $img_src = JURI::root() . 'components/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/';
        echo '<style type="text/css" media="print">';
        echo '.exclude {';
        echo '	visibility: hidden;';
        echo '  display: none;';
        echo '}';
        echo '</style>';
        echo '<div align="right" class="exclude">';
        echo '<img src="' . $img_src . '16px/print.png" border="0" onClick="javascript:window.print();" style="cursor: pointer;" title="' . JText::_('print') . '">';
        echo '&nbsp;';
        echo '<img src="' . $img_src . '16px/close.png" border="0" onClick="javascript:window.close();" style="cursor: pointer;" title="' . JText::_('close') . '">';
        echo '</div>';
    }

    $imgpath = JURI::root() . 'components/com_maqmahelpdesk/images/';

    $database->setQuery("SELECT t.id, t.id_workgroup, t.id_status, t.id_user, t.id_category, t.date, t.subject, t.message, t.last_update, t.assign_to, t.id_priority, t.id_kb, t.source, t.ticketmask, t.an_name, 
								 t.an_mail, t.duedate, t.id_export, t.date_support, t.day_week, t.id_client, t.ipaddress, u.phone, u.fax, u.mobile, u.address1, u.address2, u.zipcode, u.location, u.city, u.country, u.avatar, t.id_directory
						  FROM #__support_ticket AS t 
						  	   LEFT JOIN #__support_users AS u ON u.id_user=t.id_user 
						  WHERE t.ticketmask='" . $id . "'");
    $row = null;
    $row = $database->loadObject();
    !$database->query() ? HelpdeskUtility::ShowSCMessage($database->getErrorMsg(), 'e') : '';
	$id_workgroup = $row->id_workgroup;

    // Check Mosets Tree and Sobi Pro integration
    $directory = null;
    if ($row->id_directory && ($supportConfig->integrate_mtree || $supportConfig->integrate_sobi)) {
        if ($supportConfig->integrate_mtree) {
            $sql = "SELECT `link_id` AS directory_id, `link_name` AS directory_name
					FROM `#__mt_links`
					WHERE `link_id` = " . $row->id_directory;
        } else {
            $sql = "SELECT s.`sid` AS directory_id, s.`baseData` AS directory_name
					FROM `#__sobipro_field_data` AS s
						 INNER JOIN `#__sobipro_field_data` AS f ON f.`fid` = s.`fid`
					WHERE f.`filter` = 'title'
					  AND s.`sid` = " . $row->id_directory . "
					LIMIT 0, 1";
        }
        $database->setQuery($sql);
        $directory = $database->loadObject();
    }

    // Sets the page title
    HelpdeskUtility::PageTitle('viewTicket', $row->subject);

    $database->setQuery("
	SELECT 
		s.status_group AS status_group
	FROM 
		#__support_ticket AS t
	LEFT JOIN
		#__support_status AS s ON t.id_status = s.id
	WHERE 
		t.ticketmask='" . $id . "'
	"
    );
    $ticket_closed = null;
    $ticket_closed = $database->loadResult();

    // for non support users: this ticket is close by a manager
    $closed_by_manager = 0;
    if (count($ticket_closed) > 0) {
	    if (!$is_support && $ticket_closed == 'C' && !$is_manager) {
            $closed_by_manager = 1;
        }
    }

    $database->setQuery("SELECT id_status FROM #__support_log WHERE id_ticket='" . $row->id . "' AND id_status <> '" . $row->id_status . "' AND id_status <> '' AND id_status <> '0' ORDER BY date_time DESC LIMIT 1");
    $old_id_status = $database->loadResult();
    $old_status = ($old_id_status > 0) ? HelpdeskStatus::GetName($old_id_status) : "<i>" . JText::_('no_last_status_logged') . "</i>";

    // Get client id if the connected user if from support
    $client_id = 0;

    // Get Ticket Messages
    $ticketMsgs[0]->user = $row->an_name;
    $ticketMsgs[0]->date = $row->date;
    $ticketMsgs[0]->message = $row->message;
    $ticketMsgs[0]->timeused = 0;
    $ticketMsgs[0]->travel_time = 0;
    $ticketMsgs[0]->tickettravel = 0;
    $ticketMsgs[0]->acttype = '';
    $ticketMsgs[0]->actrate = '';
    $ticketMsgs[0]->multiplier = 0;
    $ticketMsgs[0]->user_rate = 0;
    $ticketMsgs[0]->start_time = 0;
    $ticketMsgs[0]->end_time = 0;
    $ticketMsgs[0]->break_time = 0;
    $ticketMsgs[0]->id_user = 0;
    $ticketMsgs[0]->id = $row->id;
    $ticketMsgs[0]->id_activity_type = 0;
    $ticketMsgs[0]->id_activity_rate = 0;
    $ticketMsgs[0]->reply_summary = '';
    $ticketMsgs[0]->id_msg = 0;
	$ticketMsgs[0]->msgtype = 'message';
    $ticketMsgs[0]->avatar = HelpdeskUser::GetAvatar($ticketMsgs[0]->id_user);

    $sql = "SELECT r.`id`, u.name as user, r.date, r.message, r.timeused, r.travel_time, r.tickettravel, t.description as acttype, ra.description as actrate, ra.multiplier, r.user_rate, r.start_time, r.end_time, r.break_time, r.id_user, r.id, r.id_activity_type, r.id_activity_rate, r.reply_summary, r.id as id_msg, su.avatar, 'message' AS msgtype
    		FROM #__support_ticket_resp as r 
    			 LEFT JOIN #__users as u ON r.id_user=u.id 
    			 LEFT JOIN #__support_activity_type as t ON t.id=r.id_activity_type 
    			 LEFT JOIN #__support_activity_rate as ra ON ra.id=r.id_activity_rate 
    			 LEFT JOIN #__support_users AS su ON su.id_user=u.id 
    		WHERE r.id_ticket='" . $row->id . "' 
    		ORDER BY r.`date` ASC, `id` ASC";
    $database->setQuery($sql);
    $ticketMsgs = array_merge($ticketMsgs, $database->loadObjectList());

    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    $temp = 0;
    foreach ($ticketMsgs as $k => $val) {
        if ($ticketMsgs[$temp]->user == '') {
            $ticketMsgs[$temp]->user = $row->an_name;
        }
        if ($supportConfig->support_only_show_assign == "1") {
            $v = $ticketMsgs[$temp]->user;
            if ($v == HelpdeskTicket::GetAssign($row->assign_to)) {
                $ticketMsgs[$temp]->user = JText::_('support_user');
            } else if ($v == 'Administrator') {
                $ticketMsgs[$temp]->user = JText::_('administrator');
            } else if ($v == HelpdeskTicket::GetAssign($row->id_user)) {
                $ticketMsgs[$temp]->user = JText::_('user');
            } else {
                $ticketMsgs[$temp]->user = JText::_('other_user');
            }
        }
        $ticketMsgs[$temp]->avatar = HelpdeskUser::GetAvatar($ticketMsgs[$temp]->id_user);
        $temp++;
    }

    // Get Ticket Rating
    $database->setQuery("SELECT * FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
    $ticketRate = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Attachments
    $database->setQuery("SELECT * FROM #__support_file WHERE id='" . $row->id . "' AND source='T' AND `public`=1 ORDER BY `date` DESC ");
    $ticketAttachs = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Log
    $sql = "SELECT l.id, l.date_time as `date`, l.log_reserved as message, u.name as user, l.image
			FROM #__support_log as l 
				 LEFT JOIN #__users as u ON l.id_user=u.id 
			WHERE l.id_ticket='" . $row->id . "' 
			ORDER BY l.id DESC";
    $database->setQuery($sql);
	$ticketLogs = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Custom Fields for the Workgroup
    $sql = "SELECT v.id_ticket, c.id, w.id_workgroup, w.id_field, w.required, w.support_only, w.new_only, c.caption, c.ftype, c.value, c.size, c.maxlength, v.newfield, c.tooltip, w.section, w.id_category 
			FROM #__support_wk_fields as w, #__support_custom_fields as c, #__support_field_value as v 
			WHERE w.id_field=c.id AND v.id_field=c.id AND v.id_ticket='" . $row->id . "' AND w.id_workgroup='" . $id_workgroup . "' AND c.cftype='W'
			ORDER BY w.section, w.ordering";
    $database->setQuery($sql);
    $customfields = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Rate
    $rate = 0;
    $database->setQuery("SELECT rate FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='T'");
    $rate = $database->loadResult();
    if ($rate == "") {
        $rate = 0;
    }
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Ticket Replies and Tasks Travel Sum Value
    $database->setQuery("SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(k.traveltime,'.', ':' )))),'%H:%i')FROM #__support_task as k, #__support_ticket as t WHERE t.id=k.id_ticket AND t.id='" . $row->id . "'");
    $traveltime = $database->loadResult();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    $database->setQuery("SELECT TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':' )))),'%H:%i')FROM #__support_ticket_resp as r, #__support_ticket as t WHERE t.id=r.id_ticket AND t.id='" . $row->id . "'");
    $traveltime = HelpdeskDate::ConvertHoursMinutesToDecimal($traveltime) + HelpdeskDate::ConvertHoursMinutesToDecimal($database->loadResult());
    $traveltime = HelpdeskDate::ConvertDecimalsToHoursMinutes($traveltime);
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Get Travel Time on Tickets and Tasks Value
    $database->setQuery("SELECT c.travel_time FROM #__support_client as c, #__support_client_users as u, #__support_ticket as t WHERE c.id=u.id_client AND t.id_user=u.id_user AND t.id='" . $row->id . "'");
    $clienttravel = $database->loadResult();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';
    $document->addScriptDeclaration( 'var MQM_CLIENT_TRAVEL = "'.$clienttravel.'";' );

    // Get Ticket Messages Values by Activity Type
    $database->setQuery("SELECT r.timeused, r.user_rate as rate, r.travel_time, r.tickettravel, ra.multiplier, t.description AS acttype, ra.description AS actrate FROM #__support_ticket_resp as r, #__support_activity_type as t, #__support_activity_rate as ra WHERE t.id=r.id_activity_type AND ra.id=r.id_activity_rate AND r.id_ticket='" . $row->id . "' ORDER BY t.description, ra.description");
    $ticketValues = $database->loadObjectList();
    !$database->query() ? HelpdeskUtility::ShowSCMessage(stripslashes($database->getErrorMsg()), 'e') : '';

    // Build Status select list
    $clause = ($is_client) ? " WHERE (user_access = '1' OR (user_access = '0' AND id = '" . $row->id_status . "')) " : "";

    if (!$is_support) {
        $sql = "SELECT `allow_old_status_back` FROM #__support_status WHERE id = '" . $row->id_status . "' ";
        $database->setQuery($sql);
        $allow_old_status_back = $database->loadResult();

        // lista de estados que o actual estado permite selecionar
        $sql = "SELECT `status_workflow` FROM #__support_status WHERE id = '" . $row->id_status . "' ";
        $database->setQuery($sql);
        $status_workflow = $database->loadResult();
        $status_checked = explode("#", $status_workflow);
        if (count($status_checked) > 0) {
            $list = "";
            if ($allow_old_status_back == 1) {
                $list .= "'" . $old_id_status . "',";
            }
            for ($i = 0; $i < count($status_checked); $i++) {
                if (($allow_old_status_back == 1) && ($status_checked[$i] != $old_id_status)) {
                    $list .= "'" . $status_checked[$i] . "',";
                } else {
                    $list .= "'" . $status_checked[$i] . "',";
                }
            }

            if ($list != "") { // PREVENIR ERRO: nem todos os estados poss�veis de listar aparecem (se j� for estado enterior)
                $list = JString::substr($list, 0, -1); // retira a ultima virgula
                $list_status_allowed = "('" . $row->id_status . "'," . $list . ")"; // temos de incluir o id actual porque podemos n�o o querer actualizar o estado. Para manter o status temos de o listar.
                $clause .= ($clause == "") ? " WHERE " : " AND ";
                $clause .= " `id` IN " . $list_status_allowed . " ";
            }
        }
    }

    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_status " . $clause . " ORDER BY `ordering`, `description`";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'id_status', '', 'value', 'text', $row->id_status);

    // Build Priority select list
    $sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority WHERE `show`=1 ORDER BY description";
    $database->setQuery($sql);
    $rows_wk = $database->loadObjectList();
    $lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'id_priority', 'id="id_priority"  onchange="DueDatePonderado();"', 'value', 'text', $row->id_priority);

    // Build workgroup categories select list
    $lists['category'] = HelpdeskForm::BuildCategories($row->id_category, false, true, false, false);

    $old_duedate_date = date("Y-m-d", (strtotime(JString::substr($row->duedate, 1, 10))));
    $old_duedate_hour = date("H:i", (strtotime(JString::substr($row->duedate, 11, 5))));

    // ***********************************************************************
    // Activities
    $i = 1;
    foreach ($ticketMsgs as $key2 => $value2) {
        $acttype = '';
        $actrate = '';
        $start_time = '';
        $end_time = '';
        $break_time = '';
        $timeused = '';
        $tickettravel = '';
        $travel_time = '';
        $id_user = '';
        $message = '';
        $id = '';

        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $activities_rows[$i][$key3] = $value3;

                if ($key3 == 'acttype') {
                    $acttype = $value3;
                }
                if ($key3 == 'actrate') {
                    $actrate = $value3;
                }
                if ($key3 == 'start_time') {
                    $start_time = $value3;
                }
                if ($key3 == 'end_time') {
                    $end_time = $value3;
                }
                if ($key3 == 'break_time') {
                    $break_time = $value3;
                }
                if ($key3 == 'timeused') {
                    $timeused = $value3;
                }
                if ($key3 == 'tickettravel') {
                    $tickettravel = $value3;
                }
                if ($key3 == 'travel_time') {
                    $travel_time = $value3;
                }
                if ($key3 == 'id_user') {
                    $id_user = $value3;
                }
                if ($key3 == 'message') {
                    $message = str_replace('\"', "", str_replace("\'", "'", $value3));
                }
                if ($key3 == 'id') {
                    $id = $value3;
                }
                if ($key3 == 'id_msg') {
                    $id_msg = $value3;
                }
                if ($key3 == 'date') {
                    $value3 = explode(' ', $value3);
                    $activities_rows[$i]['date_only'] = $value3[0];
                    $activities_rows[$i]['hours_only'] = $value3[1];
                }
            }
        }

        // Builds the tooltip
        $status_group = HelpdeskTicket::GetStatusGroup($row->id_status);

        if ($workgroupSettings->use_activity) {
            $tmp_tooltip_msg = ((''
                . '<b>' . JText::_('tmpl_msg12') . '</b> ' . ($acttype == "" ? JText::_('empty') : $acttype) . '<br />'
                . '<b>' . JText::_('tmpl_msg13') . '</b> ' . ($actrate == "" ? "0.00" : $actrate) . '<br />'
                . '<b>' . JText::_('tmpl_msg14') . '</b> ' . ($start_time == "" ? JText::_('empty') : $start_time == "0" ? '0:00' : $start_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg15') . '</b> ' . ($end_time == "" ? JText::_('empty') : $end_time == "0" ? '0:00' : $end_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg16') . '</b> ' . ($break_time == "" ? JText::_('empty') : $break_time == "0" ? '0:00' : $break_time) . '<br />'
                . '<b>' . JText::_('tmpl_msg19') . '</b> ' . str_replace('.', ':', $timeused == "0" ? '0:00' : $timeused) . '<br />'
                . '<b>' . JText::_('tmpl_msg22') . '</b> ' . str_replace('.', ':', $tickettravel)
                . ' (' . JText::_('chargeable') . ': ' . ($travel_time == 1 ? JText::_('MQ_YES') : JText::_('MQ_NO')) . ')' . '<br />'));
        } else {
            $tmp_tooltip_msg = '';
        }

        $tmp_tooltip = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('activity_details') . '::' . $tmp_tooltip_msg) . '"> <img src="' . $imgpath . '16px/info.png" align="absmiddle" border="0" hspace="5" style="cursor:help;"/></span>';

        $activities_rows[$i]['tooltip'] = $tmp_tooltip;

        // Checks the lines and chars limits
        if ($workgroupSettings->lim_actmsgs)
        {
            $workgroupSettings->lim_actmsgs_chars ? $char_limit = $workgroupSettings->lim_actmsgs_chars : $char_limit = 300;
            $workgroupSettings->lim_actmsgs_lines ? $line_limit = $workgroupSettings->lim_actmsgs_lines : $line_limit = 5;
            $line_count = substr_count($message, "\n") + 1;
            $char_count = strlen($message);

            if (($char_count > $char_limit) || ($line_count > $line_limit))
            {
                $more_link = '<p><img class="alglft" src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/view+.png" border="0" alt="more" style="cursor:pointer;" onclick="$jMaQma(\'#' . $id_msg . '_short\').toggle(); $jMaQma(\'#' . $id_msg . '_all\').toggle(); return false;" /></p>';
                $less_link = '<p><img class="alglft" src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/view-.png" border="0" alt="less" style="cursor:pointer;" onclick="$jMaQma(\'#' . $id_msg . '_short\').toggle(); $jMaQma(\'#' . $id_msg . '_all\').toggle(); return false;" /></p>';
                $msg_short_header = '<div id="' . $id_msg . '_short" style="display:all;"><div style="width:100%;">';
                $msg_short_footer = '</div></div>';
                $msg_all_header = '<div id="' . $id_msg . '_all" style="display:none;"><div style="width:100%;">';
                $msg_all_footer = '</div></div>';

                if ($line_count > $line_limit && $char_count < $char_limit) {
                    $linebr_char_num = 0;
                    for ($linebr = 0; ($linebr < $line_limit); $linebr++) {
                        $linebr_char_num = strpos($message, "\n", $linebr_char_num + 1);
                    }
                    $msg_short = JString::substr($message, 0, $linebr_char_num);

                } else {
                    $msg_short = JString::substr($message, 0, $char_limit);
                }

                $msg_short = rtrim($msg_short, "\r \n");
                $msg_all = $message;

                if ($workgroupSettings->hyper_links) {
                    $msg_all = HelpdeskUtility::TextHyperlinks($msg_all);
                    $msg_short = HelpdeskUtility::TextHyperlinks($msg_short);
                }
                $message = $msg_short_header . $msg_short . '...' . $more_link . $msg_short_footer . $msg_all_header . $msg_all . $less_link . $msg_all_footer;
                /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                    $message = nl2br($message);
                }*/
            } else {
                if ($workgroupSettings->hyper_links) {
                    $message = HelpdeskUtility::TextHyperlinks($message);
                }
                /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                    $message = nl2br($message);
                }*/
            }
        } else {
            if ($workgroupSettings->hyper_links) {
                $message = HelpdeskUtility::TextHyperlinks($message);
            }
            /*if (stripos($message, '<br>') === false && stripos($message, '<br />') === false && stripos($message, '<br/>') === false) {
                $message = nl2br($message);
            }*/
        }

        $activities_rows[$i]['message_original'] = $activities_rows[$i]['message'];
        $activities_rows[$i]['message'] = $message;

        $i++;
    }

    // ***********************************************************************
    // Custom fields
    $i = 1;
    $cfields_hiddenfield = "";
    $j = 1;

    foreach ($customfields as $key2 => $value2) {
        $fid = 0;
        $ftype = '';
        $fvalue = '';
        $fsize = '';
        $flength = '';

        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $cfields_rows[$i][$key3] = $value3;
                if ($key3 == 'id')
                    $fid = $value3;
                if ($key3 == 'ftype')
                    $ftype = $value3;
                if ($key3 == 'value')
                    $fvalue = $value3;
                if ($key3 == 'size')
                    $fsize = $value3;
                if ($key3 == 'maxlength')
                    $flength = $value3;
                if ($key3 == 'support_only')
                    $fsupportonly = $value3;
                if ($key3 == 'new_only')
                    $fnewonly = $value3;
                if ($key3 == 'tooltip')
                    $ftooltip = $value3;
            }
        }

        if (((!$is_support) && ($fsupportonly > 0)) || ($fnewonly)) {
            if ($fsupportonly == 2) {
                $cfields_hiddenfield .= HelpdeskForm::WriteField($row->id, $fid, 'hidden', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);
                unset($cfields_rows[$i]); // remove from array
            } else { // 0 or 1
                $cfields_rows[$i]['field'] = HelpdeskForm::WriteField($row->id, $fid, 'readonly', $fvalue, $fsize, $flength, 0, 0, 0, 0, 0, $ftooltip);

                $j++;
            }
        } else {
            $exclude = (($fsupportonly && !$is_support) || ($fnewonly)) ? 1 : 0;
            $cfields_rows[$i]['field'] = HelpdeskForm::WriteField($row->id, $fid, $ftype, $fvalue, $fsize, $flength, 0, 0, $exclude, 0, 0, $ftooltip);

            $j++;
        }
        $i++;
    }

    // ***********************************************************************
    // Attachments
    for ($i = 1; $i <= $supportConfig->attachs_num; $i++) {
        $attachs[$i]['number'] = $i;

        if ($is_support) {
            $attachs[$i]['available'] = '<input type="radio" id="available' . $i . '0" name="available' . $i . '" value="0" class="inputbox" /> ' . JText::_('MQ_NO') . ' <input type="radio" id="available' . $i . '1" name="available' . $i . '" value="1" class="inputbox" checked /> ' . JText::_('MQ_YES');
        } else {
            $attachs[$i]['available'] = '<input type="hidden" name="available' . $i . '" value="1" />';
        }
    }

    // ***********************************************************************
    // Ticket Log
    if (count($ticketLogs) > 0) {
        $i = 1;
        foreach ($ticketLogs as $key2 => $value2) {
            if (is_object($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    $v = $value3;
                    $ticket_log[$i][$key3] = $v;
                }
            }

            if ($ticket_log[$i]['user'] == '') {
                $ticket_log[$i]['user'] = $row->an_name;
            }

            $i++;
        }
    }

    // ***********************************************************************
    // Attachments
    $i = 1;
    if (count($ticketAttachs) > 0) {
        foreach ($ticketAttachs as $key2 => $value2) {
            $id_file = '';
            $id = '';

            if (is_object($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    $ticket_attachs[$i][$key3] = $value3;

                    if ($key3 == 'id')
                        $id = $value3;
                    if ($key3 == 'id_file')
                        $id_file = $value3;
                    if ($key3 == 'id_user')
                        $id_user = $value3;
                    if ($key3 == 'public')
                        $public = $value3;
                }
            }

            $ticket_attachs[$i]['info'] = '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_("attachment") . '::<b>' . JText::_('date') . '</b>:' . $ticket_attachs[$i]['date'] . '<br /><b>' . JText::_('description') . '</b>:' . $ticket_attachs[$i]['description']) . '"><img src="' . $imgpath . 'info.png" align="absmiddle" border="0" hspace="5" style="cursor:pointer; cursor:hand;" /></span>';

            $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_download&id=' . $id_file . '&extid=' . $id, false);
            $tools = '<a href="' . $link . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/down.png" title="' . JText::_('download') . '" border="0" /></a>';

            if ($is_support || $id_user == $user->id) {
                $link2 = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_delattach&id=' . $id_file . '&extid=' . $id, false);
                $tools .= '&nbsp;<a href="' . $link2 . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/delete.png" title="' . JText::_('delete') . '" border="0" /></a>';
            }

            $available = '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($public == 1 ? 'ok' : 'no') . '.png" />';
            $ticket_attachs[$i]['available'] = $available;
            $ticket_attachs[$i]['tools'] = $tools;
            $ticket_attachs[$i]['link'] = $link;

            $i++;
        }

        if (!$is_support) {
            for ($z = 1; $z < count($ticket_attachs); $z++) {
                if ($ticket_attachs[$z]["public"] == 0) {
                    unset ($ticket_attachs[$z]);
                }
            }
        }
    }

    // Variables
    if ($row->duedate < date("Y-m-d")) {
        $status_color = 'important';
    } elseif (JString::substr($row->duedate, 0, 10) == date("Y-m-d")) {
        $status_color = 'warning';
    } elseif (JString::substr($row->duedate, 0, 10) > date("Y-m-d")) {
        $status_color = 'default';
    }

    $status_group = HelpdeskTicket::GetStatusGroup($row->id_status);

    if ((($row->id_export == 0 && !$print) && (($usertype >= $supportConfig->support_change_status) || ($supportConfig->client_change_status == 1 && (bool)$is_client) || ($supportConfig->register_user_change_status == 1)))) {
        $status = $lists['status'];
    } else {
        $status = HelpdeskStatus::GetName($row->id_status);
    }

    if ($closed_by_manager) {
        $status = HelpdeskStatus::GetName($row->id_status);
    }

    if ($supportConfig->support_only_show_assign != 1) {
        $assign = HelpdeskTicket::GetAssign($row->assign_to);
    } else {
        $assign = '<i>' . JText::_('assigned_hidden') . '</i>';
    }

    if ($status_group != 'C' && !$print) {
        $priority = $lists['priority'];
    } else {
        $priority = HelpdeskPriority::GetName($row->id_priority);
    }

    $source_desc = '';
    if ($row->source == "M") {
        $source_desc = JText::_('email');
    } elseif ($row->source == "F") {
        $source_desc = JText::_('fax');
    } elseif ($row->source == "O") {
        $source_desc = JText::_('other');
    } elseif ($row->source == "W") {
        $source_desc = JText::_('website');
    } elseif ($row->source == "P") {
        $source_desc = JText::_('phone');
    }

    if (count($ticketAttachs) > 0) {
        if (!$is_support) {
            $count_ticketAttachs = count($ticket_attachs);
        } else {
            $count_ticketAttachs = count($ticketAttachs);
        }
    } else {
        $count_ticketAttachs = '0';
    }

    // Display toolbar
    HelpdeskToolbar::Create();

    // Outputs the treated html code
    $tmplfile = HelpdeskTemplate::GetFile('tickets/view_ticket_anonymous');
    include $tmplfile;
}


function anonymousSaveReply()
{
    global $filter_client, $filter_user, $filter_search;

    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $is_support = HelpdeskUser::IsSupport();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    JRequest::checkToken() or jexit('FALSE|Invalid Token');

    // Initialise variables
    $msg = '';
    $wkoptions = null;
    $ticket = null;
    $assigned = null;
    $errors = 0;

    // Get Workgroup Options
    $database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
    $wkoptions = $database->loadObject();

    // Get Ticket Details
    $database->setQuery("SELECT * FROM #__support_ticket WHERE id='" . intval(JRequest::getVar('id', 0, '', 'int')) . "'");
    $ticket = $database->loadObject();

    //////////////////////////////////////
    //	Update Tickets Custom Fields	//
    //////////////////////////////////////
    $database->setQuery("SELECT f.id_field, c.caption, c.ftype FROM #__support_wk_fields AS f INNER JOIN #__support_custom_fields AS c ON c.id=f.id_field WHERE f.id_workgroup='" . $id_workgroup . "' AND c.cftype='W' ORDER BY f.ordering");
    $customfields = $database->loadObjectList();
    /*if( !$database->query() ) {
		HelpdeskUtility::AddGlobalMessage( JText::_('tkt_dberror').'<br />'.$database->getErrorMsg() , 'e', $database->stderr(1) );
		$errors++;
	}*/

    $x = 0;
    $database->setQuery("DELETE FROM #__support_field_value WHERE id_ticket='" . $ticket->id . "'");
    $database->query();
    $cfields_array = array();
    for ($x = 0; $x < count($customfields); $x++) {
        $ticketField = $customfields[$x];

        if (($ticketField->ftype == "checkbox")) {
            $custom_val2 = serialize(JRequest::getVar('custom' . $ticketField->id_field, '', '', 'array'));
            $custom_val = unserialize($custom_val2);
            if (is_array($custom_val)) {
                $tmp_custom_val = "";
                for ($t = 0; $t < sizeof($custom_val); $t++) {
                    $tmp_custom_val .= $custom_val[$t] . ",";
                }
                $custom_val = JString::substr($tmp_custom_val, 0, strlen($tmp_custom_val) - 1);
            }

        } else {
            $custom_val = JRequest::getVar('custom' . $ticketField->id_field, '', '', 'string');
	        $custom_val = str_replace('"', '', $custom_val);
	        $custom_val = stripslashes($custom_val);
        }

        $cfields_array_tmp = array('[cfield' . $ticketField->id_field . '_caption]' => $ticketField->caption,
            '[cfield' . $ticketField->id_field . '_value]' => HelpdeskUtility::String2HTML(($is_support ? $custom_val : nl2br($custom_val))));
        $cfields_array = array_merge($cfields_array, $cfields_array_tmp);
        $sql = "INSERT INTO #__support_field_value(id_field, id_ticket, newfield)
                VALUES('" . $ticketField->id_field . "', '" . $ticket->id . "', " . $database->quote($custom_val) . ")";
	    $database->setQuery($sql);

        if (!$database->query()) {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Get Ticket Users Client ID and Name
    $client_id = 0;
    $client_name = '';

    // Get Ticket Priority
    $priority_id_old = $ticket->id_priority;
    $priority_id_new = intval(JRequest::getVar('id_priority', $ticket->id_priority, '', 'int'));

    // Get Ticket Status
    $status_id_old = $ticket->id_status;

    // Get Ticket Reply Summary and Message
    $reply_msg = JRequest::getVar('reply', '', 'POST', 'string', 2);
    $reply_summary_msg = JRequest::getVar('reply_summary', '', 'POST', 'string');

    // Get new and old assigned users info
    $assigned_id_old = JRequest::getVar('id_assign', 0, 'POST', 'int');
    $database->setQuery("SELECT u.id, u.name, u.email FROM #__users as u WHERE u.id='$assigned_id_old'");
    $assigned_old = $database->loadObject();

    // Get Ticket Reply Other details
    $replytime = JRequest::getVar('replytime', '00:00', '', 'string');
    $travel_time = JRequest::getVar('travel_time', '00:00', '', 'string');
    $tickettravel = JRequest::getVar('tickettravel', '00:00', '', 'string');
    $user_rate = JRequest::getVar('clientrate', '0', '', 'string');
    $id_activity_rate = JRequest::getVar('id_activity_rate', '0', '', 'string');
    $id_activity_type = JRequest::getVar('id_activity_type', '', '', 'string');
    $start_time = JRequest::getVar('start_time', '00:00', '', 'string');
    $end_time = JRequest::getVar('end_time', '00:00', '', 'string');
    $break_time = JRequest::getVar('break_time', '00:00', '', 'string');
    $reply_date = JRequest::getVar('reply_date', '', '', 'string');
    $reply_hours = JRequest::getVar('reply_hours', '00:00', '', 'string');
    $duedate_new = (JRequest::getVar('duedate_date', '', 'POST', 'string') . ' ' . JRequest::getVar('duedate_hours', '', 'POST', 'string') . ':00');
    $duedate_old = $ticket->duedate;

    // Set Ticket URL Link
    $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id, false);

    //////////////////////////////////
    // Calls the add-on's engine	//
    //////////////////////////////////
    HelpdeskAddon::Execute(2, 2, $ticket->id);

    // Ticket messages history
    $body = HelpdeskTemplate::Parse(array(), 'ticket_reply_mail_notify_customer');
    $messages_start = (stripos($body, '<!-- messages:start -->') - 1);
    $messages_end = stripos($body, '<!-- messages:end -->');
    $messages = '';
    if ($messages_start !== false && $messages_end !== false) {
        $messages_loop = JString::substr($body, $messages_start, ($messages_end - $messages_start));

        // Get Ticket Messages
        $ticketMsgs[0]->id_user = $ticket->id_user;
        $ticketMsgs[0]->user = $ticket->an_name;
        $ticketMsgs[0]->date = $ticket->date;
        $ticketMsgs[0]->message = $ticket->message;

        $sql = "(SELECT u.name as user, r.date, r.message, r.id_user
				FROM #__support_ticket_resp as r 
					 LEFT JOIN #__users as u ON r.id_user=u.id 
				WHERE r.id_ticket=" . (int)$ticket->id . ") 
				
				UNION
				
				(SELECT u.name as user, n.date_time AS date, n.note, n.id_user
				FROM #__support_note AS n
					 INNER JOIN #__users AS u ON u.id=n.id_user
				WHERE n.show=1 AND n.id_ticket=" . (int)$ticket->id . ")
				
				ORDER BY `date` DESC";
        $database->setQuery($sql);
        $ticketMsgs = array_merge($ticketMsgs, $database->loadObjectList());

        for ($i = 0; $i < count($ticketMsgs); $i++) {
            $messages .= str_replace('[messages:date]', $ticketMsgs[$i]->date, str_replace('[messages:author]', $ticketMsgs[$i]->user, str_replace('[messages:message]', $ticketMsgs[$i]->message, str_replace('[messages:avatar]', HelpdeskUser::GetAvatar($ticketMsgs[$i]->id_user), $messages_loop))));
        }
    }

    // Set Email Notify Template variables
    $var_set = array('[duedate]' => $ticket->duedate,
        '[duedate_old]' => '',
        '[date]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateShortFormat()),
        '[datelong]' => HelpdeskDate::DateOffset(HelpdeskDate::GetDateLongFormat()),
        '[number]' => $ticket->ticketmask,
        '[assign]' => HelpdeskUtility::String2HTML((isset($assigned_old) ? $assigned_old->name : '')),
        '[assign_email]' => (isset($assigned_old) ? $assigned_old->email : ''),
        '[unassigned]' => HelpdeskUtility::String2HTML((isset($assigned_old) && isset($assigned_new) ? $assigned_old->name : '')),
        '[unassigned_email]' => (isset($assigned_old) ? $assigned_old->email : ''),
        '[subject]' => HelpdeskUtility::String2HTML(($ticket->subject)),
        '[message]' => (HelpdeskUtility::String2HTML($ticket->message)),
        '[summary]' => ($reply_summary_msg),
        '[author]' => HelpdeskUtility::String2HTML($ticket->an_name),
        '[recipient]' => '',
        '[email]' => $ticket->an_mail,
        '[client]' => HelpdeskUtility::String2HTML($client_name),
        '[url]' => $ticket_url,
        '[department]' => HelpdeskUtility::String2HTML($wkoptions->wkdesc),
        '[priority]' => HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_new)),
        '[priority_old]' => $priority_id_old != $priority_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskPriority::GetName($priority_id_old)) . '</b>' : '',
        '[status]' => HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_new)),
        '[status_old]' => $status_id_old != $status_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskStatus::GetName($status_id_old)) . '</b>' : '',
        '[category]' => HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_new)),
        '[category_old]' => $category_id_old != $category_id_new ? '&lt; ' . JText::_('changed_from') . ' <b>' . HelpdeskUtility::String2HTML(HelpdeskCategory::GetName($category_id_old)) . '</b>' : '',
        '[source]' => HelpdeskUtility::String2HTML($ticket->source),
        '[helpdesk]' => JURI::root(),
        '[messages]' => $messages
    );
    $var_set = array_merge($var_set, $cfields_array);

    /////////////////////
    //  Ticket Reply  ///
    /////////////////////
    $reply_id = 0;
    if (strip_tags($reply_msg) != "" || strip_tags($reply_summary_msg) != "") {
        // Save Ticket Reply
        $reply_id = HelpdeskTicket::Reply($ticket->id, $reply_summary_msg, $reply_msg, $replytime, $travel_time, $tickettravel, $user_rate, $id_activity_rate, $id_activity_type, $start_time, $end_time, $break_time, $reply_date, $reply_hours);
        if ($reply_id) {
            // Notify Assigned User
            if (isset($assigned_old) && $assigned_old->id > 0) {
                $var_set['[recipient]'] = $assigned_old->name;
                SendMailNotification($ticket->id, $var_set, $assigned_old->email, 'updated_mail_subject', 'reply_mail_notify', 'ticket_reply_mail_notify_support', null, null);
            }

            // Add ticket log message
            HelpdeskTicket::Log($ticket->id, str_replace('%1', $ticket->an_name, JText::_('posted_reply')), JText::_('posted_reply_customer'), $ticket->id_status, '', 0, 0, 'add_message.png');

        } else {
            HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
            $errors++;
        }
    }

    // Attached File
    for ($xx = 1; $xx <= $supportConfig->attachs_num; $xx++) {
        if (isset($_FILES['file' . $xx])) {
            if ($_FILES['file' . $xx]['name'] != '') {
                $fileupload = HelpdeskFile::Upload($ticket->id, 'T', "file$xx", $supportConfig->docspath, $_POST['desc' . $xx], 0, 1, '', $reply_id);
                if ($fileupload) {
                    $ticketLogMsg = str_replace('%1', $ticket->an_name, JText::_('attached_file'));
                    $ticketLogMsg = str_replace('%2', $_FILES['file' . $xx]['name'], $ticketLogMsg);
                    HelpdeskTicket::Log($ticket->id, $ticketLogMsg, JText::_('attached_file_hidden'), $ticket->id_status, 'attachfile', '', 0, 'add_attach.png');
                    HelpdeskUtility::AddGlobalMessage(JText::_('upload_ok'), 'i');
                }
            }
        }
    }

    // Update Tickets Last Updated Date
    $database->setQuery("UPDATE #__support_ticket SET last_update='" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "' WHERE id='" . $_POST['id'] . "'");
    !$database->query() ? HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1)) : '';
    if (!$database->query()) $errors++;

    // Redirect
    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . $msg, false);

    // Link to display
    $ticket_url = JRoute::_(JURI::root().'index.php?option=com_maqmahelpdesk&Itemid=' .$Itemid. '&id_workgroup=' .$wkoptions->id. '&task=ticket_view&filter_ticketid='.$ticket->ticketmask.'&filter_email='.urlencode($ticket->an_mail), false);

    if ($errors == 0) {
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('reply_saved_ok'), $ticket_url, $ticket->ticketmask), 'i');
    } else {
        HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('reply_saved_ko'), $errors, $ticket_url, $ticket->ticketmask), 'e');
    }

    $mainframe->redirect($url);
}


function CheckTicket()
{
    $database = JFactory::getDBO();
    $ticketmask = JRequest::getVar('id', 0, '', 'string');

    $sql = "SELECT `id`, `subject`, `id_workgroup`
		FROM `#__support_ticket`
		WHERE `ticketmask` = '$ticketmask'";
    $database->setQuery($sql);
    $ticket = $database->loadObject();

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    if (isset($ticket)) {
        echo '{"output":"OK", "id":"' . $ticket->id . '", "id_workgroup":"' . $ticket->id_workgroup . '", "subject":"' . str_replace('"', '', $ticket->subject) . '"}';
    } else {
        echo '{"output":"ERROR"}';
    }
}


function Sticky()
{
    $database = JFactory::getDBO();
    $is_support = HelpdeskUser::IsSupport();
    $ticket = intval(JRequest::getVar('ticket', 0, '', 'int'));
    $action = intval(JRequest::getVar('action', 0, '', 'int'));

    if ($is_support) {
        $sql = "UPDATE `#__support_ticket`
				SET `sticky`=$action
				WHERE `id` = '$ticket'";
        $database->setQuery($sql);
        echo $database->query() . '|' . ($action ? JText::_('ticket_sticky') : JText::_('ticket_unsticky'));
    }
}

function getAssignList()
{
    global $supportOptions;

    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $is_client = HelpdeskUser::IsClient();
    $is_support = HelpdeskUser::IsSupport();

    $id = JRequest::getInt('id', 0);

    $sql = "SELECT `id_workgroup`
            FROM `#__support_ticket`
            WHERE `id` = $id";
    $database->setQuery($sql);
    $id_workgroup = $database->loadResult();

    $sql = "SELECT DISTINCT(u.`id`), u.`name`, p.level
            FROM #__users as u, #__support_permission as p
            WHERE u.id=p.id_user AND p.id_workgroup='" . $id_workgroup . "' " . ($supportOptions->manager ? '' : 'AND u.id=' . $user->id) . "
            ORDER BY u.name";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    $content = '<ul>';
    foreach ($rows as $row) {
        $content .= '<li>';
        $content .= '<a href="javascript:;" onclick="SetTicketAssignment(' . $id . ',' . $row->id . ');">' . addslashes($row->name . ($row->level ? ' - ' . sprintf(JText::_('SUPPORT_LEVEL_LABEL'), $row->level) : '')) . '</a>';
        $content .= '</li>';
    }
    $content .= '</ul>';

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: text/html');

    echo $content;
}


function manageViews()
{
    global $usertype;

    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $is_support = HelpdeskUser::IsSupport();

    // Get views of the user
    $sql = "SELECT `id`, `id_user`, `name`, `viewtype`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `default`
            FROM `#__support_views`
            WHERE `id_user` = " . ($supportConfig->common_ticket_views ? 0 : $user->id) . "
            ORDER BY `name`";
    $database->setQuery($sql);
    $rows = $database->loadObjectList();

    if ($supportConfig->common_ticket_views && $usertype < 7) {
        HelpdeskUtility::ShowTplMessage(JText::_('common_ticket_views_warning'), $id_workgroup);
    } else {
        $tmplfile = HelpdeskTemplate::GetFile('tickets/views_manager');
        include $tmplfile;
    }
}

function editView()
{
    $database = JFactory::getDBO();
    $supportConfig = HelpdeskUtility::GetConfig();
    $workgroupSettings = HelpdeskDepartment::GetSettings();
    $Itemid = JRequest::getInt('Itemid', 0);
    $id_workgroup = JRequest::getInt('id_workgroup', 0);

    $id = JRequest::getInt('id', 0);

    $sql = "SELECT `id`, `id_user`, `name`, `viewtype`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `default`, `orderby`
            FROM `#__support_views`
            WHERE `id` = $id";
    $database->setQuery($sql);
    $row = $database->loadObject();

    // Prevent errors with defaults - in v4 Joomla structure takes care of this
    if (!isset($row)) {
        $row->id = 0;
        $row->name = null;
        $row->viewtype = null;
        $row->ordering = null;
        $row->orderby = 'ASC';
        $row->operator = null;
        $row->field = null;
        $row->arithmetic = null;
        $row->value = null;
        $row->default = 0;
    }

    // Explode fields
    $operators = explode('|', $row->operator);
    $fields = explode('|', $row->field);
    $arithmetics = explode('|', $row->arithmetic);
    $values = explode('|', $row->value);

    $tmplfile = HelpdeskTemplate::GetFile('tickets/views_edit');
    include $tmplfile;
}

function saveView()
{
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();

    $id = JRequest::getInt('id', 0);
    $id_user = JRequest::getInt('id_user', 0);
    $name = JRequest::getVar('name', '');
    $ordering = JRequest::getVar('ordering', '');
    $orderby = JRequest::getVar('orderby', '');
    $operator = JRequest::getVar('operator', array(0), '', 'array');
    $field = JRequest::getVar('field', array(0), '', 'array');
    $arithmetic = JRequest::getVar('arithmetic', array(0), '', 'array');
    $value = JRequest::getVar('value', array(0), '', 'array');
    $default = JRequest::getInt('default', 0);

    // Convert arrays
    $toperator = "";
    for ($i = 0; $i < count($operator); $i++) {
        $toperator .= $operator[$i] . (count($operator) - $i > 1 ? '|' : "");
    }
    $tfield = "";
    for ($i = 0; $i < count($field); $i++) {
        $tfield .= $field[$i] . (count($field) - $i > 1 ? '|' : "");
    }
    $tarithmetic = "";
    for ($i = 0; $i < count($arithmetic); $i++) {
        $tarithmetic .= $arithmetic[$i] . (count($arithmetic) - $i > 1 ? '|' : "");
    }
    $tvalue = "";
    for ($i = 0; $i < count($value); $i++) {
        $tvalue .= $value[$i] . (count($value) - $i > 1 ? '|' : "");
    }

    // Insert or update in database
    if ($id) {
        $sql = "UPDATE `#__support_views`
                SET `name`=" . $database->quote($name) . ", `ordering`=" . $database->quote($ordering) . ", `orderby`=" . $database->quote($orderby) . ", `operator`=" . $database->quote($toperator) . ", `field`=" . $database->quote($tfield) . ", `arithmetic`=" . $database->quote($tarithmetic) . ", `value`=" . $database->quote($tvalue) . ", `default`=" . $default . "
                WHERE `id`=" . $id;
    } else {
        $sql = "INSERT INTO `#__support_views`(`id_user`, `name`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `default`, `orderby`)
				VALUES(" . ($supportConfig->common_ticket_views ? 0 : $user->id) . ", " . $database->quote($name) . ", " . $database->quote($ordering) . ", " . $database->quote($toperator) . ", " . $database->quote($tfield) . ", " . $database->quote($tarithmetic) . ", " . $database->quote($tvalue) . ", " . $default . ", " . $database->quote($orderby) . ")";
    }

    $database->setQuery($sql);
    $database->query();
    $id = !$id ? $database->insertid() : $id;

    // If default make sure it's the only one
    if ($default) {
        $sql = "UPDATE `#__support_views` 
				SET `default`=0 
				WHERE `id_user`=" . ($supportConfig->common_ticket_views ? 0 : $user->id) . " AND`id`!=" . $id;
        $database->setQuery($sql);
        $database->query();
    }

    manageViews();
}

function deleteView()
{
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $supportConfig = HelpdeskUtility::GetConfig();
    $id = JRequest::getInt('id', 0);

    // Delete the view from the database
    $sql = "DELETE FROM `#__support_views` 
    		WHERE `default`=0 AND `id_user`=" . ($supportConfig->common_ticket_views ? 0 : $user->id) . " AND `id`=" . $id;
    $database->setQuery($sql);
    $database->query();

    manageViews();
}

function deleteTicket()
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $id = JRequest::getInt('id', 0);
	$ids = JRequest::getVar('ids', '', '', 'string');
	$Itemid = JRequest::getInt('Itemid', 0);
	$supportConfig = HelpdeskUtility::GetConfig();

    $sql = "SELECT `can_delete` 
			FROM #__support_permission 
			WHERE id_user='" . $user->id . "' AND id_workgroup='$id_workgroup'";
    $database->setQuery($sql);
    $can_delete = $database->loadResult();

    if ($can_delete) {
	    $id = ($ids != '' ? JString::substr($ids,0,strlen($ids)-1) : $id);

        // Delete ticket
        $sql = "DELETE FROM `#__support_ticket` 
				WHERE `id` IN ($id)";
        $database->setQuery($sql);
        $database->query();
        // Delete ticket messages
        $sql = "DELETE FROM `#__support_ticket_resp` 
				WHERE `id_ticket` IN ($id)";
        $database->setQuery($sql);
        $database->query();
        // Delete ticket logs
        $sql = "DELETE FROM `#__support_log` 
				WHERE `id_ticket` IN ($id)";
        $database->setQuery($sql);
        $database->query();
	    // Get attachment details and delete physically
	    $sql = "SELECT `filename`
				FROM `#__support_file`
				WHERE `id` IN ($id) AND `source`='T'";
	    $database->setQuery($sql);
	    $attachments = $database->loadObjectList();
	    for ($i=0; $i<count($attachments); $i++)
	    {
		    unlink($supportConfig->docspath . '/' . $attachments[$i]->filename);
	    }
	    // Delete ticket attachments
	    $sql = "DELETE FROM `#__support_file`
				WHERE `id` IN ($id) AND `source`='T'";
	    $database->setQuery($sql);
	    $database->query();
        // Set message
        $msg = urlencode(JText::_('delete_message'));
        $msgtype = 'i';
    } else {
        $msg = urlencode(JText::_('delete_ticket_error'));
        $msgtype = 'e';
    }

    $url = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . $msg . '&msgtype=' . $msgtype, false);
    $mainframe->redirect($url);
}

function approveTicket()
{
    $mainframe = JFactory::getApplication();
    $database = JFactory::getDBO();
    $user = JFactory::getUser();
    $is_client = HelpdeskUser::IsClient();
    $id_workgroup = JRequest::getInt('id_workgroup', 0);
    $Itemid = JRequest::getInt('Itemid', 0);
    $id = JRequest::getVar('id', 0, '', 'int');

    // Check if user is manager
    $sql = "SELECT `manager` 
			FROM #__support_client_users 
			WHERE id_client=$is_client AND id_user=" . (int)$user->id;
    $database->setQuery($sql);
    $manager = $database->loadResult();

    if ($manager) {
        // Approve ticket
        $sql = "UPDATE `#__support_ticket` 
				SET `approved`=1
				WHERE id_client=$is_client AND `id`=$id";
        $database->setQuery($sql);
        $database->query();

        // Save in log
        HelpdeskTicket::Log($ticket->id, str_replace('%s', $user->name, JText::_('approve_log')), JText::_('approve_log'), 0, '', 0, 0, 'approved.png');

        // Get ticket details
        $sql = "SELECT t.`ticketmask`, u.email, u.name, t.`an_name`, t.`an_mail`
				FROM `#__support_ticket` AS t
					 LEFT JOIN `#__users` AS u ON u.`id`=t.`assign_to`
				WHERE t.`id`=$id";
        $database->setQuery($sql);
        $ticket = $database->loadObject();

        // Notify
        $ticket_url = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $id, false);
        $subject = sprintf(JText::_('MAIL_APPROVE_SUBJECT'), $ticket->ticketmask);
        $body = sprintf(JText::_('MAIL_APPROVE_BODY'), $ticket->ticketmask, $user->name, $ticket_url);
        jimport('joomla.mail.helper');
        $mailer = JFactory::getMailer();
        $mailer->setSender(array($CONFIG->mailfrom, $CONFIG->fromname));
        $mailer->addRecipient($ticket->an_mail);
        if ($ticket->name != '') {
            $mailer->addBCC($ticket->email);
        }
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->IsHTML(true);
        $sendmail = $mailer->Send();

        // Set message
        $msg = JText::_('approve_message');
    } else {
        $msg = JText::_('approve_error');
    }

    $url = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my&msg=' . $msg;
    $mainframe->redirect($url);
}

/*
 * Send a notification e-mail
 *
 * @param int 		The id of the ticket
 * @param array 	The variables available
 * @param string	Recipient email
 * @param boolean	If it's a message or a change
 */
function SendMailNotification($ticketid, $vars, $recipient, $subject, $body, $tmplfile, $cc = null, $bcc = null, $attachments=null)
{
    $CONFIG = new JConfig();
    $database = JFactory::getDBO();
    $supportConfig = HelpdeskUtility::GetConfig();
    $is_support = HelpdeskUser::IsSupport();
    $id_workgroup = JRequest::getInt('id_workgroup', 0);

    // Get Workgroup Options
    $sql = "SELECT *
            FROM #__support_workgroup
            WHERE id=" . $id_workgroup;
	$database->setQuery($sql);
    $wkoptions = $database->loadObject();

    // Set sender
    if ($wkoptions->wkmail_address == '')
    {
        $sender_name = $CONFIG->fromname;
        $sender_mail = $CONFIG->mailfrom;
    }
    else
    {
        $sender_name = $wkoptions->wkmail_address_name;
        $sender_mail = $wkoptions->wkmail_address;
    }

    // Clean empty
    if (is_array($cc)) {
        $cc = array_filter($cc);
    }
    if (is_array($bcc)) {
        $bcc = array_filter($bcc);
    }

    // Prepare Subject and Body
    $subject = HelpdeskTemplate::Parse($vars, '', JText::_($subject));
    $message = HelpdeskTemplate::Parse($vars, '', JText::_($body));
    $vars = array_merge($vars, array('[intro]' => $message));
    $body = HelpdeskTemplate::Parse($vars, $tmplfile);

    if ($wkoptions->add_mail_tag == 2) {
        $body = JText::_("mail_tag_start") . '<br>' . JText::_("mail_tag") . $body;
    } elseif ($wkoptions->add_mail_tag == 1) {
        $body = JText::_("mail_tag") . $body;
    }

    // Ticket messages history
    $messages_start = (stripos($body, '<!-- messages:start -->') - 1);
    $messages_end = stripos($body, '<!-- messages:end -->');
    if ($messages_start !== false && $messages_end !== false)
    {
        $body = JString::substr($body, 0, $messages_start) . '[messages]' . JString::substr($body, ($messages_end + 21));
        $body = str_replace('[messages]', $vars['[messages]'], $body);
    }

    //print '<p>---------------------------------------------------</p>TO: ';
    //print_r($recipient);
    // Send email or put in queue
    if ($supportConfig->mail_queue) {
        //echo '<p>Queue: '.$tmplfile;
        $sendmail = PutMailOnQueue($recipient, $subject, $body, $sender_mail, $sender_name, $cc, $bcc);
    } else {
        jimport('joomla.mail.helper');
        unset($mailer);
        $mailer = JFactory::getMailer();
        $mailer->ClearAllRecipients();
        if (count($cc)) {
            $mailer->addCC($cc);
        }
        if (count($bcc)) {
            $mailer->addBCC($bcc);
        }
	    if (is_array($attachments) || $attachments!='')
	    {
		    $mailer->addAttachment($attachments);
	    }
        $mailer->setSender(array($sender_mail, $sender_name));
        $mailer->addRecipient($recipient);
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->IsHTML(true);
        $sendmail = $mailer->Send();
    }

    // JText::_('log_mail')			%s - email address			%s - success or insucess
    // JText::_('log_mail_hidden')	%s - success or insucess
    $msg_support = !isset($sendmail->message) ? JText::_('success') : JText::_('unsucess') . '<br /><i>(' . $sendmail->message . ')</i>';
    $msg_support = sprintf(JText::_('log_mail'), $recipient, $msg_support);
    $msg_hidden = !isset($sendmail->message) ? JText::_('success') : JText::_('unsucess') . '<br /><i>(' . $sendmail->message . ')</i>';
    $msg_hidden = sprintf(JText::_('log_mail_hidden'), $msg_hidden);

    // Add in the log notification about mail sending
    HelpdeskTicket::Log($ticketid, $msg_support, $msg_hidden, 0, 0, 0, 0, 'email.png');

    // Add CC log notification
    //print '<p>CC na funcao de notificao: '; print_r($cc);
    if (count($cc)) {
        $cc = implode(', ', $cc);
        $msg = JText::_('cc') . ': ' . $cc;
        HelpdeskTicket::Log($ticketid, $msg, $msg, 0, 0, 0, 0, 'email.png');
    }

    // Add BCC log notification
    //print '<p>BCC na funcao de notificao: '; print_r($bcc);
    if (count($bcc)) {
        $bcc = implode(', ', $bcc);
        $msg = JText::_('bcc') . ': ' . $bcc;
        HelpdeskTicket::Log($ticketid, $msg, $msg, 0, 0, 0, 0, 'email.png');
    }
}

/*
 * Put email into queue to be processed by cron
 * 
 * @param string 	Recipient email address
 * @param string 	Subject
 * @param string	Body
 * @param string	From email address
 * @param string	From name
 */
function PutMailOnQueue($recipient, $subject, $body, $wkmail, $wkmail_name, $cc = null, $bcc = null)
{
    $database = JFactory::getDBO();
    $sql = "INSERT INTO `#__support_mail_queue`(`usermail`, `subject`, `body`, `wkmail`, `wkmail_name`, `date_created`, `cc`, `bcc`) 
			VALUES(" . $database->quote($recipient) . ", " . $database->quote($subject) . ", " . $database->quote($body) . ", " . $database->quote($wkmail) . ", " . $database->quote($wkmail_name) . ", '" . date("Y-m-d H:i:s") . "', '" . (count($cc) ? implode(',', $cc) : '') . "', '" . (count($bcc) ? implode(',', $bcc) : '') . "')";
    $database->setQuery($sql);
    return $database->query();
}

function MakeParent()
{
    $is_support = HelpdeskUser::IsSupport();

    if ($is_support) {
        $database = JFactory::getDBO();
        $ticket = intval(JRequest::getVar('ticket', 0, '', 'int'));
        $parent = intval(JRequest::getVar('parent', 0, '', 'int'));

        // Get ID of parent ticket
        $sql = "SELECT `id`
				FROM `#__support_ticket`
				WHERE `ticketmask`='$parent'";
        $database->setQuery($sql);
        $id_parent = $database->loadResult();

        // Get ID of child ticket
        $sql = "SELECT `id`
				FROM `#__support_ticket`
				WHERE `ticketmask`='$ticket'";
        $database->setQuery($sql);
        $id_ticket = $database->loadResult();

        // Inserts in log of parent
        HelpdeskTicket::Log($id_parent, sprintf(JText::_('ticket_is_parent'), $ticket), sprintf(JText::_('ticket_is_parent'), $ticket), '', '', 0, 0, 'link.png');

        // Inserts in log of child
        HelpdeskTicket::Log($id_ticket, sprintf(JText::_('ticket_is_child'), $parent), sprintf(JText::_('ticket_is_child'), $parent), '', '', 0, 0, 'link.png');

        // Updates the database
        $sql = "UPDATE `#__support_ticket`
				SET `id_ticket_parent`=$id_parent
				WHERE `id` = '$id_ticket'";
        $database->setQuery($sql);
        $database->query();

		HelpdeskUtility::AddGlobalMessage(sprintf(JText::_('ticket_is_child'), $parent), 'i');

        echo 1;
    }
}