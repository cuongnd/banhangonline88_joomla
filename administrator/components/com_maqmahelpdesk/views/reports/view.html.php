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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/report_builder.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/reports.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/reports_builder.php';

// Set toolbar and page title
if ($task == 'buildernew' || $task == 'builderedit' || $task == 'builder' || $task == 'builderreport')
{
	HelpdeskReportBuilderAdminHelper::addToolbar($task);
	HelpdeskReportBuilderAdminHelper::setDocument();
}
else
{
	HelpdeskReportAdminHelper::addToolbar($task);
}

$id = JRequest::getVar('id', 0, '', 'int');
$report = JRequest::getVar('report', '', '', 'string');
$year = JRequest::getVar('year', HelpdeskDate::DateOffset("%Y"), '', 'string');
$month = JRequest::getVar('month', HelpdeskDate::DateOffset("%m"), '', 'string');
$month = ((int)$month < 10 && $month != '00' ? '0' . (int)$month : $month);

$id_workgroup = JRequest::getInt('id_workgroup', 0);
$client = JRequest::getVar('client', 0, '', 'int');
$id_user = JRequest::getVar('id_user', 0, '', 'int');
$f_year = JRequest::getVar('f_year', '', '', 'string');
$f_month = JRequest::getVar('f_month', '', '', 'string');
$f_status = JRequest::getVar('f_status', 0, '', 'int');
$f_priority = JRequest::getVar('f_priority', 0, '', 'int');
$f_category = JRequest::getVar('f_category', '-', '', 'string');
$f_workgroup = JRequest::getVar('f_workgroup', 0, '', 'int');
$f_client = JRequest::getVar('f_client', 0, '', 'int');
$f_user = JRequest::getVar('f_user', 0, '', 'int');
$f_staff = JRequest::getVar('f_staff', 0, '', 'int');
$f_source = JRequest::getVar('f_source', '', '', 'string');
$f_customfields = JRequest::getVar('f_customfields', 1, '', 'int');

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

if ($report == '') {
	$report = $task;
}

$GLOBALS['report'] = $report;

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'reports', $report);

switch ($report) {
// DELETE EXPORT HISTORY
	case "delete":
		DeleteExport($id);
		break;

// LOCK EXPORT HISTORY
	case "unlock":
		UnLockExport($id);
		break;

// EXPORT
	case "ticketsexport":
		ExportTickets();
		break;

// SUPPORT STAFF
	case "supportanalysis":
		Analysis('S', $year, $month, $id_workgroup, $client, $id_user, $print);
		break;

// CLIENTS
	case "clientanalysis":
		Analysis('C', $year, $month, $id_workgroup, $client, $id_user, $print);
		break;

// WORKGROUPS
	case "wkanalysis":
		Analysis('W', $year, $month, $id_workgroup, $client, $id_user, $print);
		break;

// TIMESHEET
	case "timesheets":
		Analysis('TS', $year, $month, $id_workgroup, $client, $id_user, $print);
		break;

// TIMESHEET
	case "timesheetd":
		Analysis('TD', $year, $month, $id_workgroup, $client, $id_user, $print);
		break;

// DUEDATES
	case "duedate":
		DueDate($id_workgroup, $client, $id_user, $print);
		break;

// REPORT BUILDER
	case "builder":
		Builder();
		break;

	case "buildernew":
		BuilderEdit(0);
		break;

	case "builderedit":
		BuilderEdit($cid[0]);
		break;

	case "buildersave":
		BuilderSave();
		break;

	case "builderremove":
		BuilderDelete($cid);
		break;

	case "builderreport":
		BuilderReport($id, $f_year, $f_month, $f_status, $f_priority, $f_category, $f_workgroup, $f_client, $f_user, $f_staff, $f_source);
		break;

// CLIENT MONTHLY REPORT
	case "clientm":
		ClientMonth($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $print);
		break;

// TICKET MONTHLY REPORT
	case "ticketm":
		TicketMonth($year, $month, $id_workgroup, $client, $print);
		break;

	case "clientmdetail":
		ClientMonthDetail($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $print);
		break;

	case "status":
		StatusReport($year, $month, $id_workgroup, $client, $f_status, $f_staff, $print);
		break;

	case "geo":
		GeoReport($year, $month, $id_workgroup);
		break;

	case "ratings":
		RatingsReport($year, $month, $id_workgroup);
		break;

	case "downloads":
		DownloadsReport($year, $month);
		break;
}

