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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/forms.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/forms.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/forms/tmpl/data.php";
require_once "components/com_maqmahelpdesk/views/forms/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/forms/tmpl/edit.php";

// Set toolbar and page title
HelpdeskFormsAdminHelper::addToolbar($task);
HelpdeskFormsAdminHelper::setDocument();

// get parameters from the URL or submitted form
$id = JRequest::getVar('id', 0, '', 'int');
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'forms', $task, $cid[0]);

switch ($task) {
	case "new":
		editForms(0);
		break;

	case "edit":
		editForms($cid[0]);
		break;

	case "save":
		saveForms(0);
		break;

	case "apply":
		saveForms(1);
		break;

	case "remove":
		removeForms($cid);
		break;

	case "publish":
		publishForms($cid, 1);
		break;

	case "unpublish":
		publishForms($cid, 0);
		break;

	case "data":
		viewData($id);
		break;

	case "ajax":
		ConfigInclude();
		break;

	default:
		showForms();
		break;
}

function ConfigInclude()
{
	$page = JRequest::getCmd('page', '', '', 'string');
	include_once JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/ajax/ajax_' . $page . '.php';
}

function showForms()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_form");
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT id, name, description, `show`"
			. "\nFROM #__support_form"
			. "\nORDER BY name",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editForms($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableForm($database);
	$row->load($uid);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $row->show, 'switch');

	MaQmaHtmlEdit::display($row, $lists);
}

function saveForms($apply)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableForm($database);
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

	if ($apply) {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=forms_edit&cid[0]=" . $row->id);
	} else {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=forms");
	}
}

function removeForms($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('form_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_form WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=forms");
}

function publishForms($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('form_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_form SET published='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=forms");
}

function viewData($id)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_form_" . $id);
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT *"
			. "\nFROM #__support_form_" . $id
			. "\nORDER BY sc_recorddate DESC"
			. "\nLIMIT " . $limitstart . "," . $limit
	);

	$rows = $database->loadAssocList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$row = null;
	$sql = "SELECT * FROM #__support_form WHERE id='" . $id . "'";
	$database->setQuery($sql);
	$row = $database->loadObject();

	$sql = "SELECT * FROM #__support_form_field WHERE id_form='" . $id . "' ORDER BY `order`";
	$database->setQuery($sql);
	$fields = $database->loadRowList();

	MaQmaHtmlData::display($rows, $pageNav, $id, $row, $fields);
}
