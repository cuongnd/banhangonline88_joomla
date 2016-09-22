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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/stats.php';

require_once "components/com_maqmahelpdesk/views/stats/tmpl/default.php";

// Set toolbar and page title
HelpdeskStatsAdminHelper::addToolbar($task);
HelpdeskStatsAdminHelper::setDocument();

$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}
$year = JRequest::getVar('year', HelpdeskDate::DateOffset("%Y"), '', 'string');
$month = JRequest::getVar('month', HelpdeskDate::DateOffset("%m"), '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'stats', $task, $cid[0]);

switch ($task) {
	case "stats":
		ShowStats($year, $month);
		break;
	case "hits":
		ShowHits();
		break;
}

function ShowStats($year, $month)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '01', JText::_('month01'));
	$months[] = JHTML::_('select.option', '02', JText::_('month02'));
	$months[] = JHTML::_('select.option', '03', JText::_('month03'));
	$months[] = JHTML::_('select.option', '04', JText::_('month04'));
	$months[] = JHTML::_('select.option', '05', JText::_('month05'));
	$months[] = JHTML::_('select.option', '06', JText::_('month06'));
	$months[] = JHTML::_('select.option', '07', JText::_('month07'));
	$months[] = JHTML::_('select.option', '08', JText::_('month08'));
	$months[] = JHTML::_('select.option', '09', JText::_('month09'));
	$months[] = JHTML::_('select.option', '10', JText::_('month10'));
	$months[] = JHTML::_('select.option', '11', JText::_('month11'));
	$months[] = JHTML::_('select.option', '12', JText::_('month12'));
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $month);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_dl p INNER JOIN #__support_dl_version v ON p.id=v.id_download INNER JOIN #__support_dl_stats s ON p.id=s.id_download LEFT JOIN #__users u ON u.id=s.id_user AND v.id=s.id_version WHERE YEAR(s.dldate)=" . $database->quote($year) . " AND MONTH(s.dldate)=" . $database->quote($month));
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT p.pname AS product, u.name, v.version, v.filename, s.dldate, s.ipaddress FROM #__support_dl p INNER JOIN #__support_dl_version v ON p.id=v.id_download INNER JOIN #__support_dl_stats s ON p.id=s.id_download AND v.id=s.id_version LEFT JOIN #__users u ON u.id=s.id_user WHERE YEAR(s.dldate)=" . $database->quote($year) . " AND MONTH(s.dldate)=" . $database->quote($month) . " ORDER BY p.pname DESC", $pageNav->limitstart, $pageNav->limit);
	$downloads = $database->loadObjectList();

	HTML_stats::showStats($downloads, $pageNav, $lists, $month, $year);
}

function ShowHits()
{
	$database = JFactory::getDBO();

	$sql = "SELECT p.`id`, p.`pname` AS product, p.`hits`, c.`cname` AS category
			FROM `#__support_dl` AS p
			     INNER JOIN `#__support_dl_category` AS c ON c.`id`=p.`id_category`
			ORDER BY p.`hits` DESC";
	$database->setQuery($sql);
	$hits = $database->loadObjectList();

	HTML_stats::showHits($hits);
}
