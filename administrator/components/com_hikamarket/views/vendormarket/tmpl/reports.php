<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(HIKASHOP_BACK_RESPONSIVE) {
?>
<div class="row-fluid">
	<div class="span4">
<?php
} else {
?>
<table class="hikam_blocks" style="width:100%">
	<tr>
		<td style="width:35%;vertical-align:top">
<?php
}

foreach($this->statistic_slots[0] as $stat) {
	$key = $stat['key'];
?>
		<div class="hikamarket_panel hikamarket_panel_stats">
			<div class="hikamarket_panel_heading"><?php echo $stat['label']; ?></div>
			<div id="hikamarket_dashboard_stat_<?php echo $key; ?>" class="hikamarket_panel_body"><?php
				echo $this->statisticsClass->display($stat);
			?></div>
		</div>
<?php
	}

if(HIKASHOP_BACK_RESPONSIVE) {
?>
	</div>
	<div class="span8">
<?php
} else {
?>
		</td>
		<td style="width:65%;vertical-align:top">
<?php
}

foreach($this->statistic_slots[1] as $stat) {
	$key = $stat['key'];
?>
		<div class="hikamarket_panel hikamarket_panel_stats">
			<div class="hikamarket_panel_heading"><?php echo $stat['label']; ?></div>
			<div id="hikamarket_dashboard_stat_<?php echo $key; ?>" class="hikamarket_panel_body"><?php
				echo $this->statisticsClass->display($stat);
			?></div>
		</div>
<?php
	}

if(HIKASHOP_BACK_RESPONSIVE) {
?>
	</div>
</div>
<?php
} else {
?>
		</td>
	</tr>
</table>
<?php
}
