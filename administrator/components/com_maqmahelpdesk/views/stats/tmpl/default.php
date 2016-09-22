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

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');

class HTML_stats
{
	static function showStats(&$downloads, &$pageNav, $lists, $month, $year)
	{
		$database = JFactory::getDBO(); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('reports'); ?></a>
			<a href="javascript:;"><?php echo JText::_('download_stats'); ?></a>
			<span><?php echo JText::_('downloads'); ?></span>
		</div>

		<form id="adminForm" name="adminForm" method="post" action="index.php">
			<?php echo JHtml::_('form.token'); ?>
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;" align="absmiddle"/>
				<?php echo JText::_('month') . ': ' . $lists['month'];?>
				<?php echo JText::_('year') . ': ' . $lists['year'];?>
			</div>
			<input type="hidden" name="option" value="com_maqmahelpdesk">
			<input type="hidden" name="task" value="stats">
		</form>
		
		<div class="contentarea"><?php
		$sql = "select id, pname from #__support_dl order by pname";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		$nrwks = count($rows);
		$arrdays = array();
		$wkslabels = '';
		$days = HelpdeskDate::GetMonthDays($year, $month);

		for ($x = 0; $x < count($rows); $x++)
		{
			$row = $rows[$x];
			$wkslabels .= '<graph gid="' . ($x + 1) . '"><title>' . $row->pname . "</title><line_width>3</line_width><bullet>round_outlined</bullet><bullet_size>10</bullet_size><color_hover>#000000</color_hover></graph>";
			$sql = "SELECT DATE_FORMAT( s.dldate, '%d' ) AS DAY , count(*) AS total FROM `#__support_dl_stats` AS s WHERE month(s.`dldate`)=" . $database->quote($month) . " and year(s.`dldate`)=" . $database->quote($year) . " AND s.id_download='" . $row->id . "' GROUP BY DATE_FORMAT( s.dldate, '%d' ) ORDER BY DATE_FORMAT( s.dldate, '%d' )";
			$database->setQuery($sql);
			$rows_dates = $database->loadObjectList();
			$prev_day = 1;
			for ($i = 1; $i <= count($rows_dates); $i++)
			{
				$row_dates = $rows_dates[$i - 1];
				$arrdays[$x][number_format($row_dates->DAY, 0)] = $row_dates->total;
			}
		}

		$days_values = '';
		for ($i = 1; $i <= $days; $i++)
		{
			$days_values .= $i . ';';
			for ($x = 0; $x < $nrwks; $x++)
			{
				$days_values .= (isset($arrdays[$x][$i]) ? $arrdays[$x][$i] . ($nrwks - $x == 1 ? '\n' : ';') : '0' . ($nrwks - $x == 1 ? '\n' : ';'));
			}
		} ?>

		<br/>

		<table class="table table-striped table-bordered" cellspacing="0">
			<thead>
			<tr>
				<th class="algcnt">#</th>
				<th><?php echo JText::_('dl_product');?></th>
				<th><?php echo JText::_('user');?></th>
				<th class="algcnt"><?php echo JText::_('dl_version');?></th>
				<th><?php echo JText::_('filename');?></th>
				<th class="algcnt"><?php echo JText::_('ipaddress');?></th>
				<th class="algcnt"><?php echo JText::_('date');?></th>
			</tr>
			</thead>
			<tbody><?php
			for ($i = 0; $i < count($downloads); $i++)
			{
				$download = $downloads[$i]; ?>
				<tr>
					<td class="algcnt"><?php echo ($i + 1);?></td>
					<td><?php echo $download->product;?></td>
					<td><?php echo $download->name;?></td>
					<td class="algcnt"><?php echo $download->version;?></td>
					<td><?php echo $download->filename;?></td>
					<td class="algcnt"><?php echo $download->ipaddress;?></td>
					<td class="algcnt"><?php echo $download->dldate;?></td>
				</tr><?php
			} ?>
			<tr>
				<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
			</tr>
			</tbody>
		</table><?php
	}

	static function showHits(&$hits)
	{ ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('reports'); ?></a>
			<a href="javascript:;"><?php echo JText::_('download_stats'); ?></a>
			<span><?php echo JText::_('pagehits'); ?></span>
		</div>
		<div class="contentarea">
			<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th class="algcnt valgmdl" width="20">#</th>
					<th class="valgmdl"><?php echo JText::_('category');?></th>
					<th class="valgmdl"><?php echo JText::_('dl_product');?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('hits');?></th>
				</tr>
				</thead>
				<tbody><?php
					for ($i = 0; $i < count($hits); $i++)
					{
						$hit = $hits[$i]; ?>
						<tr>
							<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $hit->id; ?></span></td>
							<td class="valgmdl"><?php echo $hit->category;?></td>
							<td class="valgmdl"><?php echo $hit->product;?></td>
							<td class="algcnt valgmdl"><?php echo $hit->hits;?></td>
						</tr><?php
					} ?>
				</tbody>
			</table>
			<div class="clr"></div>
		</div><?php
	}
}
