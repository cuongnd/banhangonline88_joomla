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

<div id="status_chart" style="width:100%;height:420px;"></div>

<script type="text/javascript">
    google.load('visualization', '1', {packages: ['corechart']});

    function drawVisualization()
    {
        var data = google.visualization.arrayToDataTable([
            ['Status', 'Tickets'],
            <?php for ($i = 0; $i < count($status); $i++):?>
	        ['<?php echo $status[$i]->description;?>', <?php echo $status[$i]->tickets;?>],
            <?php endfor;?>
        ]);

        new google.visualization.PieChart(document.getElementById('status_chart')).draw(data,{
	        'is3D':true,
	        'legend.position':'bottom'
        });
    }

    google.setOnLoadCallback(drawVisualization);
</script>
