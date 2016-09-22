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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/export_options.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/export.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/export/tmpl/default.php";

// Set toolbar and page title
HelpdeskExportOptionsAdminHelper::addToolbar($task);
HelpdeskExportOptionsAdminHelper::setDocument();

// Get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'export', $task, $cid[0]);

switch ($task) {
	case "new":
		editExport(0);
		break;

	case "edit":
		editExport($cid[0]);
		break;

	case "save":
		saveExport();
		break;

	case "remove":
		removeExport($cid);
		break;

	default:
		showExport();
		break;
}

/**
 * Compiles a list of workgroups
 * @param string The component name
 */
function showExport()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_export_profile");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT id, name, description, isdefault"
			. "\nFROM #__support_export_profile"
			. "\nORDER BY name",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	export_html::show($rows, $pageNav);
}

/**
 * Compiles information to add or edit a section
 * @param database A database connector object
 * @param string The component name
 */
function editExport($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableExport($database);
	$row->load($uid);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['default'] = HelpdeskForm::SwitchCheckbox('radio', 'isdefault', $captions, $values, $row->isdefault, 'switch');
	$lists['billableonly'] = HelpdeskForm::SwitchCheckbox('radio', 'billableonly', $captions, $values, $row->billableonly, 'switch');
	$lists['auto_save'] = HelpdeskForm::SwitchCheckbox('radio', 'auto_save', $captions, $values, $row->auto_save, 'switch');
	$lists['update_exported'] = HelpdeskForm::SwitchCheckbox('radio', 'update_exported', $captions, $values, $row->update_exported, 'switch');

	$export_type[] = JHTML::_('select.option', 'A', JText::_('activities'));
	$export_type[] = JHTML::_('select.option', 'T', JText::_('tickets'));
	$export_type[] = JHTML::_('select.option', 'C', JText::_('clients'));
	$export_type[] = JHTML::_('select.option', 'U', JText::_('users'));
	$lists['export_type'] = JHTML::_('select.genericlist', $export_type, 'export_type', 'class="inputbox" size="1"', 'value', 'text', $row->export_type);

	// Build List of Available Statuses
	$database->setQuery("SELECT `id` FROM #__support_status WHERE status_group='C'");
	$default_status = $database->loadResult();
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY status_group";
	$database->setQuery($sql);
	$rows_statuses = $database->loadObjectList();
	$rows_statuses = array_merge(array(JHTML::_('select.option', 'O', JText::_('all_open'))), $rows_statuses);
	$rows_statuses = array_merge(array(JHTML::_('select.option', 'C', JText::_('all_closed'))), $rows_statuses);
	$rows_statuses = array_merge(array(JHTML::_('select.option', 'A', JText::_('all_status'))), $rows_statuses);
	$lists['statuses'] = JHTML::_('select.genericlist', $rows_statuses, 'filter_statusid', 'class="inputbox" size="1"', 'value', 'text', $row->filter_statusid);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'filter_wkid', 'class="inputbox" size="1"', 'value', 'text', $row->filter_wkid);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'filter_clientid', 'class="inputbox" size="1" onchange="changeDynaList(\'filter_userid\', ordersOS, document.adminForm.filter_clientid.options[document.adminForm.filter_clientid.selectedIndex].value, \'' . $row->filter_userid . '\', \'' . $row->filter_clientid . '\');"', 'value', 'text', $row->filter_clientid);

	$sub_os = array();

	for ($i = 0, $n = count($rows_wk); $i < $n; $i++) {
		$wkrow = &$rows_wk[$i];
		$sub_os[0][] = JHTML::_('select.option', $wkrow->value, JText::_('all_users'));
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client FROM #__users u, #__support_client_users c WHERE c.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . " ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	}

	export_html::edit($row, $lists, $sub_os);
}

/**
 * Saves the workgroup
 * @param database A database connector object
 * @param string The name of the component
 */
function saveExport()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableExport($database);
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
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	if ($row->isdefault) {
		$database->setQuery("UPDATE #__support_export_profile SET isdefault='0' WHERE id!='" . $row->id . "'");
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=export");
}

/**
 * Deletes one or more categories from the categories table
 * @param database A database connector object
 * @param string The name of the category section
 * @param array An array of unique category id numbers
 */
function removeExport($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('export_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_export_profile WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=export");
}

?>
