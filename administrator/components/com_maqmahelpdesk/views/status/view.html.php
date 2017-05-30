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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/status.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/status.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/status/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/status/tmpl/edit.php";

// Set toolbar and page title
HelpdeskStatusAdminHelper::addToolbar($task);
HelpdeskStatusAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'status', $task, $cid[0]);

switch ($task) {
	case "new":
		editStatus(0);
		break;

	case "edit":
		editStatus($cid[0]);
		break;

	case "save":
		saveStatus();
		break;

	case "remove":
		removeStatus($cid);
		break;

	case "publish":
		publishStatus($cid, 1);
		break;

	case "unpublish":
		publishStatus($cid, 0);
		break;

	case 'saveorder':
		saveOrder();
		break;

	default:
		showStatus();
		break;
}

function showStatus()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_status ORDER BY description");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT s.id, s.description, s.show, s.status_group, s.isdefault_manager, s.isdefault, s.user_access, s.color FROM #__support_status s ORDER BY s.ordering, s.description",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editStatus($uid = 0)
{
	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('color.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$row = new MaQmaHelpdeskTableStatus($database);
	$row->load($uid);

	$status_groups[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$status_groups[] = JHTML::_('select.option', 'O', JText::_('open'));
	$status_groups[] = JHTML::_('select.option', 'C', JText::_('closed'));
	$lists['group'] = JHTML::_('select.genericlist', $status_groups, 'status_group', 'class="inputbox" size="1"', 'value', 'text', $row->status_group);

	$status_side[] = JHTML::_('select.option', '0', JText::_('status_side_not_used'));
	$status_side[] = JHTML::_('select.option', '1', JText::_('support_user'));
	$status_side[] = JHTML::_('select.option', '2', JText::_('user'));
	$lists['status_side'] = JHTML::_('select.genericlist', $status_side, 'ticket_side', 'class="inputbox" size="1"', 'value', 'text', $row->ticket_side);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['default'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault', $captions, $values, $row->isdefault, 'switch');
	$lists['default_manager'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault_manager', $captions, $values, $row->isdefault_manager, 'switch');
	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $row->show, 'switch');
	$lists['user_access'] = HelpdeskForm::SwitchCheckbox('radio', 'user_access', $captions, $values, $row->user_access, 'switch');
	$lists['allow_old_status_back'] = HelpdeskForm::SwitchCheckbox('radio', 'allow_old_status_back', $captions, $values, $row->allow_old_status_back, 'switch');
	$lists['auto_status_agents'] = HelpdeskForm::SwitchCheckbox('radio', 'auto_status_agents', $captions, $values, $row->auto_status_agents, 'switch');
	$lists['auto_status_users'] = HelpdeskForm::SwitchCheckbox('radio', 'auto_status_users', $captions, $values, $row->auto_status_users, 'switch');

	$database->setQuery("SELECT id, description FROM #__support_status ");
	$status_list = $database->loadObjectList();

	$status_checked = explode("#", $row->status_workflow);
	$checkbox_status_workflow = "";
	if (count($status_list) > 0) {
		$checkbox_status_workflow = "";
		for ($i = 0, $n = count($status_list); $i < $n; $i++) {
			$status = &$status_list[$i];
			if ($row->id == $status->id) {
				$value = 0;
				$readonly = "disabled";
				$checked = "";
			} else {
				$value = 1;
				$readonly = "";
				$checked = in_array($status->id, $status_checked) ? "checked" : "";
			}
			$checkbox_status_workflow .= "<input type='checkbox' class='checkbox inline' name='status_id[" . $status->id . "]' value='" . $value . "' " . $checked . " " . $readonly . " /> " . $status->description . " <br />";
		}
		$lists['status_workflow'] = '<div class="controlset-pad">' . $checkbox_status_workflow . '</div>';
	} else {
		$lists['status_workflow'] = "";
	}

	MaQmaHtmlEdit::display($row, $lists, count($status_list));
}

function saveStatus()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableStatus($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST))
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check())
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store())
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$id = intval(JRequest::getVar('id', '', '', 'int'));
	$description = JRequest::getVar('description', '', '', 'string');
	$status_group = JRequest::getVar('status_group', '', '', 'string');
	$isdefault = intval(JRequest::getVar('isdefault', '', '', 'int'));
	$show = intval(JRequest::getVar('show', '', '', 'int'));
	$user_access = intval(JRequest::getVar('user_access', '', '', 'int'));
	$allow_old_status_back = intval(JRequest::getVar('allow_old_status_back', '', '', 'int'));
	$status_id = JRequest::getVar('status_id', '', '', 'array');

	$status_workflow = "";
	foreach ($status_id as $chave => $valor) {
		$status_workflow .= $chave . "#";
	}
	$status_workflow = JString::substr($status_workflow, 0, -1);

	$query = "UPDATE #__support_status SET `description` = " . $database->quote($description) . ", `show` = " . $show . ", `status_group` = " . $database->quote($status_group) . ", `isdefault` = " . $isdefault . ", `user_access` = " . $user_access . ", `allow_old_status_back` = " . $allow_old_status_back . ", `status_workflow` = " . $database->quote($status_workflow) . " WHERE `id` = " . $id . " ";
	$database->setQuery($query);
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	//$row->checkin();
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=status");
}

function removeStatus($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('status_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_status WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=status");
}

function publishStatus($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('status_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_status SET `show`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=status");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++) {
		$sql = "UPDATE `#__support_status`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=status", JText::_('new_ordering_save'));
}
