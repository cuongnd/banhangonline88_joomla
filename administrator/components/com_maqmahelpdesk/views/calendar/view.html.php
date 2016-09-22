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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/department.php');
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/tasks.php';
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/template.php');

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/task.php';

// HTML dependency
require_once("components/com_maqmahelpdesk/views/calendar/tmpl/default.php");

// Set toolbar and page title
HelpdeskTasksAdminHelper::addToolbar($task);
HelpdeskTasksAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid))
{
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'calendar', $task);

switch ($task) {
	case "save":
		saveCalendar();
		break;

	case "new":
		editCalendar();
		break;
}

function editCalendar()
{
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$document->addScriptDeclaration('var IMQM_USER_REQUIRED = "' . addslashes(JText::_('user_required')) . '";');
	$document->addScriptDeclaration('var IMQM_DATE_REQUIRED = "' . addslashes(JText::_('date_required')) . '";');
	$document->addScriptDeclaration('var IMQM_HOURS_REQUIRED = "' . addslashes(JText::_('hours_required')) . '";');

	$row = new MaQmaHelpdeskTableTask($database);
	$row->load(0);

	HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('calendar_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission p, #__users u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$lists['id_user'] = JHTML::_('select.genericlist', $rows_staff, 'id_user[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', 0);

	$task_date = '';
	$task_date = $row->date_time;

	// Build Date without time
	$date_task = '';
	$date_task = JString::substr($task_date, 0, 10);

	// Build Hours without time
	$hour_task = '';
	$hour_task = JString::substr($task_date, 11, -6);

	// Build Mins without time
	$mins_task = '';
	$mins_task = JString::substr($task_date, 14, -3);

	// Gets the ticket ID and Subject (only if task is associated with one ticket)
	$sql = "SELECT ticketmask, subject FROM #__support_ticket WHERE id='" . $row->id_ticket . "'";
	$database->setQuery($sql);
	$ticket_info = null;
	$ticket_info = $database->loadObject();

	MaQmaHtmlEdit::display($row, $lists, $date_task, $hour_task, $mins_task, $ticket_info);
}

function saveCalendar()
{
	$database = JFactory::getDBO();
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$date = JRequest::getVar('date_time', '', '', 'string');
	$taskmin = JRequest::getVar('taskmin', '', '', 'string');
	$status = JRequest::getVar('status', '', '', 'string');
	$id_user = JRequest::getVar('id_user', '', '', 'array');
	$users = implode(',', $id_user);

	$taskfield = JRequest::getVar('taskfield', '', 'POST', 'string', 4);
	$date = $date . " " . $taskmin . ":00";
	$database->setQuery("INSERT INTO #__support_task(id_ticket, id_user, date_time, task, status) VALUES('0', " . $database->quote($users) . ", " . $database->quote($date) . ", " . $database->quote($taskfield) . ", '" . $status . "')");
	$database->query();

	// Informs the user about the task in case it's not the logged user
	if ($user->id != $id_user) {
		$taskuser = null;
		$database->setQuery("SELECT name, email FROM #__users WHERE id='" . $id_user . "'");
		$taskuser = $database->loadObject();

		$tmpl_code->htmlcode = HelpdeskTemplate::Get('', 0, 'mail/task_created');

		// Replaces message body variables for the values
		$msginfo->message = str_replace("%ticket", '-', $tmpl_code->htmlcode); // Replaces the %ticket
		$msginfo->message = str_replace("%staff", $taskuser->name, $msginfo->message); // Replaces the %staff
		$msginfo->message = str_replace("%task", JRequest::getVar('taskfield', '', 'POST', 'string', 4), $msginfo->message); // Replaces the %message
		$msginfo->message = str_replace("%calendar", JURI::root(), $msginfo->message); // Replaces the %email
		$msginfo->message = str_replace("%url", JURI::root(), $msginfo->message); // Replaces the %url

		/*$sendmail = JUtility::sendMail( $CONFIG->mailfrom, $CONFIG->sitename, $taskuser->email, JText::_('tsk_notify_new_subj'), $msginfo->message, 1 );
		  if( $sendmail != true ) {
			  print JText::_('mail_error');
		  }*/
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk", JText::_('task_saved'));
}