function DownloadsReport($year, $month)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/downloads.php";

	$where = null;
	if ($year)
	{
		$where[] = "YEAR(t.`date`) = '" . $year . "'";
	}
	if ($month)
	{
		$where[] = "MONTH(t.`date`) = '" . $month . "'";
	}

	// Year filter
	$years[] = JHTML::_('select.option', '0', JText::_('ALL'));
	for ($i = HelpdeskDate::MinYear(); $i <= (int) HelpdeskDate::DateOffset("%Y"); $i++)
	{
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1"', 'value', 'text', $year);

	// Month filter
	$months[] = JHTML::_('select.option', '0', JText::_('ALL'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1"', 'value', 'text', $month);

	// Get the records
	// CATEGORY __ DOWNLOAD __ NUMBER OF DOWNLOADS
	$sql = "SELECT c.`countryname`, COUNT(t.`id`) AS total
			FROM `#__support_ticket` AS t
				 INNER JOIN `#__support_country` AS c ON INET_ATON(t.`ipaddress`)>=c.`startip` AND INET_ATON(t.`ipaddress`)<=c.`endip`" .
				(is_array($where) && count($where) ? "WHERE " . implode("AND ", $where) : "") . "
			GROUP BY c.`countrycode`
			ORDER BY total DESC";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	reports_html::show($rows, $lists);
}

function GeoReport($year, $month, $id_workgroup)
{
	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/geo.php";

	// Javascript dependencies
	HelpdeskUtility::AppendResource('jsapi', 'http://www.google.com/', 'js', true);

	$where = null;
	if ($year)
	{
		$where[] = "YEAR(t.`date`) = '" . $year . "'";
	}
	if ($month)
	{
		$where[] = "MONTH(t.`date`) = '" . $month . "'";
	}
	if ($id_workgroup)
	{
		$where[] = "t.`id_workgroup` = '" . $id_workgroup . "'";
	}

	$database = JFactory::getDBO();

	$sql = "SELECT c.`countryname`, COUNT(t.`id`) AS total
			FROM `#__support_ticket` AS t
				 INNER JOIN `#__support_country` AS c ON INET_ATON(t.`ipaddress`)>=c.`startip` AND INET_ATON(t.`ipaddress`)<=c.`endip`" .
		(is_array($where) && count($where) ? "WHERE " . implode("AND ", $where) : "") . "
			GROUP BY c.`countrycode`
			ORDER BY total DESC";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	// Year filter
	$years[] = JHTML::_('select.option', '0', JText::_('ALL'));
	for ($i = HelpdeskDate::MinYear(); $i <= (int) HelpdeskDate::DateOffset("%Y"); $i++)
	{
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1"', 'value', 'text', $year);

	// Month filter
	$months[] = JHTML::_('select.option', '0', JText::_('ALL'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1"', 'value', 'text', $month);

	// Department filter
	$sql = "SELECT `id` AS value, `wkdesc` AS text
			FROM #__support_workgroup
			ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('ALL'))), $rows_wk);
	$lists['department'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1"', 'value', 'text', $id_workgroup);

	reports_html::show($rows, $lists);
}

function RatingsReport($year, $month, $id_workgroup)
{
	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/ratings.php";

	// Javascript dependencies
	HelpdeskUtility::AppendResource('jsapi', 'http://www.google.com/', 'js', true);

	$where = null;
	$where_rates = null;
	if ($year)
	{
		$where[] = "YEAR(t.`date`)='" . $year . "'";
		$where_rates[] = "YEAR(r.`date`)='" . $year . "'";
	}
	if ($month)
	{
		$where[] = "MONTH(t.`date`)='" . $month . "'";
		$where_rates[] = "MONTH(r.`date`)='" . $month . "'";
	}
	if ($id_workgroup)
	{
		$where[] = "t.`id_workgroup`=" . (int) $id_workgroup;
		$where_rates[] = "t.`id_workgroup`=" . (int) $id_workgroup;
	}

	$database = JFactory::getDBO();

	// Get overview details
	$sql = "SELECT COUNT(r.`id`) AS total, (SUM(r.`rate`)/COUNT(r.`id`)) AS average
			FROM `#__support_rate` AS r
				 INNER JOIN `#__support_ticket` AS t ON t.`id`=r.`id_table`
			WHERE r.`source`='T' " . ($id_workgroup ? "AND t.`id_workgroup`=" . (int) $id_workgroup : "");
	$database->setQuery($sql);
	$row = $database->loadObject();
	$overview['all_time_ratings'] = $row->total;
	$overview['all_time_average'] = number_format($row->average, 2);

	// Get overview details
	$sql = "SELECT COUNT(r.`id`) AS total, (SUM(r.`rate`)/COUNT(r.`id`)) AS average
			FROM `#__support_rate` AS r
				 INNER JOIN `#__support_ticket` AS t ON t.`id`=r.`id_table`
			WHERE r.`source`='T' " . (is_array($where_rates) && count($where_rates) ? " AND ".implode(" AND ", $where_rates) : "");
	$database->setQuery($sql);
	$row = $database->loadObject();
	$overview['ratings'] = $row->total;
	$overview['average'] = number_format($row->average, 2);

	// Get rating per agent
	$sql = "SELECT u.`name`, COUNT(r.`id`) AS total, (SUM(r.`rate`)/COUNT(r.`id`)) AS average
			FROM `#__support_rate` AS r
				 INNER JOIN `#__support_ticket` AS t ON t.`id`=r.`id_table`
				 INNER JOIN `#__users` AS u ON u.`id`=t.`assign_to`
			WHERE r.`source`='T' " . (is_array($where_rates) && count($where_rates) ? " AND ".implode(" AND ", $where_rates) : "") . "
			GROUP BY u.`name`
			ORDER BY total DESC";
	$database->setQuery($sql);
	$agents = $database->loadObjectList();

	// Year filter
	$years[] = JHTML::_('select.option', '0', JText::_('ALL'));
	for ($i = HelpdeskDate::MinYear(); $i <= HelpdeskDate::DateOffset("%Y"); $i++)
	{
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1"', 'value', 'text', $year);

	// Month filter
	$months[] = JHTML::_('select.option', '0', JText::_('ALL'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1"', 'value', 'text', $month);

	// Department filter
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('ALL'))), $rows_wk);
	$lists['department'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1"', 'value', 'text', $id_workgroup);

	reports_html::show($overview, $agents, $lists, $year, $month, $id_workgroup);
}

// REPORT BUILDER
function Builder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/builder.php";

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_reports");
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT id, title, description"
		. "\n FROM #__support_reports"
		. "\n ORDER BY title",
		$pageNav->limitstart, $pageNav->limit
	);


	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	reports_html::show($rows, $pageNav);
}

function BuilderEdit($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableReportBuilder($database);
	$row->load($uid);

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/builder_edit.php";

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['chart_percentage'] = HelpdeskForm::SwitchCheckbox('radio', 'chart_percentage', $captions, $values, $row->chart_percentage, 'switch');

	$years[] = JHTML::_('select.option', '00', ' - Select - ');
	$years[] = JHTML::_('select.option', 'YY', 'Current Year');
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'f_year', 'class="inputbox" size="1"', 'value', 'text', $row->f_year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	$months[] = JHTML::_('select.option', 'MM', JText::_('current_month'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'f_month', 'class="inputbox" size="1"', 'value', 'text', $row->f_month);

	$chart_type[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$chart_type[] = JHTML::_('select.option', 'column', JText::_('columns'));
	$chart_type[] = JHTML::_('select.option', 'pie', JText::_('pie'));
	$chart_type[] = JHTML::_('select.option', 'bar', JText::_('bars'));
	$lists['chart_type'] = JHTML::_('select.genericlist', $chart_type, 'chart_type', 'class="inputbox" size="1"', 'value', 'text', $row->chart_type);

	$report_type[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$report_type[] = JHTML::_('select.option', '1', JText::_('tickets'));
	$report_type[] = JHTML::_('select.option', '2', JText::_('times'));
	$lists['report_type'] = JHTML::_('select.genericlist', $report_type, 'type', 'class="inputbox" size="1"', 'value', 'text', $row->type);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'f_status', 'class="inputbox" size="1"', 'value', 'text', $row->f_status);

	// Build Priority select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'f_priority', 'class="inputbox" size="1"', 'value', 'text', $row->f_priority);

	// Build Category select list
	$sql = "SELECT `id` AS value, `name` AS text
			FROM #__support_category
			ORDER BY `name`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('uncategorized'))), $rows_wk);
	$lists['category'] = JHTML::_('select.genericlist', $rows_wk, 'f_category', 'class="inputbox" size="1"', 'value', 'text', $row->f_category);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text
			FROM #__support_workgroup
			ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'f_workgroup', 'class="inputbox" size="1"', 'value', 'text', $row->f_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text
			FROM #__support_client
			ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'f_client', 'class="inputbox" size="1" onchange="changeDynaList(\'f_user\', ordersOS, document.adminForm.f_client.options[document.adminForm.f_client.selectedIndex].value, originalPos, originalOrderOS);"', 'value', 'text', $row->f_client);

	$sub_os = array();

	for ($i = 0, $n = count($rows_wk); $i < $n; $i++)
	{
		$wkrow = &$rows_wk[$i];
		$sub_os[0][] = JHTML::_('select.option', $wkrow->value, JText::_('selectlist'));
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client
			FROM #__users u, #__support_client_users c
			WHERE c.id_user=u.id
			ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text
			FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . "
			ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	}

	// Get the list of other support staff for the assignment selection list
	$assignlist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text
			FROM #__support_permission as p, #__users as u
			WHERE p.id_user=u.id
			ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	for ($staff = 0; $staff < count($rows_staff); $staff++) {
		$row_staff = $rows_staff[$staff];
		$assignlist[] = JHTML::_('select.option', $row_staff->value, $row_staff->text);
	}
	$lists['assign'] = JHTML::_('select.genericlist', $assignlist, 'f_staff', 'class="inputbox" size="1"', 'value', 'text', $row->f_staff);

	// Build Client select list
	$sql = "SELECT `id` AS value, `caption` AS text
			FROM #__support_custom_fields
			ORDER by caption";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['cfields'] = JHTML::_('select.genericlist', $rows_wk, 'cfgroupby', 'class="inputbox" size="1"', 'value', 'text', $row->groupby);

	// Build Client select list
	$sql = "SELECT `id` AS value, `caption` AS text
			FROM #__support_custom_fields
			ORDER by caption";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['cfields2'] = JHTML::_('select.genericlist', $rows_wk, 'groupby2', 'class="inputbox" size="1"', 'value', 'text', $row->groupby2);

	reports_html::show($row, $lists, $sub_os);
}

