<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(empty($this->data->bouncedetails)) return; ?>
<script language="JavaScript" type="text/javascript">
	function drawChart() {
		var dataTable = new google.visualization.DataTable();
		dataTable.addColumn('string');
		dataTable.addColumn('number');
		dataTable.addRows(<?php echo count($this->data->bouncedetails); ?>);

	<?php
	$i = 0;
	foreach($this->data->bouncedetails as $oneRule => $total ){ ?>
			dataTable.setValue(<?php echo $i ?>, 0, '<?php echo addslashes($oneRule).' ('.$total.')'; ?>');
			dataTable.setValue(<?php echo $i ?>, 1, <?php echo intval($total); ?>);
	<?php 	$i++;
	} ?>

			var vis = new google.visualization.PieChart(document.getElementById('bouncechart'));
					var options = {
						width: '100%',
						height: 400,
						is3D:true,
						legendTextStyle: {color:'#333333'},
						legend:'right'
					};
					vis.draw(dataTable, options);
			}
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
</script>
<div id="acy_content">
	<fieldset class="acyheaderarea">
		<div class="toolbar" id="toolbar" style="float: right;">
			<table><tr>
			<td><a href="<?php echo acymailing_completeLink('bounces&task=chart&export=1&mailid='.JRequest::getInt('mailid'),true); ?>" ><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT',true); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td>
			<td><a onclick="window.print(); return false;" href="#" ><span class="icon-32-acyprint" title="<?php echo JText::_('ACY_PRINT',true); ?>"></span><?php echo JText::_('ACY_PRINT'); ?></a></td>
			</tr></table>
		</div>
	</fieldset>
	<div id="iframedoc"></div>

	<div id="bouncechart"></div>

	<table id="bouncelist" class="adminlist table table-striped">
	<?php

	foreach($this->data->bouncedetails as $oneRule => $total ){
		echo '<tr><td>'.$total.'</td><td>'.$oneRule.'</td></tr>';
	}
	?>
	</table>

</div>
