<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if( !$this->singleSelection ) { ?>
<fieldset>
	<div class="toolbar" id="toolbar" style="float: right;">
		<button class="btn" type="button" onclick="if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT_SOMETHING', true); ?>');}else{submitbutton('useselection');}"><img style="vertical-align:middle;" src="<?php echo HIKASHOP_IMAGES; ?>add.png"/><?php echo JText::_('OK'); ?></button>
	</div>
</fieldset>
<?php } ?>
<div class="iframedoc" id="iframedoc"></div>
<form action="index.php?option=<?php echo HIKAMARKET_COMPONENT ?>&amp;ctrl=<?php echo JRequest::getCmd('ctrl'); ?>" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" onchange="document.adminForm.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
		</tr>
	</table>
	<table class="adminlist table table-striped table-hover" style="cell-spacing:1px">
		<thead>
			<tr>
				<th class="title titlenum"><?php
					echo JText::_('HIKA_NUM');
				?></th>
<?php if( !$this->singleSelection ) { ?>
				<th class="title titlebox"><input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" /></th>
<?php } ?>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_LOGIN'), 'u.username', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'u.name', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_EMAIL'), 'hu.user_email', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value );
				?></th>
				<th class="title"><?php
					echo JHTML::_('grid.sort',   JText::_( 'ID' ), 'hu.user_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php
					echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$k = 0;
	if(!empty($this->rows)) {
		foreach($this->rows as $i => $row) {

			$lbl1 = ''; $lbl2 = '';
			$extraTr = '';
			if( $this->singleSelection ) {
				$data = '{id:'.$row->user_id;
				foreach($this->elemStruct as $s) {
					if($s == 'id')
						continue;
					$data .= ','.$s.':\''. str_replace(array('\'','"'), array('\\\'','\\"'), $row->$s ).'\'';
				}
				$data .= '}';
				$extraTr = ' style="cursor:pointer" onclick="window.top.hikamarket.submitBox('.$data.');"';
			} else {
				$lbl1 = '<label for="cb'.$i.'">';
				$lbl2 = '</label>';
				$extraTr = ' onclick="hikamarket.checkRow(\'cb'.$i.'\');"';
			}

			if(!empty($this->pageInfo->search)) {
				$row = hikamarket::search($this->pageInfo->search, $row, 'user_id');
			}
?>
			<tr class="row<?php echo $k; ?>"<?php echo $extraTr; ?>>
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
<?php if( !$this->singleSelection ) { ?>
				<td align="center">
					<input type="checkbox" onclick="this.clicked=true; this.checked=!this.checked" value="<?php echo $row->user_id;?>" name="cid[]" id="cb<?php echo $i;?>"/>
				</td>
<?php } ?>
				<td><?php
					echo $lbl1 . $row->username . $lbl2;
				?></td>
				<td><?php
					echo $lbl1 . $row->name . $lbl2;
				?></td>
				<td><?php
					echo $lbl1 . $row->user_email . $lbl2;
				?></td>
				<td width="1%" align="center"><?php
					echo $row->user_id;
				?></td>
			</tr>
<?php
			$k = 1-$k;
		}
	}
?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="selection" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="single" value="<?php echo $this->singleSelection ? '1' : '0'; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
