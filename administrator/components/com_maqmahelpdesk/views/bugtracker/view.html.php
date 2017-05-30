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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/bugtracker.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/jomsocial.php';

require_once "components/com_maqmahelpdesk/views/bugtracker/tmpl/default.php";

// Set toolbar and page title
HelpdeskBugTrackerAdminHelper::addToolbar();
HelpdeskBugTrackerAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}
$id = JRequest::getVar('id', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'bugtracker', $task, $cid[0]);

switch ($task) {
	case "remove":
		removeBugtracker($cid);
		break;

	case "pending":
		publishBugtracker($cid, 'P');
		break;

	case "open":
		publishBugtracker($cid, 'O');
		break;

	case "inprogress":
		publishBugtracker($cid, 'I');
		break;

	case "resolved":
		publishBugtracker($cid, 'R');
		break;

	case "closed":
		publishBugtracker($cid, 'C');
		break;

	case "reopened":
		publishBugtracker($cid, 'D');
		break;

	default:
		showBugtracker();
		break;
}

function removeBugtracker($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("DELETE FROM #__support_bugtracker WHERE id IN (" . $cids . ")");
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=bugtracker");
}

function publishBugtracker($cid = null, $action = 'P')
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("UPDATE #__support_bugtracker SET `status`='" . $action . "' WHERE id IN (" . $cids . ")");
	$database->query();

	// Post in JomSocial wall
	if ($supportConfig->js_post_bugtracker_wall) {
		$sql = "SELECT b.`id_user`, b.`id`, b.`title`, b.`content`, b.`id_workgroup`, b.`id_category`, u.`name`
				FROM `#__support_bugtracker` AS b
					 INNER JOIN `#__users` AS u ON u.`id`=b.`id_user`
				WHERE b.`id` IN (" . $cids . ")";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		for ($i = 0; $i < count($rows); $i++) {
			$row = $rows[$i];
			$comment = sprintf(JText::_("post_bug_wall"), $row->name, JRoute::_(JURI::root() . "index.php?option=com_maqmahelpdesk&Itemid=" . HelpdeskUtility::GetItemid() . "&id_workgroup=" . $row->id_workgroup . "&task=bugtracker_view&id=" . $row->id . "&id_category=" . $row->id_category), $row->title);
			HelpdeskJomSocial::Post($row->id_user, $comment, $row->content);
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=bugtracker");
}

function showBugtracker()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('bugtracker_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT COUNT(*) FROM #__support_bugtracker ORDER BY id DESC");
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT b.`id`, b.`priority`, u.`name` AS user, c.`name` AS category, w.`wkdesc` AS workgroup, c.`id_workgroup`, b.`date_created`, b.`date_updated`, b.`title`, b.`content`, b.`status`, b.`type`, COUNT(m.`id`) AS messages, (SELECT COUNT(m2.`id`) FROM #__support_bugtracker_messages AS m2 WHERE m2.id_bugtracker=b.id AND m2.published=0) AS pending
			FROM #__support_bugtracker AS b 
				 INNER JOIN #__support_category AS c ON b.id_category=c.id
				 INNER JOIN #__support_workgroup AS w ON c.id_workgroup=w.id
				 INNER JOIN #__users AS u ON u.id=b.id_user 
				 LEFT JOIN #__support_bugtracker_messages AS m ON m.id_bugtracker=b.id
			GROUP BY b.`id`, u.`name`, c.`name`, w.`wkdesc`, c.`id_workgroup`, b.`date_created`, b.`date_updated`, b.`title`, b.`content`, b.`status`, b.`type`
			ORDER BY b.id DESC";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav);
}