function BuilderSave()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableReportBuilder($database);
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

	$groupby = JRequest::getVar("groupby", "", "", "string");
	$cfgroupby = JRequest::getVar("cfgroupby", "0", "", "string");

	if ($cfgroupby > 0)
	{
		$sql = "UPDATE `#__support_reports`
				SET `groupby`='" . $cfgroupby . "'
				WHERE `id`=" . $row->id;
		$database->setQuery($sql);
		$database->query();
	}
	else
	{
		$sql = "UPDATE `#__support_reports`
				SET `groupby`='" . $groupby . "'
				WHERE `id`=" . $row->id;
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=reports_builder");
}

function BuilderDelete($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('report_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_reports WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=reports_builder");
}

function BuilderReport($id, $f_year, $f_month, $f_status, $f_priority, $f_category, $f_workgroup, $f_client, $f_user, $f_staff, $f_source)
{
	$database = JFactory::getDBO();
	$CONFIG = new JConfig();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/builder_show.php";

	// Get the report info
	$database->setQuery("SELECT * FROM #__support_reports WHERE id='" . $id . "'");
	$report = $database->loadObject();

	// Filters
	$f_workgroup = $f_workgroup == '' ? $report->f_workgroup : $f_workgroup;
	$f_category = $f_category == '' ? $report->f_category : $f_category;
	$f_client = $f_client == '' ? $report->f_client : $f_client;
	$f_user = $f_user == '' ? $report->f_user : $f_user;
	$f_year = $f_year == '' ? $report->f_year : $f_year;
	$f_year = ($f_year == 'YY' ? date("Y") : $f_year);
	$f_month = $f_month == '' ? $report->f_month : $f_month;
	$f_month = ($f_month == 'MM' ? date("m") : $f_month);
	$f_priority = $f_priority == '' ? $report->f_priority : $f_priority;
	$f_status = $f_status == '' ? $report->f_status : $f_status;
	$f_source = $f_source == '' ? $report->f_source : $f_source;
	$f_staff = $f_staff == '' ? $report->f_staff : $f_staff;

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'f_year', 'class="inputbox" size="1"', 'value', 'text', $f_year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'f_month', 'class="inputbox" size="1"', 'value', 'text', $f_month);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'f_status', 'class="inputbox" size="1"', 'value', 'text', $f_status);

	// Build Priority select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'f_priority', 'class="inputbox" size="1"', 'value', 'text', $f_priority);

	// Build Category select list
	$sql = "SELECT `id` AS value, `name` AS text FROM #__support_category ORDER BY `name`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '-', JText::_('selectlist')), JHTML::_('select.option', '0', 'Uncategorized')), $rows_wk);
	$lists['category'] = JHTML::_('select.genericlist', $rows_wk, 'f_category', 'class="inputbox" size="1"', 'value', 'text', $f_category);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'f_workgroup', 'class="inputbox" size="1"', 'value', 'text', $f_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'f_client', 'class="inputbox" size="1"' . ($report->sf_user ? 'onchange="changeDynaList(\'f_user\', ordersOS, document.adminForm.f_client.options[document.adminForm.f_client.selectedIndex].value, originalPos, originalOrderOS);"' : ''), 'value', 'text', $f_client);

	$sub_os = array();

	for ($i = 0, $n = count($rows_wk); $i < $n; $i++) {
		$wkrow = &$rows_wk[$i];
		$sub_os[0][] = JHTML::_('select.option', $wkrow->value, JText::_('selectlist'));
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client FROM #__users u, #__support_client_users c WHERE c.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . " ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	}

	// Get the list of other support staff for the assignment selection list
	$assignlist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission as p, #__users as u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	for ($staff = 0; $staff < count($rows_staff); $staff++) {
		$row_staff = $rows_staff[$staff];
		$assignlist[] = JHTML::_('select.option', $row_staff->value, $row_staff->text);
	}
	$lists['assign'] = JHTML::_('select.genericlist', $assignlist, 'f_staff', 'class="inputbox" size="1"', 'value', 'text', $f_staff);

	// Build the report SQL
	$groupby = ''; // used in the SELECT , GROUP BY , ORDER BY
	$groupby2 = ''; // used in the SELECT , GROUP BY , ORDER BY
	$select = ''; // used in the SELECT
	$where = ''; // used in the WHERE

	// Builds the WHERE
	if ($f_workgroup > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`id_workgroup`='" . $f_workgroup . "'";
	}
	if ($f_category >= 0 && $f_category != '-') {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`id_category`='" . $f_category . "'";
	}
	if ($f_client > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`cl`.`id`='" . $f_client . "'";
	}
	if ($f_user > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`id_user`='" . $f_user . "'";
	}
	if ($f_year != '' && $f_year != '00') {
		if ($report->type == '1') {
			$where .= ($where == '' ? " WHERE " : " AND ") . "YEAR(`t`.`date`)='" . $f_year . "'";
		} elseif ($report->type == '2') {
			$where .= ($where == '' ? " WHERE " : " AND ") . "YEAR(`ta`.`date`)='" . $f_year . "'";
		}
	}
	if ($f_month != '' && $f_month != '00') {
		if ($report->type == '1') {
			$where .= ($where == '' ? " WHERE " : " AND ") . "MONTH(`t`.`date`)='" . $f_month . "'";
		} elseif ($report->type == '2') {
			$where .= ($where == '' ? " WHERE " : " AND ") . "MONTH(`ta`.`date`)='" . $f_month . "'";
		}
	}
	if ($f_priority > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`id_priority`='" . $f_priority . "'";
	}
	if ($f_status > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`id_status`='" . $f_status . "'";
	}
	if ($f_source != '' && !is_numeric($f_source)) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`source`='" . $f_source . "'";
	}
	if ($f_staff > 0) {
		$where .= ($where == '' ? " WHERE " : " AND ") . "`t`.`assign_to`='" . $f_staff . "'";
	}

	// Prepares the Select, Group By and Order By
	$label1 = '';
	$select = '';
	$groupby = '';
	switch ($report->groupby) {
		case 'WK':
			$groupby = '`wk`.`wkdesc`';
			//$select = $report->type == '1' ? "AS '".JText::_('workgroup')."'" : "'0'";
			$select = "AS '" . JText::_('workgroup') . "'";
			$label1 = JText::_('workgroup');
			break;
		case 'CL':
			$groupby = '`cl`.`clientname`';
			//$select = $report->type == '1' ? "AS '".JText::_('client')."'" : "'0'";
			$select = "AS '" . JText::_('client') . "'";
			$label1 = JText::_('client');
			$where .= ($where == '' ? " WHERE " : " AND ") . "`cl`.`id`>0";
			break;
		case 'ST':
			$groupby = '`st`.`description`';
			//$select = $report->type == '1' ? "AS '".JText::_('status')."'" : "'0'";
			$select = "AS '" . trim(JText::_('status')) . "'";
			$label1 = trim(JText::_('status'));
			break;
		case 'YR':
			if ($report->type == '1') {
				$groupby = 'year(`t`.`date`)';
				//$select = "AS 'Year'";
			} elseif ($report->type == '2') {
				$groupby = 'year(`ta`.`date`)';
				//$select = "AS '0'";
			}
			$select = "AS '" . JText::_('year') . "'";
			$label1 = JText::_('year');
			break;
		case 'DY':
			$groupby = 'substring(`t`.`date`, 1, 10)';
			//$select = $report->type == '1' ? "AS '".JText::_('date')."'" : "'0'";
			$select = "AS '" . JText::_('date') . "'";
			$label1 = JText::_('date');
			break;
		case 'AS':
			if ($report->type == '1') {
				$groupby = "CASE WHEN t.assign_to='' THEN '<i>" . JText::_('icon_onhold') . "</i>' ELSE `as`.`name` END";
			} elseif ($report->type == '2') {
				$groupby = "`as`.`name`";
			}
			$select = "AS '" . JText::_('support_member') . "'";
			$label1 = JText::_('support_member');
			break;
		case 'CA':
			$groupby = '`ca`.`description`';
			//$select = $report->type == '1' ? "AS '".JText::_('category')."'" : "0'";
			$select = "AS '" . JText::_('category') . "'";
			$label1 = JText::_('category');
			break;
		case 'PR':
			$groupby = '`pr`.`description`';
			//$select = $report->type == '1' ? "AS '".JText::_('priority')."'" : "'0'";
			$select = "AS '" . JText::_('priority') . "'";
			$label1 = JText::_('priority');
			break;
		case 'SO':
			$groupby = '`t`.`source`';
			//$select = $report->type == '1' ? "AS '".JText::_('source')."'" : "'0'";
			$select = "AS '" . JText::_('source') . "'";
			$label1 = JText::_('source');
			break;
		case 'MO':
			if ($report->type == '1') {
				$groupby = 'month(`t`.`date`)';
				//$select = "AS '".JText::_('month')."'";
			} elseif ($report->type == '2') {
				$groupby = 'month(`ta`.`date`)';
				//$select = "AS '0'";
			}
			$select = "AS '" . JText::_('month') . "'";
			$label1 = JText::_('month');
			break;
		case 'WD':
			$groupby = '`t`.`day_week`';
			//$select = $report->type == '1' ? "AS '".JText::_('weekday')."'" : "'0'";
			$select = "AS '" . JText::_('weekday') . "'";
			$label1 = JText::_('weekday');
			break;
	}

	// Prepares the Select, Group By and Order By
	$select2 = '';
	$label2 = '';
	switch ($report->groupby2)
	{
		case 'WK':
			$groupby.= ', `wk`.`wkdesc`';
			$label2 = JText::_('workgroup');
			break;
		case 'CL':
			$groupby.= ', `cl`.`clientname`';
			$label2 = JText::_('client');
			$where .= ($where == '' ? " WHERE " : " AND ") . "`cl`.`id`>0";
			break;
		case 'ST':
			$groupby.= ', `st`.`description`';
			$label2 = trim(JText::_('status'));
			break;
		case 'YR':
			if ($report->type == '1') {
				$groupby.= ', year(`t`.`date`)';
			} elseif ($report->type == '2') {
				$groupby.= ', year(`ta`.`date`)';
			}
			$label2 = JText::_('year');
			break;
		case 'DY':
			$groupby.= ', substring(`t`.`date`, 1, 10)';
			$label2 = JText::_('date');
			break;
		case 'AS':
			if ($report->type == '1') {
				$groupby.= ", CASE WHEN t.assign_to='' THEN '<i>" . JText::_('icon_onhold') . "</i>' ELSE `as`.`name` END";
			} elseif ($report->type == '2') {
				$groupby.= ", `as`.`name`";
			}
			$label2 = JText::_('support_member');
			break;
		case 'CA':
			$groupby.= ', `ca`.`description`';
			$label2 = JText::_('category');
			break;
		case 'PR':
			$groupby.= ', `pr`.`description`';
			$label2 = JText::_('priority');
			break;
		case 'SO':
			$groupby = ', `t`.`source`';
			$label2 = JText::_('source');
			break;
		case 'MO':
			if ($report->type == '1') {
				$groupby.= ', month(`t`.`date`)';
			} elseif ($report->type == '2') {
				$groupby.= ', month(`ta`.`date`)';
			}
			$label2 = JText::_('month');
			break;
		case 'WD':
			$groupby.= ', `t`.`day_week`';
			$label2 = JText::_('weekday');
			break;
	}

	// Set the report type
	switch ($report->type) {
		case '1':
			$report_type = "COUNT(t.`id`) AS '" . JText::_('tickets') . "'";
			break;
		case '2':
			$report_type = "((SUM(TIME_TO_SEC( REPLACE(ta.timeused,'.', ':') )))+(SUM(TIME_TO_SEC( REPLACE(ta.tickettravel,'.', ':') )))) / 3600 AS '" . JText::_('tickets') . "'";
			$where = 'RIGHT JOIN #__support_ticket_resp AS `ta` ON `t`.`id`=`ta`.`id_ticket`' . $where . ' AND ta.timeused>0';
			break;
	}

	// Based on a custom field
	if (is_numeric($report->groupby) && $report->groupby > 0)
	{
		if (is_numeric($report->groupby2) && $report->groupby2 > 0)
		{
			$sql = "SELECT fv.`newfield` AS value, fv2.`newfield` AS value2, $report_type
					FROM #__support_ticket AS `t`
					INNER JOIN #__support_field_value AS fv2 ON fv2.id_ticket=t.id AND fv2.id_field={$report->groupby2}
					INNER JOIN #__support_field_value AS fv ON fv.id_ticket=t.id AND fv.id_field={$report->groupby}
					INNER JOIN #__support_custom_fields AS `cf` ON `cf`.`id`=`fv`.`id_field`
					LEFT JOIN #__support_workgroup AS `wk` ON `wk`.`id`=`t`.`id_workgroup`
					LEFT JOIN #__support_status AS `st` ON `st`.id=`t`.`id_status`
					LEFT JOIN #__users AS `as` ON `as`.`id`=`t`.`assign_to`
					LEFT JOIN #__support_category AS `ca` ON `ca`.`id`=`t`.`id_category`
					LEFT JOIN #__support_priority AS pr ON `pr`.`id`=`t`.`id_priority`
					LEFT JOIN #__support_client_users AS `cu` ON `cu`.`id_user`=`t`.`id_user`
					LEFT JOIN #__support_client AS `cl` ON `cl`.`id`=`cu`.`id_client`
					$where
					GROUP BY fv.`newfield`, fv2.`newfield`
					ORDER BY fv.`newfield`, fv2.`newfield`";
		}
		else
		{
			$sql = "SELECT fv.`newfield` AS value, $report_type
					FROM #__support_ticket AS `t`
					INNER JOIN #__support_field_value AS fv ON fv.id_ticket=t.id AND fv.id_field={$report->groupby}
					INNER JOIN #__support_custom_fields AS `cf` ON `cf`.`id`=`fv`.`id_field`
					LEFT JOIN #__support_workgroup AS `wk` ON `wk`.`id`=`t`.`id_workgroup`
					LEFT JOIN #__support_status AS `st` ON `st`.id=`t`.`id_status`
					LEFT JOIN #__users AS `as` ON `as`.`id`=`t`.`assign_to`
					LEFT JOIN #__support_category AS `ca` ON `ca`.`id`=`t`.`id_category`
					LEFT JOIN #__support_priority AS pr ON `pr`.`id`=`t`.`id_priority`
					LEFT JOIN #__support_client_users AS `cu` ON `cu`.`id_user`=`t`.`id_user`
					LEFT JOIN #__support_client AS `cl` ON `cl`.`id`=`cu`.`id_client`
					$where
					GROUP BY fv.`newfield`
					ORDER BY fv.`newfield`";
		}
	}
	// Based on a fixed field
	else
	{
		$sql = "SELECT $groupby $select, $report_type
				FROM #__support_ticket AS `t`
				LEFT JOIN #__support_workgroup AS `wk` ON `wk`.`id`=`t`.`id_workgroup`
				LEFT JOIN #__support_status AS `st` ON `st`.id=`t`.`id_status`
				LEFT JOIN #__users AS `as` ON `as`.`id`=`t`.`assign_to`
				LEFT JOIN #__support_category AS `ca` ON `ca`.`id`=`t`.`id_category`
				LEFT JOIN #__support_priority AS pr ON `pr`.`id`=`t`.`id_priority`
				LEFT JOIN #__support_client_users AS `cu` ON `cu`.`id_user`=`t`.`id_user`
				LEFT JOIN #__support_client AS `cl` ON `cl`.`id`=`cu`.`id_client`
				$where
				GROUP BY $groupby
				ORDER BY $groupby";
	}

	$sql = str_replace('#__', $CONFIG->dbprefix, $sql);

	reports_html::show($report, $sql, $lists, $f_year, $f_month, $f_status, $f_priority, $f_category, $f_workgroup, $f_client, $f_user, $f_staff, $sub_os, $label1, $label2);
}

