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
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/download.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/template.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/download_version.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/product/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/product/tmpl/edit.php";
require_once "components/com_maqmahelpdesk/views/product/tmpl/version.php";

// Set toolbar and page title
HelpdeskDownloadAdminHelper::addToolbar($task);
HelpdeskDownloadAdminHelper::setDocument($task);

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');

$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

$id_product = JRequest::getVar('id_product', 0, '', 'int');
$id = JRequest::getVar('id', 0, '', 'int');
$id_version = JRequest::getVar('id_version', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'product', $task, $cid);

switch ($task)
{
	case "new":
		editProduct(null);
		break;

	case "apply":
		saveProduct(1);
		break;

	case "save":
		saveProduct(0);
		break;

	case "edit":
		editProduct($cid[0]);
		break;

	case "delete":
		removeProduct($cid);
		break;

	case "publish":
		publishProduct($cid, 1);
		break;

	case "unpublish":
		publishProduct($cid, 0);
		break;

	case "cancel":
	case "product":
		viewProduct();
		break;

	case "editversion":
		editVersion($id_product, $id_version);
		break;

	case "saveversion":
		saveVersion();
		break;

	case "delversion":
		deleteVersion($id_product, $id_version);
		break;

	case "download":
		HelpdeskFile::Download($id, 0, 'D');
		break;

	case 'saveorder':
		saveOrder();
		break;

	case "copy":
		copyProduct($cid);
		break;
}

function copyProduct($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	$sql = "INSERT INTO `#__support_dl`(`id_category`, `pname`, `description`, `ordering`, `url`, `plataform`, `date`, `hits`, `id_license`, `groupid`, `features`, `requirements`, `limitations`, `published`, `expired`, `updated`, `offline`, `image`, `evaluation`, `download_version`, `download_previous`, `template_file`, `registered_only`, `image_view`, `slug`)
			SELECT `id_category`, CONCAT(`pname`, ' - " . JText::_('copy') . "'), `description`, `ordering`, `url`, `plataform`, `date`, `hits`, `id_license`, `groupid`, `features`, `requirements`, `limitations`, `published`, `expired`, `updated`, `offline`, `image`, `evaluation`, `download_version`, `download_previous`, `template_file`, `registered_only`, `image_view`, CONCAT(`slug`, '-" . JText::_('copy') . "')
			FROM `#__support_dl`
			WHERE `id` IN (" . implode(',', $cid) . ")";
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product", JText::_('RECORDS_DUPLICATED'));
}

function Parent(&$row, $iscat)
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
			if ($item->id != $row->id && strpos($item->title, $this_treename) === false) {
				$mitems[] = JHTML::_('select.option', $item->id, $item->title);
			}
		} else {
			$mitems[] = JHTML::_('select.option', $item->id, $item->title);
		}
	}
	if (!$iscat) {
		$parent = JHTML::_('select.genericlist', $mitems, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent_id);
	} else {
		$parent = JHTML::_('select.genericlist', $mitems, 'id_category', 'class="inputbox" size="1"', 'value', 'text', $row->id_category);
	}
	return $parent;
}

