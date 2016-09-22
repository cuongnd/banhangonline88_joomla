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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/group.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download_group.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/group/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/group/tmpl/edit.php";

// Set toolbar and page title
HelpdeskGroupAdminHelper::addToolbar($task);
HelpdeskGroupAdminHelper::setDocument();

$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'group', $task, $cid[0]);

switch ($task) {
	case "new":
		editGroup(null);
		break;

	case "save":
		saveGroup();
		break;

	case "edit":
		editGroup($cid[0]);
		break;

	case "remove":
		removeGroup($cid);
		break;

	case "cancel":
	case "group":
		viewGroup();
		break;

}

function viewGroup()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_dl_group");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$query = "SELECT * FROM #__support_dl_group"
		. "\n ORDER BY gname ";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);

	if (!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}

	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editGroup($id_group)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$lists = array();

	$row = new MaQmaHelpdeskTableDownloadGroup($database);
	$row->load($id_group);

	// Build Users select list
	$sql = "SELECT id as value, clientname as text FROM #__support_client ORDER BY clientname";
	$database->setQuery($sql);
	if (!$database->query()) {
		echo $database->stderr();
		return false;
	}

	$lists['clients'] = JHTML::_('select.genericlist', $database->loadObjectList(), 'id_user', 'class="inputbox" size="10" multiple="multiple" onClick="PrepareUsers();"', 'value', 'text', 0);

	// Build Group Users
	$sql = "SELECT id_user FROM #__support_dl_users WHERE id_group='" . $row->id . "'";
	$database->setQuery($sql);
	$group_users = $database->loadObjectList();
	$sel_user = '';
	for ($i = 0; $i < count($group_users); $i++) {
		$group_user = $group_users[$i];
		$sel_user .= $group_user->id_user . ',';
	}
	$sel_user = JString::substr($sel_user, 0, strlen($sel_user) - 1);
	$lists['group_users'] = $sel_user;

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['unregister'] = HelpdeskForm::SwitchCheckbox('radio', 'unregister', $captions, $values, $row->unregister, 'switch');
	$lists['isdefault'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault', $captions, $values, $row->isdefault, 'switch');

	MaQmaHtmlEdit::display($row, $lists);
}

function saveGroup()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$id = JRequest::getVar('id', 0, '', 'int');
	$gname = JRequest::getVar('gname', '', '', 'string');
	$description = JRequest::getVar('description', '', '', 'string');
	$users = JRequest::getVar('users', '', '', 'string');
	$unregister = JRequest::getVar('unregister', 0, '', 'int');
	$isdefault = JRequest::getVar('isdefault', 0, '', 'int');

	if ($id > 0) {
		$database->setQuery("UPDATE #__support_dl_group SET gname=" . $database->quote($gname) . ", description=" . $database->quote($description) . ", unregister='" . $unregister . "', isdefault='" . $isdefault . "' WHERE id='" . $id . "'");
		$database->query();

		$database->setQuery("DELETE FROM #__support_dl_users WHERE id_group='" . $id . "'");
		$database->query();
	} else {
		$database->setQuery("INSERT INTO #__support_dl_group(gname, description, unregister, isdefault) VALUES(" . $database->quote($gname) . ", " . $database->quote($description) . ", '" . $unregister . "', '" . $isdefault . "')");
		$database->query();
		$id = mysql_insert_id();
	}

	if ($users != '') {
		$users = explode(",", $users);
		for ($i = 0; $i < count($users); $i++) {
			$database->setQuery("INSERT INTO #__support_dl_users(id_group, id_user) VALUES('" . $id . "', '" . $users[$i] . "')");
			$database->query();
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=group");
}

function removeGroup($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_dl_group WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=group");
}

?>