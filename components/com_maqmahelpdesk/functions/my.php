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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$id = JRequest::getVar('id', 0, '', 'int');
$limit = intval(JRequest::getVar('limit', '', '', 'string'));
$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

// Activities logger
HelpdeskUtility::ActivityLog('site', 'my', $task, $id);

switch ($task) {
	case "kb":
		showKB($limit, $limitstart);
		break;

	case "delbookmark":
		delBookmark($id);
		break;

	case "downloads":
		showDownloads();
		break;

	case "bookmark":
		showBookmark();
		break;
}

function showDownloads()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$workgroupSettings = HelpdeskDepartment::GetSettings();

	// Set title
	HelpdeskUtility::PageTitle('showMyDownloads');

	// Get knowledge base articles
	$sql = "SELECT a.`isactive`, a.`serialno`, a.`servicefrom`, a.`serviceuntil`, d.`pname`, c.`cname`
			FROM `#__support_dl_access` AS a
				 INNER JOIN `#__support_dl` AS d ON d.`id`=a.`id_download`
				 INNER JOIN `#__support_client_users` AS cu ON cu.`id_client`=a.id_user
				 LEFT JOIN `#__support_dl_category` AS c ON c.`id`=d.`id_category`
			WHERE cu.`id_user`=" . $user->id . "
			ORDER BY c.`cname`, d.`pname`, a.`servicefrom`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('users/downloads');
	include $tmplfile;
}

function showKB($limit, $limitstart)
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	// Set title
	HelpdeskUtility::PageTitle('showMyKB');

	$limit = $limit ? $limit : $CONFIG->list_limit;

	// Get knowledge base articles
	$database->setQuery("SELECT DISTINCT k.id, k.kbcode as code, k.kbtitle as title, k.views, u.name as author, k.date_created, k.date_updated FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u WHERE c.`show`='1' AND c.id_workgroup='" . $id_workgroup . "' AND c.id=kc.id_category AND kc.id_kb=k.id AND k.publish='1' AND k.id_user='" . $user->id . "' AND u.id=k.id_user GROUP BY k.id, k.kbcode, k.kbtitle, k.views ORDER BY k.date_updated DESC LIMIT " . $limitstart . ", " . $limit);
	$articles = $database->loadObjectList();

	$i = 1;
	foreach ($articles as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$articles_rows[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$articles_rows[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $value3;
					$articles_rows[$i]['link_edit'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $value3;
					$articles_rows[$i]['rate_image'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 1) : JText::_('unrated'));
					$articles_rows[$i]['rate'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 0) : JText::_('unrated'));
				}

				if ($key3 == 'date_created')
					$articles_rows[$i]['date_created'] = date($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'date_updated')
					$articles_rows[$i]['date_updated'] = date($supportConfig->date_short, strtotime($value3));
			}
		}

		$i++;
	}

	$database->setQuery("SELECT COUNT(DISTINCT k.id) FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u WHERE c.`show`='1' AND c.id_workgroup='" . $id_workgroup . "' AND c.id=kc.id_category AND kc.id_kb=k.id AND k.publish='1' AND k.id_user='" . $user->id . "' AND u.id=k.id_user");
	$total = $database->loadResult();

	if ($total <= $limit) $limitstart = 0;
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	// Set if user can edit article
	$edit = 0;
	if ($is_support) {
		$edit = 1;
	}

	// Takes care of pagination
	$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=my_kb';
	$pagelinks = $pageNav->getPagesLinks($link);
	$pagecounter = $pageNav->getPagesCounter();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('users/my_kb');
	include $tmplfile;
}

