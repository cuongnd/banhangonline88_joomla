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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/category.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/category.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/category/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/category/tmpl/edit.php";

// Set toolbar and page title
HelpdeskCategoryAdminHelper::addToolbar($task);
HelpdeskCategoryAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'category', $task, $cid[0]);

switch ($task) {
	case "categories":
		getCategories();
		break;

	case "new":
		editCategory(0);
		break;

	case "edit":
		editCategory($cid[0]);
		break;

	case "save":
		saveCategory();
		break;

	case "remove":
		removeCategory($cid);
		break;

	case "publish":
		publishCategory($cid, 1);
		break;

	case "unpublish":
		publishCategory($cid, 0);
		break;

	case 'saveorder':
		saveOrder();
		break;

	default:
		showCategory();
		break;
}

function showCategory()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getInt('limitstart', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('category_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onchange="document.filterForm.submit();"', 'value', 'text', $id_workgroup);

	$rows = getCategoryChildren(true, 0, 1);

	MaQmaHtmlDefault::display($rows, $lists);
}

function getCategories()
{
	echo HelpdeskForm::BuildCategories(0, false, false, false, true, false, false, 'parent');
}

function getCategoryChildren($loop = false, $id = 0, $level = 1)
{
	$database = JFactory::getDBO();
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	$sql = "SELECT c.id, c.name AS title, c.show, w.wkdesc as workgroup, c.parent, c.tickets, c.downloads, c.kb, c.bugtracker, c.`discussions`, $level AS level, c.`glossary`, c.slug
			FROM #__support_category c, #__support_workgroup w
			WHERE c.id_workgroup=w.id 
			  " . ($id_workgroup ? "AND id_workgroup=" . $id_workgroup : "") . "
			  AND c.parent=%s
			ORDER BY w.wkdesc, c.ordering, c.name";// AND c.level=%s
	$sql = sprintf($sql, $id);//, $level
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	if ($loop) {
		$return = array();
		foreach ($rows as $row)
		{
			$items = getCategoryChildren(true, $row->id, ($level+1));
			$item = array();
			$item[] = $row;
			$return = array_merge($return, $item, $items);
		}
		$rows = $return;
	} else {
		//$return = $rows;
	}

	return $rows;
}

function editCategory($uid = 0)
{
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$document->addScriptDeclaration('var IMQM_DESCRIPTION_REQUIRED = "' . addslashes(JText::_('description_required')) . '";');
	$document->addScriptDeclaration('var IMQM_DEPARTMENT_REQUIRED = "' . addslashes(JText::_('WORKGROUP_REQUIRED')) . '";');
	$row = new MaQmaHelpdeskTableCategory($database);
	$row->load($uid);

	HelpdeskUtility::AppendResource('category_edit.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('select3'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onchange="GetCategories();"', 'value', 'text', $row->id_workgroup);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['downloads'] = HelpdeskForm::SwitchCheckbox('radio', 'downloads', $captions, $values, $row->downloads, 'switch');
	$lists['tickets'] = HelpdeskForm::SwitchCheckbox('radio', 'tickets', $captions, $values, $row->tickets, 'switch');
	$lists['kb'] = HelpdeskForm::SwitchCheckbox('radio', 'kb', $captions, $values, $row->kb, 'switch');
	$lists['discussions'] = HelpdeskForm::SwitchCheckbox('radio', 'discussions', $captions, $values, $row->discussions, 'switch');
	$lists['bugtracker'] = HelpdeskForm::SwitchCheckbox('radio', 'bugtracker', $captions, $values, $row->bugtracker, 'switch');
	$lists['glossary'] = HelpdeskForm::SwitchCheckbox('radio', 'glossary', $captions, $values, $row->glossary, 'switch');
	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $row->show, 'switch');
	$lists['parent'] = HelpdeskForm::BuildCategories($row->parent, false, false, false, true, false, false, 'parent');

	MaQmaHtmlEdit::display($row, $lists);
}

function saveCategory()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableCategory($database);
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

	// Update the category level
	if ($row->parent) {
		// Get parent level
		$sql = "SELECT `level`
				FROM `#__support_category`
				WHERE `id`=" . $row->parent;
		$database->setQuery($sql);
		$level = $database->loadResult();

		// Increase the level of current category
		$sql = "UPDATE `#__support_category`
				SET `level`=" . ($level + 1) . "
				WHERE `id`=" . $row->id;
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=category");
}

function removeCategory($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script> alert('" . JText::_('category_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_category WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=category");
}

function publishCategory($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('category_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_category SET `show`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=category");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	$id_workgroup = JRequest::getInt('id_workgroup', $session->get('id_workgroup', '', 'maqmahelpdesk'));
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++)
	{
		if ($orders[$i] != '')
		{
			$sql = "UPDATE `#__support_category`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
			$database->setQuery($sql);
			$database->query();
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=category&id_workgroup=" . $id_workgroup, JText::_('new_ordering_save'));
}
