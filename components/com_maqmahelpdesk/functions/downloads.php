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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');

// Get variables
$id = JRequest::getInt('id', 0);
$id_version = JRequest::getInt('id_version', 0);
$id_product = JRequest::getInt('id_product', 0);
$pversion = JRequest::getVar('pversion', '', '', 'string');
$description = JRequest::getVar('vdescription', '', '', 'string');
$vdate = JRequest::getVar('vdate', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'downloads', $task, $id);

switch ($task)
{
	case "getfile":
		HelpdeskValidation::ValidPermissions($task, 'D') ? GetFile($id, $id_version) : HelpdeskValidation::NoAccessQuit();
		break;

	case "product":
		HelpdeskValidation::ValidPermissions($task, 'D') ? Product($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "license":
		HelpdeskValidation::ValidPermissions($task, 'D') ? License($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "subscriptions":
		HelpdeskValidation::ValidPermissions($task, 'D') ? Subscriptions() : HelpdeskValidation::NoAccessQuit();
		break;

	case "subscribe":
		HelpdeskValidation::ValidPermissions($task, 'D') ? Subscribe($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "unsubscribe":
		HelpdeskValidation::ValidPermissions($task, 'D') ? Unsubscribe($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "editor":
		DownloadsEditor();
		break;

	default:
		HelpdeskValidation::ValidPermissions($task, 'D') ? Category($id) : HelpdeskValidation::NoAccessQuit();
		break;
}

function DownloadsEditor()
{
	$database = JFactory::getDBO();

	// get a list of the menu items
	$query = "SELECT c.id, c.cname AS name, c.cname AS title, c.published, c.parent, c.parent AS parent_id
			  FROM #__support_dl_category c 
			  WHERE c.published='1' 
			  ORDER BY c.parent";
	$database->setQuery($query);
	$mitems = $database->loadObjectList();

	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($mitems as $v) {
		$pt = $v->parent;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

	// assemble menu items to the array
	$mitems = array();
	$this_treename = '';
	foreach ($list as $item) {
		if ($this_treename) {
			if (strpos($item->treename, $this_treename) === false) {//$item->id != $row->id &&
				$mitems[] = JHTML::_('select.option', 0, $item->treename);
				$mitems[] = JHTML::_('select.option', $item->id, "- " . $item->treename);
			}
		} else {
			$mitems[] = JHTML::_('select.option', 0, $item->treename);
			$mitems[] = JHTML::_('select.option', $item->id, "- " . $item->treename);
		}
	}
	$products = JHTML::_('select.genericlist', $mitems, 'id_product', 'class="inputbox" size="15"', 'value', 'text', '');

	$sql = "SELECT m.id AS value, m.parent_id, m.parent_id AS parent, m.title, m.title AS text, m.menutype AS menutype, m.type AS type
			FROM #__menu m, #__extensions AS c
			WHERE c.extension_id = m.component_id AND m.link = 'index.php?option=com_maqmahelpdesk&view=mainpage' AND m.menutype = 'mainmenu'
			ORDER BY m.menutype, m.parent_id, m.ordering";
	$database->setQuery($sql);
	$menus = $database->loadObjectList();
	$menus = JHTML::_('select.genericlist', $menus, 'download_menu', 'class="inputbox" size="15"', 'value', 'text', 0);

	// Workgroup items
	$query = 'SELECT id as value, wkdesc as text' .
		' FROM #__support_workgroup' .
		' WHERE `show`=1' .
		' ORDER BY wkdesc';
	$database->setQuery($query);
	$wks = $database->loadObjectList();
	$wks = JHTML::_('select.genericlist', $wks, 'download_wk', 'class="inputbox" size="15"', 'value', 'text', 0);

	$tmplfile = HelpdeskTemplate::GetFile('editor/downloads');
	include $tmplfile;
}

function Subscriptions()
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$supportConfig = HelpdeskUtility::GetConfig();

	$database->setQuery("SELECT n.id, n.date as date_subscription, d.id, d.pname as product, d.updated as date_updated, c.cname as category, d.id_category, n.id_download FROM #__support_dl_notify n, #__support_dl d, #__support_dl_category c WHERE c.id=d.id_category AND n.id_download=d.id AND n.id_user='" . $user->id . "' ORDER BY n.date ASC");
	$rows = $database->loadObjectList();

	$i = 1;
	foreach ($rows as $key2 => $value2)
	{
		if (is_object($value2))
		{
			foreach ($value2 as $key3 => $value3)
			{
				$subscriptions[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$subscriptions[$i]['delete_link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=downloads_unsubscribe&id=" . $value3);
				}

				if ($key3 == 'id_category') {
					$subscriptions[$i]['category_link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=downloads_category&id=" . $value3);
				}

				if ($key3 == 'id_download') {
					$subscriptions[$i]['product_link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=downloads_product&id=" . $value3);
				}
			}
		}

		$i++;
	}

	// Sets the title
	HelpdeskUtility::PageTitle('showSubscriptions');
	$document->title = JText::_('dl_subs') . ' - ' . JText::_('DOWNLOADS');

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('downloads/subscriptions');
	include $tmplfile;
}

function Subscribe($id)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$database->setQuery("SELECT COUNT(*) FROM #__support_dl_notify WHERE id_download='" . $id . "' AND id_user='" . $user->id . "'");
	$subs = $database->loadResult();

	if ($subs == 0)
	{
		$database->setQuery("INSERT INTO #__support_dl_notify(id_download, id_user, `date`) VALUES('" . $id . "', '" . $user->id . "', '" . date("Y-m-d") . "')");
		$database->query();
		$message = JText::_('dl_notify_message');
		$type = 'i';
	}
	else
	{
		$message = JText::_('dl_already_subs');
		$type = 'w';
	}

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=downloads_product&id=' . $id . '&msg=' . urlencode($message) . '&msgtype=' . $type);
}

function Unsubscribe($id)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$sql = "DELETE FROM #__support_dl_notify
			WHERE id_download=" . (int) $id . "
			  AND id_user=" . (int) $user->id;
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=downloads_subscriptions');
}

function Category($id)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();

	// Get selected category
	$categoryTitle = '';
	if ($id > 0) {
		$database->setQuery("SELECT cname FROM #__support_dl_category WHERE id='" . $id . "'");
		$categoryTitle = $database->loadResult();
	}

	$document->title = JText::_('dl_products') . ($categoryTitle != '' ? ' - ' . $categoryTitle : '') . ' - ' . JText::_('DOWNLOADS');

	// Get categories
	$database->setQuery("SELECT DISTINCT(c.id), c.cname as title, c.description, c.image FROM #__support_dl_category AS c WHERE c.published='1' AND c.parent='" . $id . "' ORDER BY c.`ordering`");
	$rowsCat = $database->loadObjectList();
	echo $database->getErrorMsg();

	$i = 1;
	foreach ($rowsCat as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$categories[$i][$key3] = $value3;

				if ($key3 == 'id') {
					// Add the link tag
					$categories[$i]['link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=downloads_category&id=" . $value3);

					// Code to get the image
					$database->setQuery("SELECT id, cname, image FROM #__support_dl_category WHERE parent='" . $value3 . "' ORDER BY ordering");
					$rowsSubCat = $database->loadObjectList();
					$subcats = '';
					for ($z = 0; $z < count($rowsSubCat); $z++) {
						$rowSubCategory = &$rowsSubCat[$z];
						$subcats .= ($subcats != '' ? ',' : '') . $rowSubCategory->id;
					}
					$database->setQuery("SELECT MAX(updated) FROM #__support_dl WHERE id_category IN (" . $value3 . ($subcats != '' ? ',' . $subcats : '') . ")");
					$folder = '';
					$dif_week = 0;
					$dif_days = 0;

					if ($database->loadResult() != '')
					{
						if ($database->loadResult() == date("Y-m-d")) {
							$folder = 'green';
						} else {
							$update = mktime(0, 0, 0, JString::substr($database->loadResult(), 5, 2), JString::substr($database->loadResult(), 8, 2), JString::substr($database->loadResult(), 0, 4));
							$now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
							$dif = $now - $update;
							$dif_week = date("W", $dif) - 1; //weeks diference
							$dif_days = date("d", $dif) - 1; //days diference
							if ($dif_week >= 4) {
								$folder = 'red';
							} elseif ($dif_week >= 1 && $dif_week < 4) {
								$folder = 'yellow';
							} elseif ($dif_week == 0) {
								$folder = 'blue';
							}
						}
					}
					else
					{
						$folder = 'red';
					}

					// Add the image_folder tag
					$categories[$i]['image_folder'] = 'ribbon-' . $folder . '.png';
				}

				if ($key3 == 'image') {
					// Check if image is selected
					if ($categories[$i]['image'] == '') {
						if (!file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/folder.png')) {
							copy(JPATH_SITE . '/media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/48px/workgroup.png', JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/folder.png');
						}
						$categories[$i]['image'] = 'folder.png';
					}
				}

				if ($key3 == 'description') {
					$categories[$i]['description_short'] = strip_tags(JString::substr($value3, 0, 100)) . '...';
				}
			}
		}

		$i++;
	}

	// Get products
	$database->setQuery("SELECT p.id, p.pname as title, p.description, l.title as license, IF(p.image!='', CONCAT('logos/', p.image), 'themes/" . $supportConfig->theme_icon . "/48px/files.png') as image, groupid, evaluation, download_previous, download_version, p.registered_only FROM #__support_dl AS p LEFT JOIN #__support_dl_license AS l ON l.id=p.id_license WHERE p.published='1' AND p.id_category='" . $id . "' ORDER BY p.`ordering`");
	$rows = $database->loadObjectList();
	echo $database->getErrorMsg();

	$rows_edited = null;
	$z = 0;
	for ($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		if (CheckGroup($row->id, $row->groupid) == 1 && CheckDLAccess($row->id) == 1 && $row->download_version == 1) {
			$rows_edited[$z] = $rows[$i];
			$z++;
		}
	}
	$i = 1;
	if (count($rows_edited) > 0) {
		foreach ($rows_edited as $key2 => $value2) {
			if (is_object($value2)) {
				foreach ($value2 as $key3 => $value3) {
					$products[$i][$key3] = $value3;

					if ($key3 == 'id') {
						$products[$i]['link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=downloads_product&id=" . $value3);
					}

					if ($key3 == 'description') {
						$products[$i]['description_short'] = strip_tags(JString::substr(str_replace("\'", "'", $value3), 0, 100)) . '...';
						$products[$i]['description'] = str_replace("\'", "'", $value3);
					}

					if ($key3 == 'image') {
						$products[$i]['image'] = 'media/com_maqmahelpdesk/images/' . $value3;
					}
				}
			}

			$i++;
		}
	}

	// Sets the title
	HelpdeskUtility::PageTitle('showDownloads', ($id > 0 ? $categoryTitle : ''));

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('downloads/navigate');
	include $tmplfile;
}

function Product($id)
{
	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$uri = JURI::getInstance();
	$curl = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

	HelpdeskUtility::AppendResource('downloads.view.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	//Security measure against SQL Injection
	$id = mysql_escape_string($id);

	$database->setQuery("UPDATE #__support_dl SET hits=(hits+1) WHERE id='" . $id . "'");
	$database->query();
	echo $database->getErrorMsg();

	$database->setQuery("SELECT d.`id`, d.`id_category`, d.`pname` as title, d.`description`, IF(d.image!='', CONCAT('logos/', d.image), 'themes/" . $supportConfig->theme_icon . "/48px/files.png') as image, d.`url`, d.`plataform`, d.`date`, d.`hits`, l.`title` as license, l.description as license_description, d.`features`, d.`requirements`, d.`limitations`, c.cname as category, d.updated as date_updated, d.id_license, d.evaluation, d.download_version, d.groupid, d.download_previous, d.template_file, d.registered_only, d.image_view FROM #__support_dl AS d LEFT JOIN #__support_dl_license AS l ON l.id=d.id_license LEFT JOIN #__support_dl_category AS c ON d.id_category=c.id WHERE d.id='" . $id . "'");
	$row = null;
	$row = $database->loadObject();

	$document->addScriptDeclaration( 'var MQM_DOWNLOAD_LINK = "'.addslashes($row->category . ' - ' . $row->title).'";' );
	$document->addScriptDeclaration( 'var MQM_DOWNLOAD_TRIAL = "'.addslashes($row->category . ' - ' . $row->title).'";' );

	$previous = ($row->download_previous == 1) ? "" : "LIMIT 1";
	$database->setQuery("SELECT * FROM #__support_dl_version WHERE id_download='" . $id . "' ORDER BY `id` DESC " . $previous);
	$rows_versions = $database->loadObjectList();
	echo $database->getErrorMsg();

	$i = 1;
	foreach ($rows_versions as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$versions[$i][$key3] = $value3;

				if ($key3 == 'id') {
					if (CheckGroup($row->id, $row->groupid) == 1 && CheckDLAccess($row->id) == 1 && $row->download_version == 1 && $row->download_previous == 1 && $row->evaluation == '' && (!$row->registered_only || $row->registered_only && $user->id)) {
						$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&';
						$versions[$i]['download'] = JRoute::_($link . 'task=downloads_getfile&id=' . $id . '&id_version=' . $value3);
					} else {
						$versions[$i]['download'] = 'javascript:;';
					}
				}
				if ($key3 == 'filename') {
					$filename = $supportConfig->docspath . $value3;
					$versions[$i]['size'] = HelpdeskFile::FormatFileSize(filesize($filename));
				}
			}
		}

		$i++;
	}

	$database->setQuery("SELECT version, id, filename FROM #__support_dl_version WHERE id_download='" . $id . "' ORDER BY `id` DESC LIMIT 0, 1");
	$version = null;
	$version = $database->loadObject();
	echo $database->getErrorMsg();

	// Sets the title
	$extratitle[0] = $row->category;
	$extratitle[1] = $row->id_category;
	HelpdeskUtility::PageTitle('showDownload', $row->title, $extratitle);
	$document->title = $row->title . ' - ' . JText::_('DOWNLOADS');

	// Checks what link to show
	$can_download = 0;
	$get_trial = 0;
	$cant_get = 0;
	$can_download = (CheckGroup($row->id, $row->groupid) == 1 && CheckDLAccess($row->id) == 1 && $row->download_version == 1 && $row->evaluation == '' && (!$row->registered_only || $row->registered_only && $user->id) ? 1 : 0);
	$get_trial = ($row->evaluation != '' ? 1 : 0);
	$cant_get = (!$can_download && !$get_trial ? 1 : 0);

	$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&';
	if (isset($version)) {
		$download_link = JRoute::_($link . 'task=downloads_getfile&id=' . $row->id . '&id_version=' . $version->id);
	} else {
		$download_link = 'javascript:alert(\'' . JText::_('dl_no_versions') . '\');';
	}

	$filename = $supportConfig->docspath . $version->filename;

	// Display toolbar
	HelpdeskToolbar::Create();

	$dispatcher =& JDispatcher::getInstance();
	JPluginHelper::importPlugin('maqmahelpdesk');
	$dispatcher->trigger('onBeforeDisplayContent', array(& $row));
	$row->description = JHTML::_('content.prepare', $row->description);

	$tmplfile = HelpdeskTemplate::GetFile('downloads/' . ($row->template_file != '' ? $row->template_file : 'view'));
	include $tmplfile;
}

function License($id)
{
	$database = JFactory::getDBO();

	$database->setQuery("SELECT title, description FROM #__support_dl_license WHERE id=" . (int) $id);
	$row = null;
	$row = $database->loadObject();
	echo $database->getErrorMsg();

	echo '<h4>' . $row->title . '</h4>';
	echo $row->description;
}

function GetFile($id, $id_version)
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();

	// Check if it's possible to download to prevent URL changes
	$database->setQuery("SELECT d.`id`, d.`id_category`, d.`pname` as title, d.`description`, IF(d.image!='', CONCAT('logos/', d.image), 'themes/" . $supportConfig->theme_icon . "/48px/files.png') as image, d.`url`, d.`plataform`, d.`date`, d.`hits`, l.`title` as license, l.description as license_description, d.`features`, d.`requirements`, d.`limitations`, c.cname as category, d.updated as date_updated, d.id_license, d.evaluation, d.download_version, d.groupid, d.download_previous, d.registered_only FROM #__support_dl d LEFT JOIN #__support_dl_license l ON l.id=d.id_license LEFT JOIN #__support_dl_category c ON d.id_category=c.id WHERE d.id='" . $id . "'");
	$row = null;
	$row = $database->loadObject();

	$can_download = ((CheckGroup($row->id, $row->groupid) == 1 && CheckDLAccess($row->id) == 1 && $row->download_version == 1 && $row->evaluation == '' && (!$row->registered_only || $row->registered_only && $user->id)) ? 1 : 0);

	if ($can_download) {
		//Updates the stats
		$sql = "INSERT INTO #__support_dl_stats(id_download, id_version, id_user, dldate, ipaddress)
				VALUES('" . $id . "', '" . $id_version . "', '" . $user->id . "', '" . date("Y-m-d H:i:s") . "', '" . HelpdeskUser::GetIP() . "')";
		$database->setQuery($sql);
		$database->query();
		echo $database->getErrorMsg();

		//Get the filename
		HelpdeskFile::Download($id_version, 0, 'D');
	}
}

function CheckGroup($id_download, $groups)
{
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	// Variables
	$isValid = 0;
	$unregister = 0;
	$user_group = null;

	// Get the default group
	$database->setQuery("SELECT id FROM #__support_dl_group WHERE isdefault='1'");
	$default_group = $database->loadResult();

	// Get ID of the group that is related with the unregistered users
	$database->setQuery("SELECT id FROM #__support_dl_group WHERE unregister='1'");
	$unregister = $database->loadResult();

	// Get ID of the group where the user belongs
	if ($is_client > 0) {
		$database->setQuery("SELECT id_group FROM #__support_dl_users WHERE id_user='" . $is_client . "'");
		$user_group = $database->loadObjectList();

		// If the user is connected but doesn't belong to a group add it to the default group
		if ($default_group > 0 && !count($user_group)) {
			$database->setQuery("INSERT INTO #__support_dl_users(id_group, id_user) VALUES('" . $default_group . "', '" . $is_client . "')");
			$database->query();
		}
	}

	// Check if the unregistered users group have permission to the
	// file and/or if the user group have permissions to the file
	$recsgroups = explode(",", $groups);
	for ($i = 0; $i < count($recsgroups); $i++) {
		if ($unregister == $recsgroups[$i]) {
			$isValid = 1;
		}
		for ($x = 0; $x < count($user_group); $x++) {
			if ($user_group[$x]->id_group == $recsgroups[$i]) {
				$isValid = 1;
			}
		}
	}

	// If user is from support override the previous value
	if ($is_support) {
		$isValid = 1;
		// If is not support and there's no groups for the download
	} elseif (!$is_support && $groups == '') {
		$isValid = 1;
	}

	return $isValid;
}

function CheckDLAccess($id_download)
{
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	// Variables
	$isValid = 0;
	$access_recs = 0;

	// Check if there's any record in the restricted access to this file
	$database->setQuery("SELECT COUNT(*) FROM #__support_dl_access WHERE id_download='" . $id_download . "'");
	$access_recs = $database->loadResult();

	if ($is_client == 0 && $access_recs > 0) {
		$isValid = 0;

	} elseif ($access_recs > 0 && $is_client > 0) {
		// Check if the user have records to access the file
		$access_recs = 0;
		$database->setQuery("SELECT COUNT(*) FROM #__support_dl_access WHERE id_download='" . $id_download . "' AND id_user='" . $is_client . "' AND servicefrom<='" . date("Y-m-d") . "' AND serviceuntil>='" . date("Y-m-d") . "' AND isactive='1'");
		$user_access = $database->loadResult();
		if ($user_access == 0) {
			$isValid = 0;
		} else {
			$isValid = 1;
		}
	} elseif ($access_recs == 0 && $is_client > 0) {
		$isValid = 1;
	} elseif ($is_client == 0 && $access_recs == 0) {
		$isValid = 1;
	}

	// If user is from support override the previous value
	if ($is_support) {
		$isValid = 1;
	}

	return $isValid;
}