function showBookmark()
{
	global $supportOptions;

	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	// Set title
	HelpdeskUtility::PageTitle('showMyBookmarks');

	// Set if user can edit article
	$edit = 0;
	if ($is_support) {
		$edit = 1;
	}

	// Get knowledge base articles
	$database->setQuery("SELECT DISTINCT(k.id), k.kbcode as code, k.kbtitle as title, k.views, k.id_user, k.date_created, k.date_updated, b.id AS id_bookmark FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__support_bookmark as b WHERE b.id_user='" . $user->id . "' AND b.source='K' AND k.id=b.id_bookmark AND c.`show`='1' AND c.id_workgroup='" . $id_workgroup . "' AND c.id=kc.id_category AND kc.id_kb=k.id AND k.publish='1' ORDER BY k.date_updated DESC");
	$articles = $database->loadObjectList();

	$i = 1;
	foreach ($articles as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$articles_rows[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$articles_rows[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $value3;
					$articles_rows[$i]['link_edit'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $value3;
					$articles_rows[$i]['workgroup'] = HelpdeskDepartment::GetName($id_workgroup);
					$articles_rows[$i]['rate_image'] = (HelpdeskForm::GetRate($value3, 'T', 0) ? HelpdeskForm::GetRate($value3, 'T', 1) : JText::_('unrated'));
					$articles_rows[$i]['rate'] = (HelpdeskForm::GetRate($value3, 'T', 0) ? HelpdeskForm::GetRate($value3, 'T', 0) : JText::_('unrated'));
				}

				if ($key3 == 'id_user') {
					$articles_rows[$i]['author'] = HelpdeskUser::GetName($value3);
				}

				if ($key3 == 'id_bookmark') {
					$articles_rows[$i]['delete_bookmark'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=my_delbookmark&id=' . $value3;
				}
			}
		}

		$i++;
	}

	// Get tickets
	$database->setQuery("SELECT t.id as dbid, t.ticketmask as ticketid, t.subject, t.id_status, t.assign_to, t.id_priority, t.date, t.duedate, t.id_user, t.last_update, t.id_workgroup, b.id AS id_bookmark FROM #__support_ticket as t, #__support_bookmark as b WHERE b.id_user='" . $user->id . "' AND b.source='T' AND t.id=b.id_bookmark AND t.id_workgroup='" . $id_workgroup . "'  ORDER BY t.duedate DESC");
	$tickets = $database->loadObjectList();

	$i = 1;
	foreach ($tickets as $key2 => $value2) {
		$duedate = '';
		$priority = '';
		$status = '';
		$assign = '';

		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$tickets_rows[$i][$key3] = $value3;

				if ($key3 == 'dbid') {
					$tickets_rows[$i]['messages'] = HelpdeskTicket::GetMessages($value3);
					$tickets_rows[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $value3;
				}

				if ($key3 == 'id_status') {
					$status = $value3;
					$tickets_rows[$i]['status'] = HelpdeskStatus::GetName($value3);
				}

				if ($key3 == 'assign_to')
					$assign = $value3;
				$tickets_rows[$i]['assigned'] = HelpdeskUser::GetName($value3);

				if ($key3 == 'id_user')
					$tickets_rows[$i]['user'] = HelpdeskUser::GetName($value3);

				if ($key3 == 'id_priority')
					$priority = $value3;
				$tickets_rows[$i]['priority'] = HelpdeskPriority::GetName($value3);

				if ($key3 == 'id_user')
					$tickets_rows[$i]['client'] = HelpdeskClient::GetName($value3);

				if ($key3 == 'date')
					$tickets_rows[$i]['date_created'] = date($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'last_update')
					$tickets_rows[$i]['date_updated'] = date($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'duedate') {
					$duedate = $value3;
					$tickets_rows[$i]['elapsed_time'] = HelpdeskDate::ElapsedTime(JString::substr($value3, 0, 4), JString::substr($value3, 5, 2), JString::substr($value3, 8, 2), JString::substr($value3, 11, 2), JString::substr($value3, 14, 2), HelpdeskDate::DateOffset("%Y"), HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d"), HelpdeskDate::DateOffset("%H"), HelpdeskDate::DateOffset("%M"));
				}

				if ($key3 == 'id_bookmark')
					$tickets_rows[$i]['delete_bookmark'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=my_delbookmark&id=' . $value3;
			}
		}

		$tickets_rows[$i]['icon_duedate'] = HelpdeskTicket::IsDueDateValid($duedate, $priority, $status, 0, $assign, 0);
		$tickets_rows[$i]['icontxt_duedate'] = HelpdeskTicket::IsDueDateValid($duedate, $priority, $status, 0, $assign, 1);

		$i++;
	}

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('users/bookmarks');
	include $tmplfile;
}

function delBookmark($id)
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	$database->setQuery("DELETE FROM #__support_bookmark WHERE id='" . $id . "'");
	$database->query();
	$msg = JText::_('item_removed_from_bookmarks');

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=my_bookmark&msg=' . $msg);
}
