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
	static function show($overview, $agents, $lists, $year, $month, $id_workgroup)
	{
		// Get config
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm">
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <span><?php echo JText::_('REPORTS'); ?></span>
	            <span><?php echo JText::_('RATINGS_REPORT'); ?></span>
	        </div>
	        <div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png" style="padding:5px;" align="absmiddle"/>
				<?php echo $lists['year'] . '&nbsp;' . $lists['month'] . '&nbsp;' . $lists['department']; ?>&nbsp;
	            <a href="javascript:;"
	               class="btn btn-success"
	               onclick="document.adminForm.submit();"><?php echo JText::_('filter');?></a>
	        </div>
	        <div class="contentarea">
	            <div class="row-fluid">
	                <div class="span12">
	                    <h4 style="text-align:center;"><?php echo JText::_("RATINGS_LAST_30_DAYS");?></h4>
	                    <div id="days_chart"></div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span12">
	                    <h4 style="text-align:center;"><?php echo JText::_("OVERVIEW");?></h4>
	                    <div class="row-fluid">
	                        <div class="span4">
	                            <table class="table table-striped table-bordered" cellspacing="0" style="margin-bottom:0;">
	                                <tbody>
	                                <tr>
	                                    <td style="width:75px;text-align:right;font-size:16px;font-weight:bold;"><?php echo $overview['ratings'];?></td>
	                                    <td class="valgmdl"><?php echo JText::_("RATINGS_REPORT");?></td>
	                                </tr>
	                                </tbody>
	                            </table>
	                        </div>
	                        <div class="span4">
	                            <table class="table table-striped table-bordered" cellspacing="0" style="margin-bottom:0;">
	                                <tbody>
	                                <tr>
	                                    <td style="width:75px;text-align:right;font-size:16px;font-weight:bold;"><?php echo $overview['all_time_ratings'];?></td>
	                                    <td class="valgmdl"><?php echo JText::_("RATINGS_ALL_TIME");?></td>
	                                </tr>
	                                </tbody>
	                            </table>
	                        </div>
	                        <div class="span4">
                                <table class="table table-striped table-bordered" cellspacing="0" style="margin-bottom:0;">
                                    <tbody>
                                    <tr>
                                        <td style="width:75px;text-align:right;font-size:16px;font-weight:bold;"><?php echo number_format($agents[0]->average, 2);?></td>
                                        <td class="valgmdl"><?php echo JText::_("RATINGS_BEST_AGENT_AVG");?></td>
                                    </tr>
                                    </tbody>
                                </table>
	                        </div>
	                    </div>
	                    <div class="row-fluid">
	                        <div class="span4">
	                            <table class="table table-striped table-bordered" cellspacing="0" style="margin-bottom:0;">
	                                <tbody>
	                                <tr>
	                                    <td style="width:75px;text-align:right;font-size:16px;font-weight:bold;"><?php echo $overview['average'];?></td>
	                                    <td class="valgmdl"><?php echo JText::_("RATINGS_AVG");?></td>
	                                </tr>
	                                </tbody>
	                            </table>
	                        </div>
	                        <div class="span4">
	                            <table class="table table-striped table-bordered" cellspacing="0" style="margin-bottom:0;">
	                                <tbody>
	                                <tr>
	                                    <td style="width:75px;text-align:right;font-size:16px;font-weight:bold;"><?php echo $overview['all_time_average'];?></td>
	                                    <td class="valgmdl"><?php echo JText::_("RATINGS_ALL_TIME_AVG");?></td>
	                                </tr>
	                                </tbody>
	                            </table>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
                        <h4 style="text-align:center;"><?php echo JText::_("RATINGS_REPORT");?></h4>
                        <div id="ratings_chart"></div>
	                </div>
	                <div class="span6">
	                    ...
	                </div>
	            </div>
	        </div>
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" id="task" name="task" value="reports"/>
	        <input type="hidden" id="report" name="report" value="ratings"/>
	    </form>

	    <script type="text/javascript">
	        google.load('visualization', '1', {packages: ['corechart']});

	        function drawDailyChart()
	        {
	            var data = google.visualization.arrayToDataTable([
	                ['<?php echo JText::_("DATE");?>', '<?php echo JText::_("RATINGS_REPORT");?>'],
					<?php for($i=30; $i>=1; $i--):?>
	                    ['<?php echo date($supportConfig->dateonly_format, mktime(0,0,0,date("m"),date("d")-$i,date("Y")));?>', <?php echo HelpdeskReportAdminHelper::getRatingsFromDay($year, $month, date("d", mktime(0,0,0,date("m"),date("d")-$i,date("Y"))), $id_workgroup);?>],
						<?php endfor;?>
	            ]);

	            new google.visualization.LineChart(document.getElementById('days_chart')).
	                    draw(data, {
	                        legend: 'none',
	                        curveType: "function",
	                        width: '100%',
	                        height: 400,
	                        vAxis: {maxValue: 10},
	                        fontSize: 10,
                            backgroundColor: 'transparent'
	                    }
	            );
	        }

            function drawRatingChart()
            {
                var data = google.visualization.arrayToDataTable([
                    ['<?php echo JText::_("RATINGS_REPORT");?>', '<?php echo JText::_("VOTES");?>'],
                    ['5',  <?php echo HelpdeskReportAdminHelper::getRatingsByRate('5', $year, $month, $id_workgroup);?>],
                    ['4',  <?php echo HelpdeskReportAdminHelper::getRatingsByRate('4', $year, $month, $id_workgroup);?>],
                    ['3',  <?php echo HelpdeskReportAdminHelper::getRatingsByRate('3', $year, $month, $id_workgroup);?>],
                    ['2',  <?php echo HelpdeskReportAdminHelper::getRatingsByRate('2', $year, $month, $id_workgroup);?>],
                    ['1',  <?php echo HelpdeskReportAdminHelper::getRatingsByRate('1', $year, $month, $id_workgroup);?>]
                ]);

                new google.visualization.BarChart(document.getElementById('ratings_chart')).
                        draw(data,{
	                        width: '100%',
			                legend: 'none',
                            backgroundColor: 'transparent'
		                }
                );
            }

	        google.setOnLoadCallback(drawDailyChart);
	        google.setOnLoadCallback(drawRatingChart);
	    </script><?php
	}
}
