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

switch ($task) {
	case "getclient":
		GetClientAjax();
		break;
	case "getuser":
		GetUserAjax();
		break;
	case "getusermails":
		GetUserMailsAjax();
		break;
	case "cvs":
		GetCVS();
		break;
	case "csvclientm":
		GetCVSClientReport();
		break;
	case "filemanager":
		Filemanager();
		break;
	case "slug":
		Slug();
		break;
}

function Slug()
{
	$title = JRequest::getVar('title', '', '', 'string');
	$type = JRequest::getVar('type', '', '', 'string');

	echo HelpdeskUtility::CreateSlug($title, $type);
}

function Filemanager()
{
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$html = '';

	if (!$user->id)
		return false;

	if ($handle = opendir('.')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				$html .= '<img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . (is_dir($entry) ? '' : '') . '.png" alt="" /> $entry <br />';
			}
		}
		closedir($handle);
	}

	echo $html;
}

function GetCVSClientReport()
{
	include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reportclientcsv.php');
}

function GetCVS()
{
	include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/makecvs.php');
}

function GetUserMailsAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$id_client = HelpdeskUser::IsClient();

	if (!$userl->id || !$supportConfig->extra_email_notification)
	{
		return false;
	}

	$data = '';
	$where = '';

	$name = mysql_escape_string(trim($_GET["q"]));
	if (!$name)
	{
		return;
	}

	if (!$is_support && $supportConfig->extra_email_notification == 2)
	{
		$where = "AND u.id IN (SELECT c.`id_user` FROM `#__support_client_users` WHERE `id_client`=" . $id_client . ")";
	}

	// Search Users and Clients tables
	$sql = "SELECT u.name, u.email
			FROM #__users AS u
			WHERE (UCASE(u.name) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.email) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.username) LIKE UCASE('%" . ($name) . "%'))
			   $where
			ORDER BY u.name, u.email 
			LIMIT 0, 10";
	$database->setQuery($sql);
	$users = $database->loadObjectList();

	for ($i = 0; $i < count($users); $i++) {
		$userr = $users[$i];
		$data .= $userr->email . '|' . $userr->name . "\n";
	}

	echo $data;
}

function GetUserAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();

	if (!$userl->id)
		return false;

	$data = '';

	$name = JRequest::getVar('q', '', '', 'string');
	if (!$name) return;

	// Maximum number of users that will be displayed in the results ajax box
	$results_item_limit = 20;

	// Search Users and Clients tables
	$sql = "SELECT DISTINCT u.id, u.name, c.clientname, c.id AS id_client, u.email
			FROM #__users u 
				 LEFT JOIN #__support_client_users cu ON cu.id_user=u.id 
				 LEFT JOIN #__support_client c ON c.id=cu.id_client 
			WHERE UCASE(u.name) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(c.clientname) LIKE UCASE('%" . ($name) . "%')
			   OR UCASE(u.username) LIKE UCASE('%" . ($name) . "%')
			ORDER BY u.name, c.clientname 
			LIMIT 0, " . $results_item_limit . "";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	for ($i = 0; $i < count($rows); $i++)
	{
		$row = $rows[$i];
		$data .= $row->id . "|" . $row->name . "|" . $row->id_client . "|" . $row->clientname . "|" . $row->email . "|" . HelpdeskUser::GetAvatar($row->id) . "\n";
	}

	echo $data;
}

function GetClientAjax()
{
	$database = JFactory::getDBO();
	$userl = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();

	if (!$userl->id)
		return false;

	$data = '';

	$name = JRequest::getVar('q', '', '', 'string');
	if (!$name) return;

	// Maximum number of users that will be displayed in the results ajax box
	$results_item_limit = 20;

	// Search Users and Clients tables
	$sql = "SELECT c.id, c.clientname, c.logo
			FROM #__support_client c 
			WHERE UCASE(c.clientname) LIKE UCASE('%" . ($name) . "%')
			ORDER BY c.clientname 
			LIMIT 0, " . $results_item_limit . "";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	for ($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		$data .= $row->id . "|" . $row->clientname . "|" . ($row->logo != '' ? JURI::root() . 'media/com_maqmahelpdesk/images/logos/' . $row->logo : JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/48px/clients.png') . "\n";
	}

	echo $data;
}
