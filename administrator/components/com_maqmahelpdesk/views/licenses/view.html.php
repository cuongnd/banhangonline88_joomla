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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/licenses.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download_license.php';

// Output
require_once "components/com_maqmahelpdesk/views/licenses/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/licenses/tmpl/edit.php";

// Set toolbar and page title
HelpdeskLicensesAdminHelper::addToolbar($task);
HelpdeskLicensesAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'licenses', $task, $cid[0]);

switch ($task) {
	case "new":
		editLicenses(0);
		break;

	case "edit":
		editLicenses($cid[0]);
		break;

	case "save":
		saveLicenses();
		break;

	case "remove":
		removeLicenses($cid);
		break;

	default:
		showLicenses();
		break;
}

function showLicenses()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_dl_license");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT *"
			. "\nFROM #__support_dl_license"
			. "\nORDER BY `title`",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editLicenses($uid = 0)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableLicenses($database);
	$row->load($uid);

	MaQmaHtmlEdit::display($row);
}

function saveLicenses()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableLicenses($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=licenses");
}

function removeLicenses($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('license_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_dl_license WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=licenses");
}
