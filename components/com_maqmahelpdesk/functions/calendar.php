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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/task.php');

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/task.php';

$id = JRequest::getVar('id', 0, '', 'int');
$month = JRequest::getVar('month', HelpdeskDate::DateOffset("%m"), '', 'int');
$year = JRequest::getVar('year', HelpdeskDate::DateOffset("%Y"), '', 'int');
$date = JRequest::getVar('date', HelpdeskDate::DateOffset("%Y-%m-%d"), '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'calendar', $task, $id);

switch ($task)
{
	case "view":
		showCalendar('M', $month, $year, '');
		break;

	case "add":
		showEdit(0);
		break;

	case "edit":
		$user->id > 0 && isMineCal($id) ? showEdit($id) : JError::raiseError(403, JText::_("ALERTNOTAUTH"));
		break;

	case "save":
		showSave();
		break;

	case "delete":
		$user->id > 0 && isMineCal($id) ? showDelete($id) : JError::raiseError(403, JText::_("ALERTNOTAUTH"));
		break;

	case "week":
		showCalendar('W', $month, $year, $date);
		break;

	case "day":
		showCalendar('D', $month, $year, $date);
		break;

	case "list":
		showCalendar('L', $month, $year, '');
		break;
}

function isMineCal($id = 0)
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$sql = "SELECT COUNT(*)
			FROM #__support_task 
			WHERE id_user IN (" . $user->id . ") AND id=" . $id;
	$database->setQuery($sql);
	if ($database->loadResult() > 0) {
		return true;
	} else {
		return false;
	}
}

function showCalendar($report, $month, $year, $date)
{
	global $supportOptions, $print;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	HelpdeskUtility::AppendResource('tasks.view.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Sets the title
	HelpdeskUtility::PageTitle('showCalendar');

	$imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/';

	// Year and Month filters
	if ($month == '') {
		$month = HelpdeskDate::DateOffset("%m");
	}

	if ($year == '') {
		$year = HelpdeskDate::DateOffset("%Y");
	}

	if ($month == HelpdeskDate::DateOffset("%m") && $year == HelpdeskDate::DateOffset("%Y")) {
		$day = HelpdeskDate::DateOffset("%d");
	} else {
		$day = 0;
	}

	if ($date == '') {
		$date = HelpdeskDate::DateOffset("%Y-%m-%d");
	} else {
		$part_date = explode('-', $date);
		$day = $part_date[2];
		$month = $part_date[1];
		$year = $part_date[0];
	}

	$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=';

	$task = JRequest::getVar('task', 'view', '', 'string');
	$taskcheck = explode('_', $task);
	switch ($taskcheck[1]) {
		case 'view':
			$tmplfile = 'month';
			break;
		case 'list':
			$tmplfile = 'list';
			break;
		case 'day':
			$tmplfile = 'day';
			break;
		case 'week':
			$tmplfile = 'week';
			break;
	}

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('tasks/' . $tmplfile);
	include $tmplfile;
}

function showEdit($id)
{
	global $supportOptions, $is_manager;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$database = JFactory::getDBO();
	$document = & JFactory::getDocument();
	$editor = JFactory::getEditor();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	$document->addScriptDeclaration( 'var MQM_NEGATIVE_LABOUR = "'.JText::_('labour_negative').'";' );
	$document->addScriptDeclaration( 'var MQM_URL_DELETE = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=calendar_delete&id=' . $id).'";' );
	$document->addScriptDeclaration( 'var MQM_URL_CANCEL = "'.JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=calendar_view').'";' );
	HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('tasks.form.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	
	// Sets the title
	HelpdeskUtility::PageTitle('editCalendar', ($id ? JText::_('pathway_edit') : JText::_('pathway_new')));

	$row = new MaQmaHelpdeskTableTask($database);
	$row->load($id);

	// Build Date without time
	$date_task = '';
	$date_task = JString::substr($row->date_time, 0, 10);

	// Get Activity Rate Default
	$database->setQuery("SELECT id FROM #__support_activity_rate WHERE isdefault='1'");
	$default_rate = $database->loadResult();

	// Build Activity Rate select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_rate WHERE published='1' ORDER BY description";
	$database->setQuery($sql);
	$rows_actrate = $database->loadObjectList();
	$lists['task_rate'] = JHTML::_('select.genericlist', $rows_actrate, 'activity_rate', 'class="inputbox" size="1"', 'value', 'text', $default_rate);

	// Get Activity Type Default
	$database->setQuery("SELECT id FROM #__support_activity_type WHERE isdefault='1'");
	$default_type = $database->loadResult();

	// Build Activity Type select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_type WHERE published='1' ORDER BY description";
	$database->setQuery($sql);
	$rows_acttype = $database->loadObjectList();
	$lists['task_type'] = JHTML::_('select.genericlist', $rows_acttype, 'activity_type', 'class="inputbox" size="1"', 'value', 'text', $default_type);

	// Build the Travel yes/no list
	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['task_travel'] = JHTML::_('select.booleanlist', 'tasktravel', 'class="inputbox" onclick="SetTravelTime();"', 1, JText::_('MQ_YES'), JText::_('MQ_NO'));
	$lists['taskstatus'] = HelpdeskForm::SwitchCheckbox('radio', 'status', $captions, $values, ($row->status == 'O' ? 1 : 0), 'switch');
	$clienttravel = 0;

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission as p, #__users as u WHERE p.id_user=u.id AND p.id_workgroup='" . $id_workgroup . "' ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$lists['id_user'] = JHTML::_('select.genericlist', $rows_staff, 'id_user[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', 0);

	if ($row->id_ticket > 0) {
		// Ticket Number
		$database->setQuery("SELECT ticketmask FROM #__support_ticket WHERE id='" . $row->id_ticket . "'");
		$ticket_mask = $database->loadResult();
		$ticket_link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $row->id_ticket);
	}

	ob_start();
	echo JHTML::Calendar($date_task, 'date', 'date', '%Y-%m-%d', array('class' => 'span2', 'maxlength' => '10'));
	$content_date = ob_get_contents();
	ob_end_clean();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('tasks/form');
	include $tmplfile;
}

function showDelete($id)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();

	$sql = "delete from #__support_task where id='" . $id . "'";
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect(JRoute::_("index.php?option=com_maqmahelpdesk&id_workgroup=" . $id_workgroup . "&Itemid=" . $Itemid . "&task=calendar_view&msg=" . JText::_('task_deleted')));
}

