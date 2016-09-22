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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/contracts.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/contract_fields.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/contract_template.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/contracts/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/contracts/tmpl/edit.php";
require_once "components/com_maqmahelpdesk/views/contracts/tmpl/cfdefault.php";
require_once "components/com_maqmahelpdesk/views/contracts/tmpl/cfedit.php";

// Set toolbar and page title
HelpdeskContractsAdminHelper::addToolbar($task, $function);
HelpdeskContractsAdminHelper::setDocument($task, $function);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'contracts', $task, $cid[0]);

switch ($task) {
	case "new":
		editContract(0);
		break;
	case "edit":
		editContract($cid[0]);
		break;
	case "save":
		saveContract();
		break;
	case "remove":
		removeContract($cid);
		break;
	case "fields":
		showCustomField();
		break;
	case "fields_new":
		editCustomField(0);
		break;
	case "fields_edit":
		editCustomField($cid[0]);
		break;
	case "fields_remove":
		removeCustomField($cid);
		break;
	case 'saveorder':
		saveOrder();
		break;
	case "fields_save":
		saveCustomField();
		break;
	default:
		showContract();
		break;
}

function showCustomField()
{
	$database = JFactory::getDBO();

	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_contract_fields");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT c.id, f.caption, f.ftype, c.required "
			. "\nFROM #__support_contract_fields c, #__support_custom_fields f"
			. "\nWHERE c.id_field=f.id",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlCFDefault::display($rows, $pageNav);
}

function editCustomField($uid = 0)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableContractFields($database);
	$row->load($uid);

	// Build Custom Fields select list
	$sql = "SELECT `id` AS value, `caption` AS text FROM #__support_custom_fields WHERE cftype='C' ORDER BY `caption`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['fields'] = JHTML::_('select.genericlist', $rows_wk, 'id_field', 'class="inputbox" size="1"', 'value', 'text', $row->id_field);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['required'] = HelpdeskForm::SwitchCheckbox('radio', 'required', $captions, $values, $row->required, 'switch');

	// Build Custom Fields select list
	$sql = "SELECT MAX(`ordering`)
			FROM #__support_contract_fields";
	$database->setQuery($sql);
	$lists['ordering'] = (int) ($database->loadResult() + 1);

	MaQmaHtmlCFEdit::display($row, $lists);
}

function saveCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableContractFields($database);
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

	/*
		TODO
		Must check field type and if it's creation and
		it's type user or contract insert automatically
		in the SET CUSTOM FIELDS and the user just can
		manage them after that
	*/

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=contracts_fields");
}

function removeCustomField($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		// TODO: create language call refering contact (not wk)
		echo "<script type='text/javascript'> alert('" . JText::_('wkfield_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_contract_fields WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=contracts_fields");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');

	for ($i = 1; $i < (count($orders) - 1); $i++) {
		$sql = "UPDATE `#__support_contract_fields`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=contracts_fields", JText::_('new_ordering_save'));
}

function showContract()
{
	$database = JFactory::getDBO();

	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_contract_template");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT t.id, t.name, t.description, t.unit, t.val, CONCAT(p.description, ' (', p.timevalue, ' ', p.timeunit, ')') AS priority"
			. "\nFROM #__support_contract_template t, #__support_priority p"
			. "\nWHERE t.id_priority=p.id"
			. "\nORDER BY t.name",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editContract($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableContractTemplate($database);
	$row->load($uid);

	// Build Unit select list
	$units[] = JHTML::_('select.option', '', JText::_('selectlist'));
	$units[] = JHTML::_('select.option', 'Y', JText::_('years'));
	$units[] = JHTML::_('select.option', 'M', JText::_('months'));
	$units[] = JHTML::_('select.option', 'D', JText::_('days'));
	$units[] = JHTML::_('select.option', 'H', JText::_('hours'));
	$units[] = JHTML::_('select.option', 'T', JText::_('tickets'));
	$lists['unit'] = JHTML::_('select.genericlist', $units, 'unit', 'class="inputbox" size="1"', 'value', 'text', $row->unit);

	// Build Priority select list
	$sql = "SELECT `id` AS value, CONCAT(description, ' (', timevalue, ' ', timeunit, ')') AS text FROM #__support_priority";
	$database->setQuery($sql);
	$rows_cat = $database->loadObjectList();
	$rows_cat = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_cat);
	$lists['priority'] = JHTML::_('select.genericlist', $rows_cat, 'id_priority', 'class="inputbox" size="1"', 'value', 'text', $row->id_priority);

	MaQmaHtmlEdit::display($row, $lists);
}

function saveContract()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableContractTemplate($database);
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

	$row->checkin();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=contracts");

}

function removeContract($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script> alert('" . JText::_('contracttmpl_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_contract_template WHERE id IN (" . $cids . ")");
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=contracts");
}