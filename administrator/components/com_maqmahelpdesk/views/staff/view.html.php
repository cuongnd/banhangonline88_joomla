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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/staff.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/staff.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/staff/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/staff/tmpl/edit.php";

// Set toolbar and page title
HelpdeskStaffAdminHelper::addToolbar($task);
HelpdeskStaffAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

$id = intval(JRequest::getVar('id', 0, '', 'int'));
$uid = intval(JRequest::getVar('uid', 0, '', 'int'));
$permission = intval(JRequest::getVar('permission', 0, '', 'int'));
$action = intval(JRequest::getVar('action', 0, '', 'int'));

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'staff', $task, $cid[0]);

switch ($task) {
	case "categories":
		getCategories();
		break;

	case "new":
		editPermission(0);
		break;

	case "edit":
		editPermission($id);
		break;

	case "save":
		savePermission();
		break;

	case "remove":
		removePermission($id);
		break;

	case "permission":
		changePermission($id, $permission);
		break;

	default:
		showStaff();
		break;
}

function getCategories($id_workgroup = 0, $id_user = 0, $echo = true)
{
	$database = JFactory::getDBO();
	$id_workgroup = JRequest::getInt('id_workgroup', $id_workgroup);
	$id_user = JRequest::getInt('id_user', $id_user);

	// Build Workgroups select list
	$sql = "SELECT c.`id`, c.`name`, p.`id` AS cid, c.`level`
			FROM `#__support_category` AS c
				 LEFT JOIN `#__support_permission_category` AS p ON p.`id_category`=c.`id` AND p.`id_workgroup`=" . (int) $id_workgroup . " AND p.`id_user`=" . (int) $id_user . "
			WHERE c.`id_workgroup`=" . (int) $id_workgroup . " AND c.`show`=1 AND c.`tickets`=1
			ORDER BY c.`level`, c.`name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$list = '<select id="id_category" name="id_category[]" size="7" multiple="multiple">';
	foreach ($rows as $row) {
		$list .= '<option value="' . $row->id . '" ' . ($row->cid ? 'selected' : '') . '>';
		for ($i = 2; $i <= $row->level; $i++) {
			$list .= ' ';
		}
		$list .= $row->name . '</option>';
	}
	$list .= '<select>';

	if ($echo) {
		echo $list;
	} else {
		return $list;
	}
}

function changePermission($id, $permission)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	$database->setQuery("UPDATE #__support_permission SET manager='" . $permission . "' WHERE id='" . $id . "'");
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=staff");
}

function showStaff()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getInt('limitstart', 0);
	$id_workgroup = JRequest::getInt('filter_workgroup', 0);

	// get the total number of records
	$database->setQuery("SELECT count(distinct(id_user)) FROM #__support_permission");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT p.id, p.id_user, u.name, u.username, w.wkdesc, p.bugtracker, p.can_delete, p.manager
			FROM #__support_permission AS p
				 INNER JOIN #__users AS u ON u.id=p.id_user
				 INNER JOIN #__support_workgroup AS w ON p.id_workgroup=w.id
			" . ($id_workgroup ? "WHERE p.id_workgroup=" . $id_workgroup : '') . "
			ORDER BY u.name, w.wkdesc";
	$database->setQuery($sql);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'filter_workgroup', 'class="inputbox" size="1" onchange="document.filterForm.submit();"', 'value', 'text', $id_workgroup);

	MaQmaHtmlDefault::display($rows, $lists, $pageNav);
}

function editPermission($uid = 0)
{
	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$row = new MaQmaHelpdeskTableStaff($database);
	$row->load($uid);

	// get the name of the user
	$sql = "SELECT `name`
			FROM `#__users`
			WHERE `id`='" . $row->id_user . "'";
	$database->setQuery($sql);
	$row->name = $database->loadResult();

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('select3'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onchange="GetCategories();"', 'value', 'text', $row->id_workgroup);

	// Build Category select list
	$lists['category'] = getCategories($row->id_workgroup, $row->id_user, false);

	// Usertype
	$sup_usertype[] = JHTML::_('select.option', '7', JText::_('manager'));
	$sup_usertype[] = JHTML::_('select.option', '6', JText::_('team_leader'));
	$sup_usertype[] = JHTML::_('select.option', '5', JText::_('support_user'));
	$lists['sup_usertype'] = JHTML::_('select.genericlist', $sup_usertype, 'manager', 'class="inputbox" size="1"', 'value', 'text', $row->manager);

	// Support level
	$level[] = JHTML::_('select.option', '0', JText::_('NOT_APPLICABLE'));
	for ($i = 1; $i < 11; $i++)
	{
		$level[] = JHTML::_('select.option', $i, sprintf(JText::_('SUPPORT_LEVEL_LABEL'), $i));
	}
	$lists['level'] = JHTML::_('select.genericlist', $level, 'level', 'class="inputbox" size="1"', 'value', 'text', $row->level);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$assign_aditional = ($row->assign_report_users == '') ? 0 : 1;
	$lists['assign_report'] = HelpdeskForm::SwitchCheckbox('radio', 'assign_report', $captions, $values, $assign_aditional, 'switch', 'showhidediv();');
	$lists['can_delete'] = HelpdeskForm::SwitchCheckbox('radio', 'can_delete', $captions, $values, $row->can_delete, 'switch');
	$lists['bugtracker'] = HelpdeskForm::SwitchCheckbox('radio', 'bugtracker', $captions, $values, $row->bugtracker, 'switch');

	// Build Users checkboxs list
	$sql = "SELECT DISTINCT u.`id` AS value, u.`name` AS text
			FROM `#__users` AS u INNER JOIN `#__support_permission` AS p ON p.`id_user`=u.`id`
			WHERE `id_user`!=" . (int)$row->id_user . "
			ORDER BY u.`name`";
	$database->setQuery($sql);
	$rows_user = $database->loadObjectList();
	ob_start(); ?>
	<div id="div_support_users"><?php
	if (count($rows_user) > 0)
	{
		$users_report_aditional = "";
		if ($row->assign_report_users != '')
		{
			$users_report_aditional = explode('#', $row->assign_report_users);
		}
		foreach ($rows_user as $row_user)
		{
			$checked = is_array($users_report_aditional) ? (in_array($row_user->value, $users_report_aditional) ? " checked " : "") : ""; ?>
			<p style="margin:0;line-height:10px;padding:0;"><label><input
				name="assign_report_users[<?php echo $row_user->value; ?>]" value="1"
				type="checkbox" <?php echo $checked; ?> /> <?php echo $row_user->text; ?></label></p><?php
		}
	} ?>
	</div><?php
	$aditional_assign_user = ob_get_contents();
	ob_end_clean();
	$lists['assign_report_users'] = $aditional_assign_user;

	MaQmaHtmlEdit::display($row, $lists);
}

function savePermission()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$id = intval(JRequest::getVar('id', '', '', 'int'));
	$id_workgroup = intval(JRequest::getVar('id_workgroup', '', '', 'int'));
	$id_user = intval(JRequest::getVar('id_user', '', '', 'int'));
	$manager = intval(JRequest::getVar('manager', '', '', 'int'));
	$can_delete = intval(JRequest::getVar('can_delete', '', '', 'int'));
	$level = intval(JRequest::getVar('level', '', '', 'int'));
	$assign_report = intval(JRequest::getVar('assign_report', '0', '', 'int'));
	$report_users_colect = JRequest::getVar('assign_report_users', '', '', 'array');
	$categories = JRequest::getVar('id_category', '', '', 'array');

	if ($assign_report == 1)
	{
		$assign_report_users = "";
		foreach ($report_users_colect as $chave => $valor)
		{
			$assign_report_users .= $chave . "#";
			echo ($chave . ":" . $valor . "<br />");
		}
		$assign_report_users = substr_replace($assign_report_users, "", -1);
	}
	else
	{
		$assign_report_users = '';
	}

	// Check for ticket manager views
	$sql = "SELECT COUNT(*)
			FROM `#__support_permission`
			WHERE `id_user`=" . $id_user;
	$database->setQuery($sql);
	$permissions = (int)$database->loadResult();

	if ($id)
	{
		$sql = "UPDATE `#__support_permission`
				SET `id_user` = " . $id_user . ", `id_workgroup` = " . $id_workgroup . ", `manager` = " . $manager . ", `can_delete` = " . $database->quote($can_delete) . ", `assign_report_users` = " . $database->quote($assign_report_users) . ", `level` = " . $database->quote($level) . "
				WHERE `id` = " . $id . " ";
		$database->setQuery($sql);
		$database->query();
	}
	else
	{
		$sql = "INSERT INTO `#__support_permission` (`id_user`,`id_workgroup`,`manager`,`assign_report_users`,`can_delete`, `level`)
				VALUES (" . $id_user . "," . $id_workgroup . "," . $manager . "," . $database->quote($assign_report_users) . "," . $can_delete . "," . $level . ") ";
		$database->setQuery($sql);
		$database->query();
	}

	// Set categories
	$sql = "DELETE FROM `#__support_permission_category`
			WHERE `id_workgroup`=" . $id_workgroup . " AND `id_user`=" . $id_user;
	$database->setQuery($sql);
	$database->query();
	for ($i = 0; $i < count($categories); $i++)
	{
		if (trim($categories[$i]) != '')
		{
			$sql = "INSERT INTO `#__support_permission_category`(`id_workgroup`, `id_category`, `id_user`)
					VALUES(" . $id_workgroup . ", " . $categories[$i] . "," . $id_user . ")";
			$database->setQuery($sql);
			$database->query();
		}
	}

	// Create default views for ticket manager
	//echo "<p>-".$id;
	//echo "<p>-".$supportConfig->common_ticket_views;
	//echo "<p>-".$permissions;
	if (!$id && !$supportConfig->common_ticket_views && !$permissions)
	{
		$id = $database->insertid();
		$operator = 'AND' . ($manager > 5 ? '|OR' : '');
		$field = 't.assign_to' . ($manager > 5 ? '|t.assign_to' : '');
		$arithmetic = '=' . ($manager > 5 ? '|=' : '');
		$value = $id_user . '' . ($manager > 5 ? '|0' : '');
		$sql = "INSERT INTO `#__support_views`(`id_user`, `name`, `viewtype`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `default`)
				VALUES($id_user, '" . JText::_('lbl_assigned_to_me') . ($manager > 5 ? ' ' . JText::_('lbl_not_assigned') : '') . "', 'table', 't.duedate', '$operator', '$field', '$arithmetic', '$value', 1)";
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=staff");
}

function removePermission($id)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$id)
	{
		echo "<script type='text/javascript'> alert('" . JText::_('staff_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$database->setQuery("SELECT id_user FROM #__support_permission WHERE id=" . $id);
	$users = $database->loadObjectList();

	$database->setQuery("DELETE FROM #__support_permission WHERE id=" . $id);
	$database->query();

	for ($i = 0; $i < count($users); $i++)
	{
		$sql = "SELECT COUNT(*)
				FROM `#__support_permission`
				WHERE `id_user`=" . $users[$i]->id_user;
		$database->setQuery($sql);
		$permissions = (int) $database->loadResult();

		if (!$permissions)
		{
			$sql = "DELETE FROM #__support_views
					WHERE id_user=" . $users[$i]->id_user;
			$database->setQuery($sql);
			$database->query();
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=staff");
}
