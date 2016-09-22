<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaserial::completeLink('pack'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
	<div class="row-fluid">
		<div class="span4">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" />
				<button class="btn" onclick="document.adminForm.limitstart.value=0;this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.adminForm.limitstart.value=0;document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span8">
<?php } else { ?>
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" onchange="document.adminForm.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } ?>
				<!-- Filters -->
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
		</div>
	</div>
<?php } else { ?>
			</td>
		</tr>
	</table>
<?php } ?>
	<table class="adminlist pad5 table table-striped">
		<thead>
			<tr>
				<th class="hikaserial_pack_num_title title titlenum"><?php
					echo JText::_( 'HIKA_NUM' );
				?></th>
				<th class="hikaserial_pack_select_title title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" />
				</th>
				<th class="hikaserial_pack_name_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.pack_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_pack_data_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PACK_DATA'), 'a.pack_data', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_pack_generator_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PACK_GENERATOR'), 'a.pack_generator', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_pack_stats_title title"><?php
					echo JText::_('STATISTICS');
				?></th>
				<th class="title titletoggle"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_PUBLISHED'), 'a.pack_published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_pack_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'a.pack_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
foreach($this->rows as $i => &$row) {
?>
			<tr class="row<?php echo $k;?>">
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
				<td align="center"><?php
					echo JHTML::_('grid.id', $i, $row->pack_id);
				?></td>
				<td><?php
					if($this->manage){
						?><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$row->pack_id); ?>"><?php
					}
					echo $row->pack_name;
					if($this->manage){
						?></a> [<a href="<?php echo hikaserial::completeLink('serial&filter_pack='.$row->pack_id);?>"><?php echo JText::_('SEE_SERIALS');?></a>]<?php
					}
				?></td>
				<td><?php
					echo $row->pack_data;
				?></td>
				<td><?php
					$gen = $this->packGeneratorType->get($row->pack_generator);
					if(!empty($gen))
						echo $gen;
					else
						echo $row->pack_generator;
				?></td>
				<td><?php
					$counters = array();
					if(!empty($this->counters[$row->pack_id])) {
						foreach($this->counters[$row->pack_id] as $status => $counter) {
							$counters[] = '<span class="hikaserial_packlist_status">'. $this->serialStatusType->get($status) . '</span><span class="hikaserial_packlist_value">' . $counter . '</span>';
						}
					}
					if(!empty($counters)) {
						echo implode('<br/>', $counters);
					} else {
						echo '<em>'.JText::_('EMPTY_PACK').'</em>';
					}
				?></td>
				<td align="center"><?php
					if($this->manage) {
						?><span id="pack_published-<?php echo $row->pack_id; ?>" class="spanloading"><?php
							echo $this->toggleClass->toggle('pack_published-'.$row->pack_id, (int)$row->pack_published, 'pack');
						?></span><?php
					} else {
						echo $this->toggleClass->display('activate', $row->pack_published);
					}
				?></td>
				<td width="1%" align="center"><?php
					echo $row->pack_id;
				?></td>
			</tr>
<?php
	$k = 1 - $k;
}
unset($row);
?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="<?php echo @$this->task; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
