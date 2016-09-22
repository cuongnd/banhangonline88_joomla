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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/download_category.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download_category.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/dlcategory/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/dlcategory/tmpl/edit.php";

// Set toolbar and page title
HelpdeskDownloadCategoryAdminHelper::addToolbar($task);
HelpdeskDownloadCategoryAdminHelper::setDocument();

$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

global $product_id;

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'dlcategory', $task, $cid[0]);

switch ($task) {
	case "new":
		editCategory(null);
		break;

	case "save":
		saveCategory();
		break;

	case "edit":
		editCategory($cid[0]);
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

	case "cancel":
	case "dlcategory":
		viewCategory();
		break;

	case 'saveorder':
		saveOrder();
		break;
}

function viewCategory()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_dl_category");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	$query = "SELECT c.id, c.cname AS title, c.description, c.published, c.parent AS parent_id, c.`level`, c.slug FROM #__support_dl_category c ORDER BY c.parent, c.ordering";
	$database->setQuery($query);
	$rows2 = $database->loadObjectList();

	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($rows2 as $v) {
		$pt = $v->parent_id;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, 10), 0, 0);

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	// slice out elements based on limits
	$list = array_slice($list, $pageNav->limitstart, $pageNav->limit);

	MaQmaHtmlDefault::display($list, $pageNav);
}

function editCategory($Categoryid)
{
	$lists = array();
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	$row = new MaQmaHelpdeskTableDownloadCategory($database);
	$row->load($Categoryid);

	$lists['parent'] = ParentCategory($row, 0);
	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');
	$lists['delete_image'] = HelpdeskForm::SwitchCheckbox('radio', 'delete_image', $captions, $values, 0, 'switch');

	MaQmaHtmlEdit::display($row, $lists);
}

function saveCategory()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableDownloadCategory($database);
	$delete_image = JRequest::getInt("delete_image", 0);
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

	// Delete existing image
	if ($delete_image)
	{
		unlink(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/' . $row->image);
	}

	// Saves the image - icon in the database
	if ($_FILES['image']['name'] != '') {
		// Check if the folder exists, if not creates it
		if (!is_dir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/')) {
			mkdir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/');
		}

		$msg = HelpdeskFile::Upload($row->id, 'CA', "image", JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/', '', 1, 1, 'image');
	}

	// Update the category level
	if ($row->parent) {
		// Get parent level
		$sql = "SELECT `level`
				FROM `#__support_dl_category`
				WHERE `id`=" . $row->parent;
		$database->setQuery($sql);
		$level = $database->loadResult();
		// Increase the level of current category
		$sql = "UPDATE `#__support_dl_category`
				SET `level`=" . ($level + 1) . "
				WHERE `id`=" . $row->id;
		$database->setQuery($sql);
		$database->query();
	}

	if ($database->getErrorMsg()) {
		echo "<p>" . $database->getErrorMsg();
	} else {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=dlcategory");
	}
}

function publishCategory($cid, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('category_action') . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("UPDATE #__support_dl_category SET published='$publish' WHERE id IN (" . $cids . ")");
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=dlcategory");

}

function removeCategory($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_dl_category WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=dlcategory");
}

function saveOrder(&$cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++)
	{
		$sql = "UPDATE `#__support_dl_category`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=dlcategory", JText::_('new_ordering_save'));
}

function ParentCategory(&$row, $iscat)
{
	$database = JFactory::getDBO();

	// get a list of the menu items
	$query = "SELECT c.id, c.cname AS title, c.published, c.parent AS parent_id FROM #__support_dl_category c WHERE c.published='1' ORDER BY c.parent";
	$database->setQuery($query);
	$mitems = $database->loadObjectList();

	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($mitems as $v) {
		$pt = $v->parent_id;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

	// assemble menu items to the array
	$mitems = array();
	$mitems[] = JHTML::_('select.option', '0', JText::_('top'));

	$this_treename = '';
	foreach ($list as $item) {
		if ($this_treename) {
			if ($item->id != $row->id && strpos($item->treename, $this_treename) === false) {
				$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
			}
		} else {
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
	}
	if (!$iscat) {
		$parent = JHTML::_('select.genericlist', $mitems, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent);
	} else {
		$parent = JHTML::_('select.genericlist', $mitems, 'id_category', 'class="inputbox" size="1"', 'value', 'text', $row->id_category);
	}
	return $parent;
}
