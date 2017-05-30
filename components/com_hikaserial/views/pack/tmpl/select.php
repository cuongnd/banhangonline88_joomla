<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if( !$this->singleSelection ) { ?>
<fieldset>
	<div class="toolbar" id="toolbar" style="float: right;">
		<button class="btn" type="button" onclick="if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT_SOMETHING', true); ?>');}else{hikaserial.submitform('useselection',this.form);}"><img src="<?php echo HIKASERIAL_IMAGES; ?>icon-16/add.png"/><?php echo JText::_('OK'); ?></button>
	</div>
</fieldset>
<?php } else { ?>
<script type="text/javascript">
function hikaserial_setId(id) {
	var form = document.getElementById("adminForm");
	form.cid.value = id;
	hikaserial.submitform("useselection",form);
}
</script>
<?php } ?>
<form action="index.php?option=<?php echo HIKASERIAL_COMPONENT ?>&amp;ctrl=<?php echo JRequest::getCmd('ctrl'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" onchange="this.form.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
		</tr>
	</table>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="title titlenum"><?php
					echo JText::_( 'HIKA_NUM' );
				?></th>
<?php
$cols = 5;
if( !$this->singleSelection ) {
	$cols++;
?>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" />
				</th>
<?php } ?>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.pack_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('PACK_DATA'), 'a.pack_data', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('PACK_GENERATOR'), 'a.pack_generator', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'a.pack_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$k = 0;
	for($i = 0, $a = count($this->rows); $i < $a; $i++) {
		$row =& $this->rows[$i];

		$lbl1 = ''; $lbl2 = '';
		$extraTr = '';
		if( $this->singleSelection ) {
			if($this->confirm) {
				$data = '{id:'.$row->pack_id;
				foreach($this->elemStruct as $s) {
					if($s == 'id')
						continue;
					$data .= ','.$s.':\''. str_replace(array('\'','"'),array('\\\'','\\\''),$row->$s).'\'';
				}
				$data .= '}';
				$extraTr = ' style="cursor:pointer" onclick="window.top.hikaserial.submitBox('.$data.');"';
			} else {
				$extraTr = ' style="cursor:pointer" onclick="hikaserial_setId(\''.$row->user_id.'\');"';
			}
		} else {
			$lbl1 = '<label for="cb'.$i.'">';
			$lbl2 = '</label>';
			$extraTr = ' onclick="hikaserial.checkRow(\'cb'.$i.'\');"';
		}
?>
			<tr class="row<?php echo $k; ?>"<?php echo $extraTr; ?>>
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
<?php if( !$this->singleSelection ) { ?>
				<td align="center">
					<input type="checkbox" onclick="this.clicked=true; this.checked=!this.checked" value="<?php echo $row->pack_id;?>" name="cid[]" id="cb<?php echo $i;?>"/>
				</td>
<?php } ?>
				<td><?php
					echo $lbl1 . $row->pack_name . $lbl2;
				?></td>
				<td><?php
					echo $lbl1 . $row->pack_data . $lbl2;
				?></td>
				<td><?php
					echo $lbl1 . $row->pack_generator . $lbl2;
				?></td>
				<td width="1%" align="center"><?php
					echo $row->pack_id;
				?></td>
			</tr>
<?php
		$k = 1-$k;
	}
?>
		</tbody>
	</table>
<?php if( $this->singleSelection ) { ?>
	<input type="hidden" name="cid" value="0" />
<?php } ?>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="select" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="confirm" value="<?php echo $this->confirm ? '1' : '0'; ?>" />
	<input type="hidden" name="single" value="<?php echo $this->singleSelection ? '1' : '0'; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