function UnLockExport($id)
{
	$database = JFactory::getDBO();

	$locked_tickets_id_list = '';
	$locked_tickets_num_list = '';

	// Build list of affected ticket IDs
	$database->setQuery("SELECT id FROM #__support_ticket WHERE id_export='" . $id . "'");
	$locked_tickets_ids = $database->loadObjectList();
	if ($database->getErrorMsg() != '') {
		HelpdeskUtility::ShowSCMessage('<b>' . JText::_('error_compile_ids') . '</b><br /> ' . $database->getErrorMsg(), 'e');
	}

	// Add a ticket log message and build list of ticket IDs
	for ($i = 0; $i < count($locked_tickets_ids); $i++) {
		$locked_ticket_id = $locked_tickets_ids[$i];
		$locked_tickets_id_list .= $locked_ticket_id->id . ',';
		HelpdeskTicket::ticketLog($locked_ticket_id->id, JText::_('export_unlocked_message') . ' (' . $id . ')', JText::_('export_unlocked_message'), '');
	}
	// Remove extra , at the end of the Ticket IDs list for SQL query
	$locked_tickets_id_list = JString::substr($locked_tickets_id_list, 0, strlen($locked_tickets_id_list) - 1);

	// Build list of affected ticket numbers for pop up message
	$database->setQuery("SELECT ticketmask FROM #__support_ticket WHERE id_export='" . $id . "'");
	$locked_tickets_nums = $database->loadObjectList();
	if ($database->getErrorMsg() != '') {
		HelpdeskUtility::ShowSCMessage('<b>' . JText::_('error_compile_numbers') . '</b><br /> ' . $database->getErrorMsg(), 'e');
	}
	for ($i = 0; $i < count($locked_tickets_nums); $i++) {
		$locked_ticket_num = $locked_tickets_nums[$i];
		$locked_tickets_num_list .= $locked_ticket_num->ticketmask . ', ';
	}
	// Remove extra , at the end of the Ticket Nums list for SQL query
	$locked_tickets_num_list = JString::substr($locked_tickets_num_list, 0, strlen($locked_tickets_num_list) - 2);

	if (strlen($locked_tickets_id_list) > 0) {
		$database->setQuery("UPDATE #__support_ticket SET id_export='0' WHERE id IN (" . $database->quote($locked_tickets_id_list) . ")");
		if ($database->query()) {
			HelpdeskUtility::ShowSCMessage('<b>' . JText::_('ticket_unlocked') . ':</b><br /> ' . $locked_tickets_num_list, 'i');
		} else {
			HelpdeskUtility::ShowSCMessage('<b>' . JText::_('error_unlocking') . '</b><br /> ' . $database->getErrorMsg(), 'e');
		}
	}
	ExportTickets();
}

