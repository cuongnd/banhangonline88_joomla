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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/types.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/types.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/types/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/types/tmpl/edit.php";

// Set toolbar and page title
HelpdeskTypesAdminHelper::addToolbar($task);
HelpdeskTypesAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'types', $task, $cid[0]);

switch ($task) {
	case "new":
		editTypes(0);
		break;

	case "edit":
		editTypes($cid[0]);
		break;

	case "save":
		saveTypes();
		break;

	case "remove":
		removeTypes($cid);
		break;

	case "publish":
		publishTypes($cid, 1);
		break;

	case "unpublish":
		publishTypes($cid, 0);
		break;

	default:
		showTypes();
		break;
}

function showTypes()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_activity_type");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT id, isdefault, description, published"
			. "\nFROM #__support_activity_type"
			. "\nORDER BY description",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editTypes($uid = 0)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableTypes($database);
	$row->load($uid);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');
	$lists['isdefault'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault', $captions, $values, $row->isdefault, 'switch');

	MaQmaHtmlEdit::display($row, $lists);
}

function saveTypes()
{
	global $mainframe;

	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableTypes($database);
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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=types");
}

function removeTypes($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('actype_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_activity_type WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=types");
}

function publishTypes($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('actype_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_activity_type SET published='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=types");
}