function showSave()
{
	global $supportOptions, $is_manager;

	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$is_client = HelpdeskUser::IsClient();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$CONFIG = new JConfig();
	$id = JRequest::getVar('id', '', 'POST', 'int');
	$id_user = JRequest::getVar('id_user', '', 'POST', 'array');
	$date = JRequest::getVar('date', '', 'POST', 'string');
	$taskfield = JRequest::getVar('taskfield', '', 'POST', 'string', 4);
	$status = JRequest::getVar('status', '', 'POST', 'string');
	$tasktime = JRequest::getVar('time', '', 'POST', 'string');
	$date = $date . " " . $tasktime . ":00";
	$users = implode(',', $id_user);

	if ($_POST['id'] == 0) {
		$database->setQuery("INSERT INTO #__support_task(id_ticket, id_user, date_time, task, status) VALUES('" . $id . "', " . $database->quote($users) . ", " . $database->quote($date) . ", " . $database->quote($taskfield) . ", " . $database->quote($status) . ")");
		$msg = JText::_('task_created');
	} else {
		$database->setQuery("UPDATE #__support_task SET id_user=" . $database->quote($users) . ", date_time=" . $database->quote($date) . ", task=" . $database->quote($taskfield) . ", status=" . $database->quote($status) . " WHERE id='" . $id . "'");
		$msg = JText::_('task_updated');
	}
	$database->query();

	// Informs the user about the task in case it's not the logged user
	if ($user->id != $_POST['id_user']) {
		// Get Assigned User
		$database->setQuery("SELECT name, email FROM #__users WHERE id=" . $database->quote($_POST['id_user']));
		$taskuser = null;
		$taskuser = $database->loadObject();

		$htmlcode = HelpdeskTemplate::Get('', $id_workgroup, 'mail/task_created');

		// Replaces message body variables for the values
		$msginfo->message = str_replace("%ticket", '-', $htmlcode);
		$msginfo->message = str_replace("%staff", $taskuser->name, $msginfo->message);
		$msginfo->message = str_replace("%task", JRequest::getVar('taskfield', '', 'POST', 'string'), $msginfo->message);
		$msginfo->message = str_replace("%calendar", JURI::root(), $msginfo->message);
		$msginfo->message = str_replace("%url", JURI::root(), $msginfo->message);

		/*$sendmail1 = JUtility::sendMail( ($wkoptions->wkadmin_email!='' ? $wkoptions->wkadmin_email : $CONFIG->mailfrom), ($wkoptions->wkmail_address_name!='' ? $wkoptions->wkmail_address_name : $CONFIG->sitename)." <".($wkoptions->wkadmin_email!='' ? $wkoptions->wkadmin_email : $CONFIG->fromname).">", $taskuser->email, JText::_('tsk_notify_new_subj'), $msginfo->message, 1 );

		if($sendmail1!=true) {
			$msg .= "MAIL: An error ocurred while sending the mail to assigned user!";
		}*/
	}

	$mainframe->redirect(JRoute::_("index.php?option=com_maqmahelpdesk&id_workgroup=" . $id_workgroup . "&Itemid=" . $Itemid . "&task=calendar_view&msg=" . $msg));
}