function DeleteExport($id)
{
	$database = JFactory::getDBO();

	$database->setQuery("DELETE FROM #__support_export WHERE id='" . $id . "'");
	if ($database->query() && $database->getAffectedRows() > 0) {
		HelpdeskUtility::ShowSCMessage(JText::_('export_history_id') . ': ' . $id . ' was deleted successfully.');
	} else {
		HelpdeskUtility::ShowSCMessage(JText::_('error_delete_export_id') . ' ' . $id . '.<br /> ' . $database->getErrorMsg(), 'e');
	}

	ExportTickets();
}

function ExportTickets()
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/export_step1.php";

	// Get the default export profile and its values
	$database->setQuery("SELECT `id` AS export_id, `filter_statusid` AS status_id, `filter_wkid` AS wk_id, `filter_clientid` AS client_id, `filter_userid` AS user_id FROM #__support_export_profile WHERE isdefault='1' LIMIT 1");
	$default_export = $database->loadObject();

	if (isset($default_export)) {
		$default_export_id = $default_export->export_id;
		$default_export_status = $default_export->status_id;
		$default_export_wk = $default_export->wk_id;
		$default_export_client = $default_export->client_id;
		$default_export_user = $default_export->user_id;
	} else {
		$default_export_id = 0;
		$default_export_status = 0;
		$default_export_wk = 0;
		$default_export_client = 0;
		$default_export_user = 0;
	}


	// Build Export Profiles select list
	$sql = "SELECT `id` AS value, `name` AS text FROM #__support_export_profile ORDER BY description";
	$database->setQuery($sql);
	$rows_profiles = $database->loadObjectList();
	if ($default_export_id == 0) {
		$rows_profiles = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_profiles);
	}
	$lists['profile'] = JHTML::_('select.genericlist', $rows_profiles, 'export_profile_id', 'class="inputbox" size="1" onChange="SelectRecords();"', 'value', 'text', $default_export_id);

	// Build Export Profiles information
	$sql = "SELECT id, filter_statusid as status, filter_wkid as workgroup, filter_clientid as client, filter_userid as user FROM #__support_export_profile ORDER BY id";
	$database->setQuery($sql);
	$lists['profiles'] = $database->loadObjectList();

	// Build List of Available Statuses
	$database->setQuery("SELECT `id` FROM #__support_status WHERE status_group='C'");
	$default_status = $database->loadResult();
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY status_group";
	$database->setQuery($sql);
	$rows_statuses = $database->loadObjectList();
	$rows_statuses = array_merge(array(JHTML::_('select.option', 'O', JText::_('all_open'))), $rows_statuses);
	$rows_statuses = array_merge(array(JHTML::_('select.option', 'C', JText::_('all_closed'))), $rows_statuses);
	$rows_statuses = array_merge(array(JHTML::_('select.option', '0', JText::_('all_status'))), $rows_statuses);
	$lists['statuses'] = JHTML::_('select.genericlist', $rows_statuses, 'id_export_statuses', 'class="inputbox" size="1"', 'value', 'text', $default_export_status);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'selwk', 'class="inputbox" size="1"', 'value', 'text', $default_export_wk);

	// Get all previous exports
	$database->setQuery("SELECT * FROM `#__support_export` ORDER BY export_date DESC");
	$exports = $database->loadObjectList();

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_clients = $database->loadObjectList();
	$rows_clients = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_clients);
	$lists['client'] = JHTML::_('select.genericlist', $rows_clients, 'client', 'class="inputbox" size="1" onchange="changeDynaList(\'id_user\', ordersOS, document.adminForm.client.options[document.adminForm.client.selectedIndex].value, originalPos, originalOrderOS);"', 'value', 'text', $default_export_client);

	$sub_os = array();

	for ($i = 0, $n = count($rows_clients); $i < $n; $i++) {
		$client_row = &$rows_clients[$i];
		$sub_os[0][] = JHTML::_('select.option', $client_row->value, JText::_('all_users'));
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client FROM #__users u, #__support_client_users c WHERE c.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . " ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	}

	// Build list of file format
	$rows_files = array(JHTML::_('select.option', 'CSV', 'CSV'));
	$rows_files = array_merge(array(JHTML::_('select.option', 'XML', 'XML ')), $rows_files);
	$lists['fileformat'] = JHTML::_('select.genericlist', $rows_files, 'fileformat', 'class="inputbox" size="1"', 'value', 'text', 'CSV');

	$year = '';
	$month = '';
	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" ', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" ', 'value', 'text', $month);

	reports_html::show($lists, $exports, $sub_os);
}

