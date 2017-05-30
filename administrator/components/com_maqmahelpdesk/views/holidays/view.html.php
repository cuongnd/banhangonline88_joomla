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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/holidays.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/holidays.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/holidays/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/holidays/tmpl/edit.php";

// Set toolbar and page title
HelpdeskHolidaysAdminHelper::addToolbar($task);
HelpdeskHolidaysAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'holidays', $task, $cid[0]);

switch ($task) {

	case "new":
		editHoliday(0);
		break;

	case "edit":
		editHoliday($cid[0]); // edita o 1� selecionado das checkboxes
		break;

	case "save":
		saveHoliday();
		break;

	case "remove":
		removeHoliday($cid); // pode eliminar v�rios (com checkbox)
		break;

	default:
		showHoliday();
		break;
}

function showHoliday()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	$sql = "SELECT count(h.holiday_date)
			FROM #__support_holidays h 
			WHERE (YEAR(h.holiday_date)='0000' OR h.holiday_date > CURDATE() ) 
			ORDER BY h.holiday_date DESC";
	$database->setQuery($sql);
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	if ($total > 0) {
		$sql = "SELECT *
				FROM #__support_holidays h 
				WHERE (YEAR(h.holiday_date)='0000' OR h.holiday_date > CURDATE()) 
				ORDER BY h.holiday_date ";
		$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);

		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	}

	MaQmaHtmlDefault::display($rows, $pageNav);

}

function editHoliday($hid = 0)
{
	$database = JFactory::getDBO();
	$holidayInfo = new MaQmaHelpdeskTableHolidays($database);
	$holidayInfo->load($hid);

	$lists = "";

	MaQmaHtmlEdit::display($holidayInfo, $lists);

}

function saveHoliday()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new  MaQmaHelpdeskTableHolidays($database);
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

//	$row->checkin();

	$id = intval(JRequest::getVar('id', '', '', 'int'));
	$holiday_date = JRequest::getVar('holiday_date', '0000-00-00', '', 'string');
	$name = JRequest::getVar('name', '', '', 'string');

	$query = "UPDATE #__support_holidays SET `holiday_date` = " . $database->quote($holiday_date) . ", `name` = " . $database->quote($name) . " WHERE `id` = " . $id . " ";
	$database->setQuery($query);
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=holidays");
}


function removeHoliday($cid)
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
		$database->setQuery("DELETE FROM #__support_holidays WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=holidays");
}

?>