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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/mail.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/mails/tmpl/default.php";

// Set toolbar and page title
HelpdeskMailAdminHelper::addToolbar($task);
HelpdeskMailAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'mails', $task, $cid[0]);

switch ($task) {
	default:
		showMail();
		break;
}

function showMail()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_mail_fetch m, #__support_mail_log l WHERE l.id_mail_fetch=m.id ORDER BY l.date DESC");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	//$pageNav = new JPagination( $total, $pageNav->limitstart, $pageNav->limit );
	$pageNav = new JPagination($total, $limitstart, $limit);

	$query = "SELECT l.id, l.date, l.email, l.log, m.email as mailaccount FROM #__support_mail_fetch m, #__support_mail_log l WHERE l.id_mail_fetch=m.id ORDER BY l.date DESC";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav);
}
