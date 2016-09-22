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

include_once (JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php');

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/timesheet.php';

$year = JRequest::getVar('year', date("Y"), '', 'int');
$month = JRequest::getVar('month', date("m"), '', 'int');
$id_client = JRequest::getVar('id_client', 0, '', 'int');
$type = JRequest::getVar('type', 'D', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'timesheet', $task);

switch ($task)
{
	case 'edit':
		$is_support ? editTimesheet() : HelpdeskValidation::NoAccessQuit();
		break;

	case 'save':
		$is_support ? saveTimesheet() : HelpdeskValidation::NoAccessQuit();
		break;

	case 'delete':
		$is_support ? deleteTimesheet() : HelpdeskValidation::NoAccessQuit();
		break;

	case 'manage':
		$is_support ? manageTimesheet() : HelpdeskValidation::NoAccessQuit();
		break;

	default:
		HelpdeskValidation::ValidPermissions($task, 'TM') ? showTimesheet($year, $month, $id_client, $type) : HelpdeskValidation::NoAccessQuit();
		break;
}

function manageTimesheet()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$month = JRequest::getVar('month', date("m"), "", "string");
	$year = JRequest::getVar('year', date("Y"), "", "string");

	$years[] = JHTML::_('select.option', '00', JText::_('select3'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Get times for selected year/month
	$sql = "SELECT t.`day`, c.`clientname`, TIME_TO_SEC(REPLACE(t.`time`,'.', ':')) AS total
			FROM `#__support_timesheet` AS t
				 INNER JOIN `#__support_client` AS c ON c.`id`=t.`id_client`
			WHERE t.`month`='" . $month . "'
			  AND t.`year`='" . $year . "'
			  AND t.`id_user`=" . $user->id . "
			ORDER BY t.`day`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('times/times');
	include $tmplfile;
}

function editTimesheet()
{
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);

	HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('timepicker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('times.form.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$document->addScriptDeclaration('var MQM_LOADING = "' . addslashes(JText::_('loading')) . '";');
	$document->addScriptDeclaration('var MQM_CLIENT = "' . addslashes(JText::_('CLIENT_IS_REQUIRED')) . '";');
	$document->addScriptDeclaration('var MQM_YEAR = "' . addslashes(JText::_('YEAR_IS_REQUIRED')) . '";');
	$document->addScriptDeclaration('var MQM_MONTH = "' . addslashes(JText::_('MONTH_IS_REQUIRED')) . '";');
	$document->addScriptDeclaration('var MQM_DAY = "' . addslashes(JText::_('DAY_IS_REQUIRED')) . '";');
	$document->addScriptDeclaration('var MQM_TIME = "' . addslashes(JText::_('TIME_IS_REQUIRED')) . '";');

	// Get row
	$row = new MaQmaHelpdeskTableTimesheet($database);
	$row->load($id);

	// Get client name
	$sql = "SELECT `clientname`
			FROM `#__support_client`
			WHERE `id`=" . $row->id_client;
	$database->setQuery($sql);
	$row->clientname = $database->loadResult();

	// Year select list
	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++)
	{
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1"', 'value', 'text', $row->year);

	// Month select list
	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1"', 'value', 'text', $row->month);

	// Day select list
	$days[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 1; $i <= 31; $i++)
	{
		$days[] = JHTML::_('select.option', str_pad($i, 2, "0", STR_PAD_LEFT), str_pad($i, 2, "0", STR_PAD_LEFT));
	}
	$lists['day'] = JHTML::_('select.genericlist', $days, 'day', 'class="inputbox" size="1"', 'value', 'text', $row->day);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('times/form');
	include $tmplfile;
}

function saveTimesheet()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);
	$client = JRequest::getInt('id_client', 0);
	$year = JRequest::getVar('year', '', '', 'string');
	$month = JRequest::getVar('month', '', '', 'string');
	$day = JRequest::getVar('day', '', '', 'string');
	$time = JRequest::getVar('time', '', '', 'string');

	if ($id)
	{
		$sql = "UPDATE `#__support_timesheet`
				SET `year`='" . $year . "',
					`month`='" . $month . "',
					`day`='" . $day . "',
					`time`='" . $time . "',
					`id_client`='" . $client . "'
				WHERE `id`=" . $id . "
				  AND `id_user`=" . $user->id;
	}
	else
	{
		$sql = "INSERT INTO `#__support_timesheet`(`id_client`, `id_user`, `year`, `month`, `day`, `time`)
				VALUES(" . $client . ", " . $user->id . ", '" . $year . "', '" . $month . "', '" . $day . "', '" . $time . "')";
	}
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=timesheet_manage');
}

function deleteTimesheet()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);

	$sql = "DELETE FROM `#__support_timesheet`
			WHERE `id`=" . $id . "
			  AND `id_user`=" . $user->id;
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=timesheet_manage');
}

function showTimesheet($year, $month, $id_client, $type)
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$user = JFactory::getUser();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	// Sets the title
	HelpdeskUtility::PageTitle('showTimesheet');
	$document->title = JText::_('timesheet');

	$years[] = JHTML::_('select.option', '00', JText::_('select3'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	$reporting = new SupportReports();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('reports/timesheet');
	include $tmplfile;
}