function viewProduct()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_dl");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$query = "SELECT d.id, d.pname, d.description, d.published, c.cname, d.slug
			  FROM #__support_dl AS d
				   LEFT JOIN #__support_dl_category As c ON c.id = d.id_category
			  ORDER BY d.ordering";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);

	if (!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}

	$rows = $database->loadObjectList();
	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editProduct($productid)
{
	$lists = array();

	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableDownload($database);
	$row->load($productid);

	// Build groups select list
	$sql = "SELECT `id` AS value, `gname` AS text FROM #__support_dl_group";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$lists['id_group'] = JHTML::_('select.genericlist', $rows_wk, 'id_group', 'class="span10" size="10" multiple="multiple"', 'value', 'text', 0);

	// Build licenses select list
	$sql = "SELECT `id` AS value, `title` AS text FROM #__support_dl_license";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$lists['id_license'] = JHTML::_('select.genericlist', $rows_wk, 'id_license', 'class="inputbox" style="width:250px" size="1"', 'value', 'text', $row->id_license);

	$database->setQuery("SELECT * FROM #__support_dl_version WHERE id_download='" . $row->id . "'");
	$versions = $database->loadObjectList();

	$database->setQuery("SELECT * FROM #__support_dl_group ORDER BY gname");
	$groups = $database->loadObjectList();

	$lists['id_category'] = Parent($row, 1);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');
	$lists['offline'] = HelpdeskForm::SwitchCheckbox('radio', 'offline', $captions, $values, $row->offline, 'switch');
	$lists['download_version'] = HelpdeskForm::SwitchCheckbox('radio', 'download_version', $captions, $values, $row->download_version, 'switch');
	$lists['download_previous'] = HelpdeskForm::SwitchCheckbox('radio', 'download_previous', $captions, $values, $row->download_previous, 'switch');
	$lists['registered_only'] = HelpdeskForm::SwitchCheckbox('radio', 'registered_only', $captions, $values, $row->registered_only, 'switch');
	$lists['delete_image1'] = HelpdeskForm::SwitchCheckbox('radio', 'delete_image1', $captions, $values, 0, 'switch');
	$lists['delete_image2'] = HelpdeskForm::SwitchCheckbox('radio', 'delete_image2', $captions, $values, 0, 'switch');

	MaQmaHtmlEdit::display($row, $lists, $versions, $groups);
}