function Analysis($analyze, $year, $month, $id_workgroup, $id_client, $id_user, $print)
{
	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/analysis.php";

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = HelpdeskDate::MinYear(); $i <= (int) HelpdeskDate::DateOffset("%Y"); $i++)
	{
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '-1', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="' . ($analyze == 'T' ? '' : 'changeDynaList(\'id_user\', ordersOS, document.adminForm.client.options[document.adminForm.client.selectedIndex].value, originalPos, originalOrderOS); ') . 'document.adminForm.submit();"', 'value', 'text', $id_client);

	$sub_os = array();

	for ($i = 0, $n = count($rows_wk); $i < $n; $i++) {
		$wkrow = &$rows_wk[$i];
		$sub_os[0][] = JHTML::_('select.option', $wkrow->value, JText::_('all_users'));
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client FROM #__users u, #__support_client_users c WHERE c.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . " ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	}

	// Get the list of other support staff for the assignment selection list
	$assignlist[] = JHTML::_('select.option', '0', JText::_('all_staff'));
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission as p, #__users as u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	for ($staff = 0; $staff < count($rows_staff); $staff++) {
		$row_staff = $rows_staff[$staff];
		$assignlist[] = JHTML::_('select.option', $row_staff->value, $row_staff->text);
	}
	$lists['assign'] = JHTML::_('select.genericlist', $assignlist, 'id_user', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_user);

	// W -> Workgroups
	// C -> Clients
	// S -> Support Staff
	// T -> Timesheet
	reports_html::show($analyze, $year, $month, $id_workgroup, $id_client, $id_user, $lists, $print, $sub_os);
}

