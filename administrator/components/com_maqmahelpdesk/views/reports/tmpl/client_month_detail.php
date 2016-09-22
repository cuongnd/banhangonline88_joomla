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
	static function show($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, $lists, $print)
	{
		$database = JFactory::getDBO();
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	    <form id="adminForm" name="adminForm" action="index.php">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="task" value="reports"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="report" value="clientmdetail"/>
	        <table class="adminheading" border="0">
	            <tr>
					<?php		if (!$print) { ?>
	                <td><?php echo $lists['workgroup']; ?></td>
	                <td><?php echo $lists['client']; ?></td>
	                <td><?php echo $lists['status']; ?></td>
	                <td><?php echo $lists['month']; ?></td>
	                <td><?php echo $lists['year']; ?></td>
	                <td><?php echo $lists['showcustomfields']; ?></td>
					<?php } ?>
	            </tr>
	        </table>
	        <table class="admintable">
	            <tr>
	                <td>
	                    <table width="100%"><?php
							$reporting = new SupportReports();
							echo $reporting->ClientTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, 1); ?>
	                    </table>
	                </td>
	            </tr>
	        </table>
	    </form>
		<?php
	}
}
