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

class HelpdeskReportBuilderAdminHelper
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
		global $title_showReport;

		switch ($task)
		{
			case "buildernew":
			case "builderedit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-reports');
				JToolBarHelper::save('reports_buildersave');
				JToolBarHelper::cancel('reports_builder');
				break;

			case "builder":
				JToolBarHelper::title(JText::_('reports_manager'), 'sc-reports');
				JToolBarHelper::addNew('reports_buildernew', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('reports_builderedit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'reports_builderremove', 'JTOOLBAR_DELETE');
				break;

			case "builderreport":
				JToolBarHelper::title($title_showReport, 'sc-reports');
				$id = JRequest::getVar('id', 0, '', 'int');
				$year = JRequest::getVar('year', date("Y"), '', 'int');
				$month = JRequest::getVar('month', date("m"), '', 'int');
				$id_workgroup = JRequest::getInt('id_workgroup', 0);
				$client = JRequest::getVar('client', 0, '', 'int');
				$id_user = JRequest::getVar('id_user', 0, '', 'int');
				$f_year = JRequest::getVar('f_year', '', '', 'string');
				$f_month = JRequest::getVar('f_month', '', '', 'string');
				$f_status = JRequest::getVar('f_status', 0, '', 'int');
				$f_priority = JRequest::getVar('f_priority', 0, '', 'int');
				$f_category = JRequest::getVar('f_category', 0, '', 'int');
				$f_workgroup = JRequest::getVar('f_workgroup', 0, '', 'int');
				$f_client = JRequest::getVar('f_client', 0, '', 'int');
				$f_user = JRequest::getVar('f_user', 0, '', 'int');
				$f_staff = JRequest::getVar('f_staff', 0, '', 'int');
				$f_source = JRequest::getVar('f_source', '', '', 'string');
				MaQmaToolBarHelper::Preview('index.php?option=com_maqmahelpdesk&task=reports_builderreport&id=' . $id . '&year=' . $year . '&month=' . $month . '&id_workgroup=' . $id_workgroup . '&client=' . $client . '&id_user=' . $id_user . '&f_year=' . $f_year . '&f_month=' . $f_month . '&f_status=' . $f_status . '&f_priority=' . $f_priority . '&f_category=' . $f_category . '&f_workgroup=' . $f_workgroup . '&f_client=' . $f_client . '&f_user=' . $f_user . '&f_staff=' . $f_staff . '&f_source=' . $f_source . '&print=1&tmpl=component');
				break;
		}
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('reports_manager'));
	}
}