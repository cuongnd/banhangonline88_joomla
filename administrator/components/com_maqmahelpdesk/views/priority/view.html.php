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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/priority.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/priority.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/priority/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/priority/tmpl/edit.php";

// Set toolbar and page title
HelpdeskPriorityAdminHelper::addToolbar($task);
HelpdeskPriorityAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'priority', $task, $cid[0]);

switch ($task) {
	case "new":
		editPriority(0);
		break;
	case "edit":
		editPriority($cid[0]);
		break;
	case "save":
		savePriority();
		break;
	case "remove":
		removePriority($cid);
		break;
	case "publish":
		publishPriority($cid, 1);
		break;
	case "unpublish":
		publishPriority($cid, 0);
		break;
	default:
		showPriority();
		break;
}

function showPriority()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_priority ORDER BY description");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT p.id, p.description, p.show, p.timeunit, p.timevalue, p.isdefault"
			. "\nFROM #__support_priority p"
			. "\nORDER BY p.description",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editPriority($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTablePriority($database);
	$row->load($uid);

	$timelist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$timelist[] = JHTML::_('select.option', 'H', JText::_('hours'));
	$timelist[] = JHTML::_('select.option', 'D', JText::_('days'));
	$lists['timeunit'] = JHTML::_('select.genericlist', $timelist, 'timeunit', 'class="inputbox" size="1"', 'value', 'text', $row->timeunit);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['default'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault', $captions, $values, $row->isdefault, 'switch');
	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $row->show, 'switch');

	MaQmaHtmlEdit::display($row, $lists);
}

function savePriority()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTablePriority($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if ($_POST['isdefault']) {
		$database->setQuery("UPDATE #__support_priority SET isdefault='0'");
		$database->query();
	}

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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=priority");
}

function removePriority($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('priority_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_priority WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=priority");
}

function publishPriority($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('priority_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_priority SET `show`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=priority");
}
