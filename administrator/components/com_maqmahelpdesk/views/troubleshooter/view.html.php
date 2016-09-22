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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/troubleshooter.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/troubleshooter.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/troubleshooter/tmpl/default.php";

// Set toolbar and page title
HelpdeskTroubleshooterAdminHelper::addToolbar($task);
HelpdeskTroubleshooterAdminHelper::setDocument();

$parent = JRequest::getVar('parent', 0, '', 'int');
$title = JRequest::getVar('title', '', '', 'string');
$id = JRequest::getVar('id', 0, '', 'int');
$description = JRequest::getVar('description', '', '', 'string', 2);

if ($task == "") {
	$task = $function;
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'troubleshooter', $task, $id);

switch ($task) {
	case "new":
		editTroubleshooter(0, $parent = '');
		break;

	case "edit":
		editTroubleshooter($id, 0);
		break;

	case "save":
		saveTroubleshooter($id, $parent, $title, $description);
		break;

	case "delete":
		removeTroubleshooter($id);
		viewTroubleshooter();
		break;

	case "cancel":
		viewTroubleshooter();
		break;

	default:
		viewTroubleshooter();
		break;
}

function viewTroubleshooter()
{
	ShowTroubleshooter($rows);
}


function removeTroubleshooter($id)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	if ($id) {
		$database->setQuery("DELETE FROM #__support_troubleshooter WHERE `id`='$id'");

		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=troubleshooter");
}


function saveTroubleshooter($id, $parent, $title, $description)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableTroubleshooter($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if ($id == 0) {
		$database->setQuery("INSERT INTO #__support_troubleshooter(parent, title, description, `show`) VALUES('$parent', " . $database->quote($title) . ", " . $database->quote($description) . ", '1')");
	} else {
		$database->setQuery("UPDATE #__support_troubleshooter SET title=" . $database->quote($title) . ", description=" . $database->quote($description) . " WHERE `id`='$id'");
	}

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=troubleshooter");
}


function editTroubleshooter($id, $parent)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableTroubleshooter($database);
	$row->load($id);

	troubleForm($row, $parent, $id);
}

?>