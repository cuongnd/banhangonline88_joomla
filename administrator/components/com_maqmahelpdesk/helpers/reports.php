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

class HelpdeskReportAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @param   string  $task  Action to execute.
	 *
	 * @return  void
	 */
	static public function addToolbar($task)
	{
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$report = JRequest::getVar('report', '', '', 'string');
		$id_client = JRequest::getVar('client', 0, '', 'int');
		$f_status = JRequest::getVar('f_status', 0, '', 'int');
		$f_customfields = JRequest::getVar('f_customfields', 1, '', 'int');
		$year = JRequest::getVar('year', HelpdeskDate::DateOffset("%Y"), '', 'string');
		$month = JRequest::getVar('month', HelpdeskDate::DateOffset("%m"), '', 'string');
		$month = ((int)$month < 10 && $month != '00' ? '0' . (int)$month : $month);

		switch ($report)
		{
			case 'wkanalysis':
				JToolBarHelper::title(JText::_('wk_analysis'), 'sc-reports');
				self::setDocument(JText::_('wk_analysis'));
				break;

			case 'clientanalysis':
				JToolBarHelper::title(JText::_('client_analysis'), 'sc-reports');
				self::setDocument(JText::_('client_analysis'));
				break;

			case 'supportanalysis':
				JToolBarHelper::title(JText::_('support_analysis'), 'sc-reports');
				self::setDocument(JText::_('support_analysis'));
				break;

			case 'timesheets':
				JToolBarHelper::title(JText::_('timesheet'), 'sc-reports');
				self::setDocument(JText::_('timesheet'));
				break;

			case 'timesheetd':
				JToolBarHelper::title(JText::_('timesheetd'), 'sc-reports');
				self::setDocument(JText::_('timesheetd'));
				break;

			case 'clientm':
				JToolBarHelper::title(JText::_('client_report_t'), 'sc-reports');
				self::setDocument(JText::_('client_report_t'));
				MaQmaToolBarHelper::Preview('index.php?task=reports&option=com_maqmahelpdesk&report=clientm&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&client=' . $id_client . '&f_status=' . $f_status . '&f_customfields=' . $f_customfields . '&print=1&tmpl=component');
				MaQmaToolBarHelper::exportCsv(JURI::root() . 'administrator/index.php?option=com_maqmahelpdesk&task=ajax_csvclientm&format=raw&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&client=' . $id_client . '&f_status=' . $f_status . '&f_customfields=' . $f_customfields);
				MaQmaToolBarHelper::exportPdf(JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_reportclient&format=raw&report=clientm&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&client=' . $id_client . '&f_status=' . $f_status . '&f_customfields=' . $f_customfields);
				break;

			case 'duedate':
				JToolBarHelper::title(JText::_('duedate_report'), 'sc-reports');
				self::setDocument(JText::_('duedate_report'));
				break;

			case 'ticketsexport':
				JToolBarHelper::title(JText::_('export_data'), 'sc-reports');
				self::setDocument(JText::_('export_data'));
				break;

			case 'clientmdetail':
				JToolBarHelper::title(JText::_('client_report_detail'), 'sc-reports');
				self::setDocument(JText::_('client_report_detail'));
				MaQmaToolBarHelper::Preview('index.php?task=reports&option=com_maqmahelpdesk&report=clientmdetail&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&id_client=' . $id_client . '&f_status=' . $f_status . '&f_customfields=' . $f_customfields . '&detail=1&print=1&component=tmpl');
				MaQmaToolBarHelper::exportPdf(JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_reportclient&format=raw&report=clientmdetail&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&id_client=' . $id_client . '&f_status=' . $f_status . '&f_customfields=' . $f_customfields . '&detail=1');
				break;

			case 'status':
				JToolBarHelper::title(JText::_('status_report2'), 'sc-reports');
				self::setDocument(JText::_('status_report2'));
				MaQmaToolBarHelper::Preview('index.php?task=reports&option=com_maqmahelpdesk&report=status&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&id_client=' . $id_client . '&f_status=' . $f_status . '&print=1&tmpl=component');
				MaQmaToolBarHelper::exportPdf(JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_reportstatus&format=raw&report=status&id_workgroup=' . $id_workgroup . '&month=' . $month . '&year=' . $year . '&id_client=' . $id_client . '&f_status=' . $f_status);
				break;

			case 'ratings':
				JToolBarHelper::title(JText::_('RATINGS_REPORT'), 'sc-reports');
				self::setDocument(JText::_('RATINGS_REPORT'));
				break;

			case 'geo':
				JToolBarHelper::title(JText::_('REPORTS_GEO'), 'sc-reports');
				self::setDocument(JText::_('REPORTS_GEO'));
				break;

			default:
				JToolBarHelper::title(JText::_('wk_analysis'), 'sc-reports');
				self::setDocument(JText::_('wk_analysis'));
				break;
		}

		if ($report == 'ticketsexport')
		{
			JToolBarHelper::custom('', 'save_f2', 'save_f2', JText::_('export'), false);
		}

		if ($report == 'wkanalysis' || $report == 'clientanalysis' || $report == 'supportanalysis' || $report == 'timesheets' || $report == 'timesheetd' || $report == 'duedate')
		{
			$month = JRequest::getVar('month', date("m"), '', 'int');
			$year = JRequest::getVar('year', date("Y"), '', 'int');
			$id_user = JRequest::getVar('id_user', 0, '', 'int');
			$client = JRequest::getVar('client', 0, '', 'int');
			$wk = JRequest::getVar('id_workgroup', 0, '', 'int');
			MaQmaToolBarHelper::Preview('index.php?option=com_maqmahelpdesk&task=reports&report=' . $report . '&id_workgroup=' . $wk . '&year=' . $year . '&month=' . $month . '&id_user=' . $id_user . '&client=' . $client . '&print=1&tmpl=component');
		}
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument($title)
	{
		$document = JFactory::getDocument();
		$document->setTitle($title);
	}

	/**
	 * Returns the number of tickets for a given day by type (opened or closed)
	 *
	 *
	 *
	 * @return int
	 */
	static public function getTicketsByDay($type, $date)
	{
		$database = JFactory::getDBO();

		// If it's created use the DATE field, if it's closed use the LAST_UPDATE field
		$field = ($type == 'O' ? '`date`' : '`last_update`');

		$sql = "SELECT COUNT(*)
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
				WHERE SUBSTRING($field, 1, 10)='" . $date . "' " . ($type == 'O' ? '' : "AND s.`status_group`='" . $type . "'");
		$database->setQuery($sql);

		return (int) $database->loadResult();
	}

	static public function getTicketsByMonth($type, $month, $year)
	{
		$database = JFactory::getDBO();

		// If it's created use the DATE field, if it's closed use the LAST_UPDATE field
		$field = ($type == 'O' ? '`date`' : '`last_update`');

		$sql = "SELECT COUNT(*)
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
				WHERE MONTH($field)='" . $month . "'
				  AND YEAR($field)='" . $year . "' " .
				  ($type == 'O' ? '' : "AND s.`status_group`='" . $type . "'");
		$database->setQuery($sql);

		return (int) $database->loadResult();
	}

	static public function getTicketsByYear($type, $year)
	{
		$database = JFactory::getDBO();

		// If it's created use the DATE field, if it's closed use the LAST_UPDATE field
		$field = ($type == 'O' ? '`date`' : '`last_update`');

		$sql = "SELECT COUNT(*)
				FROM `#__support_ticket` AS t
					 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
				WHERE YEAR($field)='" . $year . "' " .
				  ($type == 'O' ? '' : "AND s.`status_group`='" . $type . "'");
		$database->setQuery($sql);
		return (int) $database->loadResult();
	}

	static public function getRatingsFromDay($year, $month, $day, $id_workgroup)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT COUNT(*)
				FROM `#__support_rate` AS r
					 INNER JOIN `#__support_ticket` AS t ON t.`id`=r.`id_table`
				WHERE YEAR(r.`date`)='" . $year . "'
				  AND MONTH(r.`date`)='" . $month . "'
				  AND DAY(r.`date`)='" . $day . "'
				  AND r.`source`='T' " .
				  ($id_workgroup ? "AND t.`id_workgroup`=" . (int) $id_workgroup : "");
		$database->setQuery($sql);

		return (int) $database->loadResult();
	}

	static public function getRatingsByRate($rate, $year, $month, $id_workgroup)
	{
		$database = JFactory::getDBO();

		$where_rates = null;
		if ($year)
		{
			$where_rates[] = "YEAR(r.`date`)='" . $year . "'";
		}
		if ($month)
		{
			$where_rates[] = "MONTH(r.`date`)='" . $month . "'";
		}
		if ($id_workgroup)
		{
			$where_rates[] = "t.`id_workgroup`=" . (int) $id_workgroup;
		}

		$sql = "SELECT COUNT(*) AS total
			FROM `#__support_rate` AS r
				 INNER JOIN `#__support_ticket` AS t ON t.`id`=r.`id_table`
			WHERE r.`source`='T'
			  AND r.`rate`=" . $rate .
			  (is_array($where_rates) && count($where_rates) ? " AND ".implode(" AND ", $where_rates) : "");
		$database->setQuery($sql);

		return (int) $database->loadResult();
	}
}