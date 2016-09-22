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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/department_fields.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/department_fields.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/wkfields/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/wkfields/tmpl/edit.php";

// Set toolbar and page title
HelpdeskDepartmentsFieldsAdminHelper::addToolbar($task);
HelpdeskDepartmentsFieldsAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'wkfields', $task, $cid[0]);

switch ($task) {
	case "categories":
		getCategories();
		break;

	case "sections":
		getSections();
		break;

	case "new":
		editCustomField(0);
		break;

	case "edit":
		editCustomField($cid[0]);
		break;

	case "save":
		saveCustomField();
		break;

	case "remove":
		removeCustomField($cid);
		break;

	case 'saveorder':
		saveOrder();
		break;

	default:
		showCustomField();
		break;
}

function getCategories($id_workgroup = 0, $id_category = null, $echo = true)
{
	$database = JFactory::getDBO();

	$id_workgroup = JRequest::getInt('id_workgroup', $id_workgroup);
	$id_category = JRequest::getVar('id_category', $id_category, '', 'string');

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `name` AS text
			FROM `#__support_category`
			WHERE `id_workgroup`=" . (int)$id_workgroup . " AND `show`=1 AND `tickets`=1
			ORDER BY `name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$list = JHTML::_('select.genericlist', $rows, 'id_category[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', 0);

	if ($echo) {
		echo $list;
	} else {
		return $list . '<script>$jMaQma("#id_category").val([' . $id_category . ']);</script>';
	}
}

function getSections($id_workgroup = 0, $value = '', $echo = true)
{
	$database = JFactory::getDBO();
	$id_workgroup = JRequest::getInt('id_workgroup', $id_workgroup);

	$sql = "SELECT DISTINCT(`section`) AS value, `section` AS text
			FROM #__support_wk_fields 
			WHERE `id_workgroup`=" . (int)$id_workgroup . " AND `section`!=''
			ORDER BY `section`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$list = '<select id="section" name="section" class="medium" size="1">';
	$list .= '<option value="">' . JText::_('selectlist') . '</option>';
	foreach ($rows as $row) {
		$list .= '<option value="' . $row->value . '" ' . ($row->value == $value ? 'selected' : '') . '>' . $row->text . '</option>';
	}
	$list .= '<select>';

	if ($echo) {
		echo $list;
	} else {
		return $list;
	}
}

function showCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_wk_fields");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT s.id, f.caption, f.ftype, w.wkdesc, s.required, s.support_only, s.new_only, s.section, s.id_category
			FROM #__support_wk_fields AS s
				 INNER JOIN #__support_custom_fields AS f ON s.id_field=f.id 
				 INNER JOIN #__support_workgroup AS w ON s.id_workgroup=w.id
			ORDER BY w.wkdesc, s.section, s.ordering";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editCustomField($uid = 0)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableDepartmentFields($database);
	$row->load($uid);

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text
			FROM #__support_workgroup
			ORDER BY `wkdesc`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onchange="GetSections();GetCategories();"', 'value', 'text', $row->id_workgroup);

	// Build Section select list
	$lists['section'] = getSections($row->id_workgroup, $row->section, false);

	// Build Custom Fields select list
	$sql = "SELECT `id` AS value, `caption` AS text
			FROM #__support_custom_fields WHERE cftype='W' 
			ORDER BY `caption`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['fields'] = JHTML::_('select.genericlist', $rows_wk, 'id_field', 'class="inputbox" size="1"', 'value', 'text', $row->id_field);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['required'] = HelpdeskForm::SwitchCheckbox('radio', 'required', $captions, $values, $row->required, 'switch');
	$lists['new_only'] = HelpdeskForm::SwitchCheckbox('radio', 'new_only', $captions, $values, $row->new_only, 'switch');

	$support_only = array(JHTML::_('select.option', '0', JText::_('normal')),
		JHTML::_('select.option', '1', JText::_('readonly')),
		JHTML::_('select.option', '2', JText::_('hidden')));
	$lists['support_only'] = JHTML::_('select.genericlist', $support_only, 'support_only', 'id="support_only" class="inputbox" size="1" ', 'value', 'text', $row->support_only);

	// build the html select list for ordering
	$order = JHTML::_('list.genericordering', "SELECT s.ordering AS value, f.caption AS text"
		. "\nFROM #__support_wk_fields s, #__support_custom_fields f"
		. "\nWHERE s.id_workgroup='$row->id_workgroup' AND s.id_field=f.id ORDER BY s.ordering"
	);
	$lists['ordering'] = JHTML::_('select.genericlist', $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));

	// Build Category select list
	$lists['category'] = getCategories($row->id_workgroup, $row->id_category, false);

	MaQmaHtmlEdit::display($row, $lists);
}

function saveCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$section_new = JRequest::getVar('section_new', '', '', 'string');
	$id_category = JRequest::getVar('id_category', '', '', 'array');
	$row = new MaQmaHelpdeskTableDepartmentFields($database);
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

	// Check if section is to create a new one or a selected
	if ($section_new != '') {
		$sql = "UPDATE `#__support_wk_fields`
				SET `section`='$section_new'
				WHERE `id`=" . $row->id;
		$database->setQuery($sql);
		$database->query();
	}

	// Take care of categories
	$categories = implode($id_category, ',');
	$sql = "UPDATE `#__support_wk_fields`
			SET `id_category`='$categories'
			WHERE `id`=" . $row->id;
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=wkfields");
}

function removeCustomField($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('wkfield_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_wk_fields WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=wkfields");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++) {
		$sql = "UPDATE `#__support_wk_fields`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=wkfields", JText::_('new_ordering_save'));
}