function saveProduct($apply)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableDownload($database);
	$delete_image1 = JRequest::getInt("delete_image1", 0);
	$delete_image2 = JRequest::getInt("delete_image2", 0);
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

	$message = "";

	// Delete existing image
	if ($delete_image1)
	{
		unlink(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/' . $row->image2);
	}
	if ($delete_image2)
	{
		unlink(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/' . $row->image_view);
	}

	// Saves the image - icon in the database
	if ($_FILES['image2']['name'] != '')
	{
		// Check if the folder exists, if not creates it
		if (!is_dir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/'))
		{
			mkdir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/');
		}

		$message.= HelpdeskFile::Upload($row->id, 'D', "image2", JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/', '', 1, 1, 'image');
	}

	// Saves the image in the database
	if ($_FILES['image_view']['name'] != '')
	{
		// Check if the folder exists, if not creates it
		if (!is_dir(JPATH_SITE . '/components/com_maqmahelpdesk/images/downloads/'))
		{
			mkdir(JPATH_SITE . '/components/com_maqmahelpdesk/images/downloads/');
		}

		$message.= HelpdeskFile::Upload($row->id, 'D', "image_view", JPATH_SITE . '/components/com_maqmahelpdesk/images/downloads/', '', 1, 1, 'image_view');
	}

	if ($apply) {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product_edit&cid[0]=" . $row->id);
	} else {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product", $message);
	}
}

function publishProduct($cid, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('product_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$database->setQuery("UPDATE #__support_dl SET published='$publish'"
		. "\nWHERE id IN ($cids)"
	);
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product");

}

function removeProduct($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid)) {
		$cids = implode(',', $cid);

		// Delete products
		$database->setQuery("DELETE FROM #__support_dl WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete files
		$database->setQuery("SELECT filename FROM #__support_dl_version WHERE id_download IN (" . $cids . ")");
		$versions = $database->loadObjectList();

		for ($i = 0; $i < count($versions); $i++) {
			$version = $versions[$i];
			unlink(JPATH_SITE . '/components/com_maqmahelpdesk/files/' . $version->filename);
		}

		// Delete versions
		$database->setQuery("DELETE FROM #__support_dl_version WHERE id_download IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product");
}

function saveVersion()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$upload_msg = 1;
	$id_product = JRequest::getVar('id_product', 0, '', 'int');
	$id_version = JRequest::getVar('id_version', 0, '', 'int');
	$pversion = JRequest::getVar('pversion', '', '', 'string');
	$vdescription = JRequest::getVar('vdescription', '', '', 'string', 4); // 4 = JREQUEST_ALLOWHTML
	$vdate = JRequest::getVar('vdate', '', '', 'string');
	$filename_exist = JRequest::getVar('filename_exists', '', '', 'string');

	if ($filename_exist != '') {
		$filename = $filename_exist;
	} else {
		$filename = $_FILES['filename']['name'];
	}

	if ($id_version == 0) {
		$database->setQuery("INSERT INTO #__support_dl_version(id_download, `date`, description, version, filename, filename_original) VALUES('" . $id_product . "', " . $database->quote($vdate) . ", " . $database->quote($vdescription) . ", " . $database->quote($pversion) . ", " . $database->quote($filename) . ", " . $database->quote($filename) . ")");
		$database->query();

		$message = JText::_('version_saved');
		$id_version = $database->insertid();
		if ($_FILES['filename']['name'] != "" && $filename_exist == '') {
			$upload_msg = HelpdeskFile::Upload($id_version, 'V', "filename", $supportConfig->docspath . '/', '', 1);
		}
	} else {
		$message = JText::_('version_saved');

		$database->setQuery("UPDATE #__support_dl_version SET `date`=" . $database->quote($vdate) . ", description=" . $database->quote($vdescription) . ", version=" . $database->quote($pversion) . "" . ($filename_exist != '' ? ", filename=" . $database->quote($filename_exist) . ", filename_original=" . $database->quote($filename_exist) : "") . " WHERE id='" . $id_version . "'");
		$database->query();
		print "<p>UPDATE #__support_dl_version SET `date`=" . $database->quote($vdate) . ", description=" . $database->quote($vdescription) . ", version=" . $database->quote($pversion) . "" . ($filename_exist != '' ? ", filename=" . $database->quote($filename_exist) . ", filename_original=" . $database->quote($filename_exist) : "") . " WHERE id='" . $id_version . "'";

		if ($_FILES['filename']['name'] != "" && $filename_exist == '') {
			$upload_msg = HelpdeskFile::Upload($id_version, 'V', "filename", $supportConfig->docspath . '/', '', 1);
		}
	}

	if (!$upload_msg && $filename_exist == '') {
		$database->setQuery("DELETE FROM #__support_dl_version WHERE id='" . $id_version . "'");
		$database->query();
		$message = JText::_('version_not_saved');
	} else {
		if ($supportConfig->download_notification) {
			$notifier = notifyUsers($id_product);
			$message .= ' ' . ($notifier ? str_replace('%1', $notifier, JText::_('dl_usersnotified')) : '');
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product_edit&cid[0]=" . $id_product, $message);
}

function notifyUsers($id)
{
	$database = JFactory::getDBO();
	$CONFIG = new JConfig();
	$database->setQuery("SELECT u.email, u.name FROM #__users u, #__support_dl_notify n WHERE u.id=n.id_user AND n.id_download=" . $id);
	$users = $database->loadObjectList();

	$sql = "SELECT p.`pname`, c.`cname`
			FROM `#__support_dl` AS p
				 INNER JOIN `#__support_dl_category` AS c ON c.`id`=p.`id_category`
			WHERE p.`id`=$id";
	$database->setQuery($sql);
	$product = $database->loadObject();

	$subj = str_replace('%1', $product->pname, JText::_('notify_subject'));

	if (count($users) > 0) {
		for ($j = 0, $m = count($users); $j < $m; $j++) {
			$row = &$users[$j];
			$var_set = array('%category%' => $product->cname, '%product%' => $product->pname, '%user%' => $row->name, '%subject%' => $subj, '%url%' => JURI::root());
			$body = HelpdeskTemplate::Parse($var_set, 'product_updated');
			JUtility::sendMail($CONFIG->mailfrom, $CONFIG->fromname, $row->email, $subj, $body, 1);
		}
	}

	return count($users);
}

function deleteVersion($id_product, $id_version)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$database->setQuery("SELECT filename FROM #__support_dl_version WHERE id='" . $id_version . "'");
	$filename = $database->loadResult();

	unlink($supportConfig->docspath . $filename);

	$database->setQuery("DELETE FROM #__support_dl_version WHERE id='" . $id_version . "'");
	$database->query();
	$message = "Version deleted.";

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product_edit&cid[0]=$id_product", $message);
}

function editVersion($id_product, $id)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableDownloadVersion($database);
	$row->load($id);

	MaQmaHtmlVersion::display($id_product, $id, $row);
}

function saveOrder(&$cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++)
	{
		$sql = "UPDATE `#__support_dl`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=product", JText::_('new_ordering_save'));
}
