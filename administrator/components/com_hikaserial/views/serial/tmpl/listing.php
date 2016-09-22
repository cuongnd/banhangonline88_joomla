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
<form action="<?php echo hikaserial::completeLink('serial'); ?>" method="post" name="adminForm" id="adminForm">
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
				<?php echo $this->packType->display('filter_pack', @$this->pageInfo->filter->pack, true, true);?>
				<?php echo $this->serialStatusType->display('filter_status', @$this->pageInfo->filter->serial_status, true, true);?>
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
		</div>
	</div>
<?php } else { ?>
			</td>
		</tr>
	</table>
<?php } ?>
	<table class="adminlist pad5 table table-striped table-hover">
		<thead>
			<tr>
				<th class="hikaserial_serial_num_title title titlenum"><?php
					echo JText::_( 'HIKA_NUM' );
				?></th>
				<th class="hikaserial_serial_select_title title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" />
				</th>
				<th class="hikaserial_serial_data_title title"><?php
					echo JHTML::_('grid.sort', JText::_('SERIAL_DATA'), 'a.serial_data', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_serial_pack_title title"><?php
					echo JHTML::_('grid.sort', JText::_('SERIAL_PACK'), 'a.serial_pack_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_serial_status_title title"><?php
					echo JHTML::_('grid.sort', JText::_('SERIAL_STATUS'), 'a.serial_status', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_serial_orderid_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ORDER_NUMBER'), 'a.serial_order_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_serial_userid_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_USER'), 'a.serial_user_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaserial_serial_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'a.serial_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
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
					echo JHTML::_('grid.id', $i, $row->serial_id);
				?></td>
				<td><?php
					if($this->manage){
						?><a href="<?php echo hikaserial::completeLink('serial&task=edit&cid[]='.$row->serial_id); ?>"><?php
					}
					if(!isset($row->serial_text_data)) {
						$row->serial_text_data = $row->serial_data;
						$row->serial_data = str_replace(array("\r\n","\r","\n"), '<br/>', $row->serial_data);
					}
					if(strpos($row->serial_data, '<span ') !== false) {
						echo $row->serial_data;
					} else {
						$sizelimit = $this->config->get('serial_display_size', 30);
						if($sizelimit > 0 && strlen($row->serial_text_data) > $sizelimit && (!isset($row->trucante) || $row->trucante !== false)) {
							echo str_replace( array("\r\n","\r","\n"), '<br/>', substr($row->serial_text_data, 0, $sizelimit)) . JText::_('SERIAL_TRUNCATED');
						} else {
							echo $row->serial_data;
						}
					}
					if($this->manage){
						?></a><?php
					}
				?></td>
				<td><?php
					if($this->manage){
						?><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$row->serial_pack_id); ?>"><?php
					}
					echo $row->pack_name;
					if($this->manage){
						?></a><?php
					}
				?></td>
				<td><?php
					if($this->manage){
						?><a href="<?php echo hikaserial::completeLink('serial&task=edit&cid[]='.$row->serial_id); ?>"><?php
					}
					echo $this->serialStatusType->get($row->serial_status);
					if($this->manage){
						?></a><?php
					}
				?></td>
				<td><?php
					if((int)$row->serial_order_id > 0) {
						if($this->manage_shop_order) {
							?><a href="<?php echo hikaserial::completeLink('shop.order&task=edit&cid[]='.$row->serial_order_id); ?>"><?php
						}
						if(!empty($row->order_number))
							echo $row->order_number;
						else
							echo '<em>' . $row->serial_order_id . '</em>';
						if($this->manage_shop_order) {
							?></a><?php
						}
					} else {
						echo ''; // TODO
					}
				?></td>
				<td><?php
					if((int)$row->serial_user_id > 0) {
						if($this->manage_shop_user) {
							?><a href="<?php echo hikaserial::completeLink('shop.user&task=edit&cid[]='.$row->serial_user_id); ?>"><?php
						}
						if(!empty($row->username))
							echo $row->username;
						else
							echo '<em>' . $row->serial_user_id . '</em>';
						if($this->manage_shop_user) {
							?></a><?php
						}
					} else {
						echo ''; // TODO
					}
				?></td>
				<td align="center"><?php
					echo $row->serial_id;
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
	<input type="hidden" name="ctrl" value="serial" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="export_data" id="hikaserial_export_data" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
