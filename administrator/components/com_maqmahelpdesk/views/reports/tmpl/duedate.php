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

/**
 * @package MaQma Helpdesk
 */
class reports_html
{
	static function show($id_workgroup, $id_client, $lists, $print)
	{
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	    <form id="adminForm" name="adminForm" action="index.php">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="task" value="reports"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="report" value="duedate"/>
	        <table class="adminheading" border="0">
	            <tr>
	                <!--th class="reports">< ?php echo JText::_('duedate_report'); ?></th-->
					<?php		if (!$print) { ?>
	                <td><?php echo $lists['workgroup']; ?></td>
	                <td><?php echo $lists['client']; ?></td>
					<?php } ?>
	            </tr>
	        </table>
	        <table class="adminform">
	            <tr>
	                <td>
	                    <table width="100%"><?php
							$reporting = new SupportReports();
							echo $reporting->DueDates($id_workgroup, $id_client); ?>
	                    </table>
	                </td>
	            </tr>
	        </table>
	    </form><?php
	}
}