function Duedate($id_workgroup, $client, $print)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/duedate.php";

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client);

	reports_html::show($id_workgroup, $client, $lists, $print);
}

function ClientMonth($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $print)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/client_month.php";

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_status'))), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'f_status', 'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $f_status);

	$showcustomfields[] = JHTML::_('select.option', '0', JText::_('hide_custom_field'));
	$showcustomfields[] = JHTML::_('select.option', '1', JText::_('show_custom_field'));
	$lists['showcustomfields'] = JHTML::_('select.genericlist', $showcustomfields, 'f_customfields', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $f_customfields);

	reports_html::show($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $lists, $print);
}

function ClientMonthdetail($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $print)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/client_month_detail.php";

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_status'))), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'f_status', 'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $f_status);

	$showcustomfields[] = JHTML::_('select.option', '0', JText::_('hide_custom_field'));
	$showcustomfields[] = JHTML::_('select.option', '1', JText::_('show_custom_field'));
	$lists['showcustomfields'] = JHTML::_('select.genericlist', $showcustomfields, 'f_customfields', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $f_customfields);

	reports_html::show($year, $month, $id_workgroup, $client, $f_status, $f_customfields, $lists, $print);
}

function StatusReport($year, $month, $id_workgroup, $client, $f_status, $f_staff, $print)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/status_report.php";

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_status'))), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'f_status', 'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $f_status);

	// Get the list of other support staff for the assignment selection list
	$assignlist[] = JHTML::_('select.option', '0', JText::_('all_staff'));
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission as p, #__users as u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	for ($staff = 0; $staff < count($rows_staff); $staff++) {
		$row_staff = $rows_staff[$staff];
		$assignlist[] = JHTML::_('select.option', $row_staff->value, $row_staff->text);
	}
	$lists['assign'] = JHTML::_('select.genericlist', $assignlist, 'f_staff', 'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $f_staff);

	reports_html::show($year, $month, $id_workgroup, $client, $f_status, $f_staff, $lists, $print);
}

function TicketMonth($year, $month, $id_workgroup, $client, $print)
{
	$database = JFactory::getDBO();

	// HTML dependency
	require_once "components/com_maqmahelpdesk/views/reports/tmpl/ticket_month.php";

	$years[] = JHTML::_('select.option', '00', JText::_('selectlist'));
	for ($i = 2004; $i <= HelpdeskDate::DateOffset("%Y"); $i++) {
		$years[] = JHTML::_('select.option', $i, $i);
	}
	$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $year);

	$months[] = JHTML::_('select.option', '00', JText::_('selectlist'));
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
	$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $month);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '', JText::_('all_wks'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client);

	reports_html::show($year, $month, $id_workgroup, $client, $lists, $print);
}
