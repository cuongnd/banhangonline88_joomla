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

defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>

<div id="daily_chart" style="width:100%;height:350px;"></div>

<script type="text/javascript">
    google.load('visualization', '1', {packages: ['annotatedtimeline']});

    function drawVisualization()
    {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', '<?php echo JText::_("TICKETS_CREATED");?>');
        data.addColumn('number', '<?php echo JText::_("TICKETS_CLOSED");?>');
        data.addRows([
			<?php for ($i = 30; $i >= 0; $i--):?>
            [new Date(<?php echo date("Y", mktime(0, 0, 0, date("m"), date("d") - $i, date("Y")));?>, <?php echo (date("m", mktime(0, 0, 0, date("m"), date("d") - $i, date("Y")))-1);?>, <?php echo date("d", mktime(0, 0, 0, date("m"), date("d") - $i, date("Y")));?>), <?php echo HelpdeskReportAdminHelper::getTicketsByDay('O', date("Y-m-d", mktime(0, 0, 0, date("m"),date("d") - $i, date("Y"))));?>, <?php echo HelpdeskReportAdminHelper::getTicketsByDay('C', date("Y-m-d", mktime(0, 0, 0, date("m"),date("d") - $i, date("Y"))));?>],
	        <?php endfor;?>
        ]);

        var annotatedtimeline = new google.visualization.AnnotatedTimeLine(document.getElementById('daily_chart'));
        annotatedtimeline.draw(data, {
	        'thickness':2,
	        'displayAnnotations': false,
	        'colors': ['#FF3300', '#009900'],
            'zoomStartTime': new Date(<?php echo date("Y", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));?>, <?php echo (date("m", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")))-1);?>, <?php echo date("d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));?>),
            'zoomEndTime': new Date(<?php echo date("Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));?>, <?php echo (date("m", mktime(0, 0, 0, date("m"), date("d"), date("Y")))-1);?>, <?php echo date("d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));?>)
        });
    }

    google.setOnLoadCallback(drawVisualization);
</script>