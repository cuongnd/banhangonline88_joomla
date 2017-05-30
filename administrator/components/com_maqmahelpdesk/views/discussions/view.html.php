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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/public_discussions.php';

require_once "components/com_maqmahelpdesk/views/discussions/tmpl/default.php";

// Set toolbar and page title
HelpdeskPublicDiscussionsAdminHelper::addToolbar();
HelpdeskPublicDiscussionsAdminHelper::setDocument();

// Get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}
$id = JRequest::getVar('id', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'discussions', $task, $cid[0]);

switch ($task) {
	case "remove":
		removeDiscussions($cid);
		break;

	case "publish":
		publishDiscussion($cid, 1);
		break;

	case "unpublish":
		publishDiscussion($cid, 0);
		break;

	default:
		showDiscussions();
		break;
}

function removeDiscussions($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("DELETE FROM #__support_discussions WHERE id IN (" . $cids . ")");
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=discussions");
}

function publishDiscussion($cid = null, $action = 0)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("UPDATE #__support_discussions SET `published`=" . (int)$action . " WHERE id IN (" . $cids . ")");
	$database->query();

	// Post in JomSocial wall
	if ($supportConfig->js_post_question_wall) {
		$sql = "SELECT d.`id_user`, d.`id`, d.`title`, d.`content`, d.`id_workgroup`, d.`id_category`
				FROM `#__support_discussions` AS d
				WHERE d.`id` IN (" . $cids . ")";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			$comment = sprintf(JText::_("js_created_question"), JRoute::_(JURI::root() . "index.php?option=com_maqmahelpdesk&Itemid=" . HelpdeskUtility::GetItemid() . "&id_workgroup=" . $row->id_workgroup . "&task=discussion_view&id=" . $row->id . "&id_category=" . $row->id_category), $row->title, $row->title);
			HelpdeskJomSocial::Post($row->id_user, $comment, $row->content);
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=discussions");
}

function showDiscussions()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT COUNT(*) FROM #__support_discussions ORDER BY id DESC");
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT d.`id`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, w.`wkdesc` AS workgroup, c.`name` AS category, u.`name` AS user, d.`id_workgroup`, COUNT(m.`id`) AS messages, (SELECT COUNT(m2.`id`) FROM #__support_discussions_messages AS m2 WHERE m2.id_discussion=d.id AND m2.published=0) AS pending
			FROM #__support_discussions AS d 
				 INNER JOIN #__support_workgroup AS w ON d.id_workgroup=w.id
				 INNER JOIN #__support_category AS c ON d.id_category=c.id
				 INNER JOIN #__users AS u ON u.id=d.id_user 
				 LEFT JOIN #__support_discussions_messages AS m ON m.id_discussion=d.id
			GROUP BY d.`id`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, w.`wkdesc`, c.`name`, u.`name`, d.`id_workgroup`
			ORDER BY d.id DESC";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav);
}
