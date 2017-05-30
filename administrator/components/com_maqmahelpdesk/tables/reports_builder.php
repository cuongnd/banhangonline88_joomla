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

class MaQmaHelpdeskTableReportBuilder extends JTable
{
	var $id = null;
	var $title = null;
	var $description = null;
	var $f_workgroup = 0;
	var $f_category = 0;
	var $f_client = 0;
	var $f_user = 0;
	var $f_year = null;
	var $f_month = null;
	var $f_priority = 0;
	var $f_status = 0;
	var $f_source = null;
	var $f_staff = 0;
	var $groupby = null;
	var $groupby2 = null;
	var $report_type = null;
	var $chart_type = null;
	var $sf_workgroup = 0;
	var $sf_category = 0;
	var $sf_client = 0;
	var $sf_user = 0;
	var $sf_year = 0;
	var $sf_month = 0;
	var $sf_priority = 0;
	var $sf_status = 0;
	var $sf_source = 0;
	var $sf_staff = 0;
	var $chart_width = 300;
	var $chart_height = 300;
	var $layout = 1;
	var $type = 1;
	var $chart_percentage = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_reports', 'id', $_db);
	}
}