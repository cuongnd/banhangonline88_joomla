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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/download_access.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download_access.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/customer/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/customer/tmpl/edit.php";

// Set toolbar and page title
HelpdeskDownloadAccessAdminHelper::addToolbar($task);
HelpdeskDownloadAccessAdminHelper::setDocument();

//$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
$cid = JRequest::getVar('cid', array(0), 'REQUEST', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'customer', $task, $cid[0]);

switch ($task) {
	case "new":
		editCustomer(null);
		break;

	case "save":
		saveCustomer();
		break;

	case "edit":
		editCustomer($cid[0]);
		break;

	case "remove":
		removeCustomer($cid);
		break;

	case "cancel":
	case "customer":
		viewCustomers();
		break;

}

function viewCustomers()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	$filter = JRequest::getVar('filter', $session->get('filter_customer', '', 'maqmahelpdesk'), 'POST', 'string');
	$filter = ($filter);
	$session->set('filter_customer', $filter, 'maqmahelpdesk');

	$sql = "SELECT count(*)
			FROM #__support_dl_access a 
				 INNER JOIN #__support_dl d ON a.id_download=d.id
				 INNER JOIN #__support_client c ON a.id_user=c.id " .
		($filter != '' ? "WHERE c.clientname LIKE '%$filter%' OR d.pname LIKE '%$filter%'" : "");
	$database->setQuery($sql);
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT a.id, c.clientname, d.pname AS product, a.isactive, a.serialno, a.servicefrom, a.serviceuntil , ca.cname AS category
			FROM #__support_dl_access a 
				 INNER JOIN #__support_dl d ON a.id_download=d.id
				 INNER JOIN #__support_dl_category ca ON d.id_category=ca.id
				 INNER JOIN #__support_client c ON a.id_user=c.id " .
		($filter != '' ? "WHERE c.clientname LIKE '%$filter%' OR d.pname LIKE '%$filter%'" : "") . "
			ORDER BY c.clientname, d.pname ";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);

	if (!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}

	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav, $filter);
}

function editCustomer($id_group)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$lists = array();

	$row = new MaQmaHelpdeskTableDownloadAccess($database);
	$row->load($id_group);
	HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Get client name
	$sql = "SELECT clientname FROM #__support_client WHERE id=" . (int)$row->id_user;
	$database->setQuery($sql);
	$row->clientname = $database->loadResult();

	// Build Products select list
	$sql = "SELECT d.`id` as value, CONCAT(c.`cname`, ' - ', d.`pname`) as text
			FROM `#__support_dl` AS d 
				 INNER JOIN `#__support_dl_category` AS c ON c.`id`=d.`id_category`
			ORDER BY c.`cname`, d.`pname`";
	$database->setQuery($sql);
	if (!$database->query()) {
		echo $database->stderr();
		return false;
	}
	$rows_prod = $database->loadObjectList();
	$rows_prod = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_prod);
	$lists['products'] = JHTML::_('select.genericlist', $rows_prod, 'id_download', 'class="span10" size="1"', 'value', 'text', $row->id_download);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['isactive'] = HelpdeskForm::SwitchCheckbox('radio', 'isactive', $captions, $values, $row->isactive, 'switch');

	// Get custom fields
	$sql = "SELECT cf.id, cf.id AS id_field, '0' AS ordering, '0' AS required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength
			FROM #__support_custom_fields cf
			WHERE cf.cftype='D'
			ORDER BY cf.caption";
	$database->setQuery($sql);
	$lists['cfields'] = $database->loadObjectList();

	MaQmaHtmlEdit::display($row, $lists);
}

function saveCustomer()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableDownloadAccess($database);
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

	// Delete Custom fields
	$sql = "DELETE FROM #__support_download_field_value
			WHERE id_download=" . $row->id;
	$database->setQuery($sql);
	$database->query();

	// Get custom fields
	$sql = "SELECT c.id
			FROM #__support_custom_fields AS c
			WHERE c.`cftype`='D'
			ORDER BY c.caption";
	$database->setQuery($sql);
	$customfields = $database->loadObjectList();

	// Insert values
	for ($x = 0; $x < count($customfields); $x++)
	{
		$cField = $customfields[$x];
		$custom_val = stripslashes(JRequest::getVar('custom' . $cField->id, '', '', 'string'));
		$sql = "INSERT INTO #__support_download_field_value(id_field, id_download, value)
				VALUES(" . (int) $cField->id . ", '" . (int) $row->id . "', " . $database->quote($custom_val) . ")";
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=customer");
}

function removeCustomer($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_dl_access WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=customer");
}
