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

// Required helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/schedule.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/schedule.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/schedule/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/schedule/tmpl/edit.php";

// Set toolbar and page title
HelpdeskScheduleAdminHelper::addToolbar($task);
HelpdeskScheduleAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'schedule', $task, $cid[0]);

switch ($task) {

	case "new":
		editSchedule(0);
		break;

	case "edit":
		editSchedule($cid[0]); // edita o 1� selecionado das checkboxes
		break;

	case "save":
		saveSchedule();
		break;

	case "remove":
		removeSchedule($cid); // pode eliminar v�rios (com checkbox)
		break;

	default:
		showSchedule();
		break;
}

function showSchedule()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(profile) FROM #__support_schedule ");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	if ($total > 0) {
		$database->setQuery("SELECT * FROM #__support_schedule ", $pageNav->limitstart, $pageNav->limit);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	}
	MaQmaHtmlDefault::display($rows, $pageNav);
}


function editSchedule($sid = 0)
{
	$database = JFactory::getDBO();
	$scheduleInfo = new MaQmaHelpdeskTableSchedule($database);
	$scheduleInfo->load($sid);
	HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['work_on_holidays'] = HelpdeskForm::SwitchCheckbox('radio', 'work_on_holidays', $captions, $values, $scheduleInfo->work_on_holidays, 'switch');

	// weekdays aqui
	$schedule_weekdayInfo = null;
	if ($sid != 0) {
		$database->setQuery("SELECT * FROM #__support_schedule_weekday WHERE id_schedule='" . $sid . "' ORDER BY weekday ");
		$schedule_weekdayInfo = $database->loadObjectList();
	}

	$listweekday = array('', JText::_('week_full_monday'), JText::_('week_full_tuesday'), JText::_('week_full_wednesday'), JText::_('week_full_thursday'), JText::_('week_full_friday'), JText::_('week_full_saturday'), JText::_('week_full_sunday'));
	$lists['listweekday'] = $listweekday;


	for ($w = 1; $w < 8; $w++) {
		$week_array[$w]['id'] = 0;
		$week_array[$w]['weekday'] = $w;
		$week_array[$w]['weekdayname'] = $listweekday[$w];
		$week_array[$w]['work_start'] = '00:00';
		$week_array[$w]['work_end'] = '00:00';
		$week_array[$w]['break_start'] = '00:00';
		$week_array[$w]['break_end'] = '00:00';
	}

	for ($n = 0; $n < sizeof($schedule_weekdayInfo); $n++) {
		$indice = $schedule_weekdayInfo[$n]->weekday;
		$week_array[$indice]['id'] = $schedule_weekdayInfo[$n]->id; // para hidden field
		$week_array[$indice]['work_start'] = $schedule_weekdayInfo[$n]->work_start;
		$week_array[$indice]['work_end'] = $schedule_weekdayInfo[$n]->work_end;
		$week_array[$indice]['break_start'] = $schedule_weekdayInfo[$n]->break_start;
		$week_array[$indice]['break_end'] = $schedule_weekdayInfo[$n]->break_end;

	}
	$lists['schedule_weekdayInfo'] = $week_array;

	MaQmaHtmlEdit::display($scheduleInfo, $lists);

}


function saveSchedule()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new  MaQmaHelpdeskTableSchedule($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}


	$schedule_id = array();
	foreach ($_POST['schedule_id'] AS $k => $v) {
		$schedule_id[] = $v;
	}

	$schedule_weekday = array();
	foreach ($_POST['schedule_weekday'] AS $k => $v) {
		$schedule_weekday[] = $v;
	}

	$schedule_work_start = array();
	foreach ($_POST['schedule_work_start'] AS $k => $v) {
		$schedule_work_start[] = $v;
	}

	$schedule_work_end = array();
	foreach ($_POST['schedule_work_end'] AS $k => $v) {
		$schedule_work_end[] = $v;
	}

	$schedule_break_start = array();
	foreach ($_POST['schedule_break_start'] AS $k => $v) {
		$schedule_break_start[] = $v;
	}

	$schedule_break_end = array();
	foreach ($_POST['schedule_break_end'] AS $k => $v) {
		$schedule_break_end[] = $v;
	}

	for ($w = 0; $w < 7; $w++) {
		$database->setQuery("SELECT * FROM #__support_schedule_weekday WHERE id = '" . $schedule_id[$w] . "' AND id_schedule = '" . $row->id . "' AND weekday = '" . $schedule_weekday[$w] . "' ");
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		if ($rows) {
			if ($schedule_work_start[$w] == '' && $schedule_work_end[$w] == '') {
				// delete
				$database->setQuery("DELETE FROM #__support_schedule_weekday WHERE `id` = '" . $schedule_id[$w] . "'  AND `id_schedule` = '" . $row->id . "' ");
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			} else {
				// update
				$query = "UPDATE #__support_schedule_weekday SET `work_start` = '" . $schedule_work_start[$w] . "', `work_end` = '" . $schedule_work_end[$w] . "', `break_start` = '" . $schedule_break_start[$w] . "', `break_end` = '" . $schedule_break_end[$w] . "' WHERE `id` = '" . $schedule_id[$w] . "' AND `id_schedule` = '" . $row->id . "' ";
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		} else {
			if ($schedule_work_start[$w] != '' && $schedule_work_end[$w] != '') {
				// insert
				$query = "INSERT INTO #__support_schedule_weekday (`id`, `id_schedule`, `weekday`, `work_start`, `work_end`, `break_start`, `break_end` ) VALUES ( '', '" . $row->id . "', '" . $schedule_weekday[$w] . "', '" . $schedule_work_start[$w] . "', '" . $schedule_work_end[$w] . "', '" . $schedule_break_start[$w] . "', '" . $schedule_break_end[$w] . "' ) ";
				$database->setQuery($query);
				if (!$database->query()) {
					echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=schedule");
}


function removeSchedule($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// pode escolher mais de uma checkbox para eliminar
	if (!is_array($cid) || count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('todo') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_schedule WHERE id IN ($cids)");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		$database->setQuery("DELETE FROM #__support_schedule_weekday WHERE `id_schedule` IN ($cids)");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=schedule");
}

?>