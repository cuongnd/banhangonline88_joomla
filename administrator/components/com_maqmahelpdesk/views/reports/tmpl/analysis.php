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
	static function show($analyze, $year, $month, $id_workgroup, $id_client, $id_user, $lists, $print, $sub_os)
	{
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php');

		// set the title
		switch ($analyze) {
			case 'W':
				$title = JText::_('wk_analysis');
				$report = 'wkanalysis';
				break;
			case 'C':
				$title = JText::_('client_analysis');
				$report = 'clientanalysis';
				break;
			case 'S':
				$title = JText::_('support_analysis');
				$report = 'supportanalysis';
				break;
			case 'TS':
				$title = JText::_('timesheet');
				$report = 'timesheets';
				break;
			case 'TD':
				$title = JText::_('timesheet');
				$report = 'timesheetd';
				break;
		} ?>

	    <script language="javascript" text="text/javascript">
	        <!--
	        var originalOrderOS = '<?php echo $id_user; ?>';
	        var originalPos = '<?php echo $id_client; ?>';

	        var ordersOS = new Array();
				<?php
				$i = 0;
				foreach ($sub_os as $k => $items) {
					foreach ($items as $v) {
						echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
					}
				} ?>
	        //-->
	    </script>

	    <form id="adminForm" name="adminForm" action="index.php">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="task" value="reports"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="report" value="<?php echo $report; ?>"/>
	        <table class="adminheading" border="0">
	            <tr>
	                <!--th class="reports">< ?php echo $title; ?></th-->
					<?php		if (!$print) {
					if ($analyze == 'C' || $analyze == 'TS' || $analyze == 'TD') {
						?>
	                    <td><?php echo $lists['client']; ?></td>
	                    <td>
	                        <script language="javascript" type="text/javascript">
	                            <!--
	                            writeDynaList('class="inputbox" name="id_user" size="1" onChange="document.adminForm.submit();"', ordersOS, originalPos, originalPos, originalOrderOS);
	                            //-->
	                        </script>
	                    </td>
						<?php } elseif ($analyze == 'S') { ?>
	                    <td><?php echo $lists['assign']; ?></td>
						<?php } ?>
	                <td><?php echo $lists['workgroup']; ?></td>
	                <td><?php echo $lists['month']; ?></td>
	                <td><?php echo $lists['year']; ?></td>
					<?php } ?>
	            </tr>
	        </table>
	        <table class="admintable" cellspacing="1" width="100%">
	            <tr>
	                <td>
	                    <table width="100%">
							<?php			$reporting = new SupportReports();
							switch ($analyze) {
								case 'W':
									echo $reporting->WorkgroupAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
									break;

								case 'S':
									echo $reporting->StaffAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
									break;

								case 'C':
									echo $reporting->ClientAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
									break;

								case 'TS':
									echo $reporting->Timesheet($year, $month, $id_workgroup, $id_client, 'S');
									break;

								case 'TD':
									echo $reporting->Timesheet($year, $month, $id_workgroup, $id_client, 'D');
									break;
							} ?>
	                    </table>
	                </td>
	            </tr>
	        </table>
	    </form><?php
	}
}
