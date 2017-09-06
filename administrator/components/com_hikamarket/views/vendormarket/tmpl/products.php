<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if( !isset($this->embbed) ) { ?>
<div class="iframedoc" id="iframedoc"></div>
<div>
	<form action="<?php echo hikamarket::completeLink('vendor&task=products'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
	<div class="row-fluid">
		<div class="span6">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" onchange="this.form.submit();" />
				<button class="btn" onclick="this.form.limitstart.value=0;this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="this.form.limitstart.value=0;document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span6">
<?php } else { ?>
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" onchange="this.form.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
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
<?php } else { ?>
<div style="float:right">
	<a class="btn" href="<?php echo hikamarket::completeLink("vendor&task=products&cid[]=".$this->vendor_id); ?>">
		<img src="<?php echo HIKASHOP_IMAGES; ?>go.png" style="vertical-align:middle;margin:0 3px 0 0;"/><?php echo JText::_('SEE_ALL');?>
	</a>
</div>
<?php } ?>
	<table class="adminlist pad5 table table-striped table-hover" style="width:100%">
		<thead>
			<tr>
<?php if(!isset($this->embbed)) { ?>
				<th class="hikamarket_product_num_title title titlenum">
					<?php echo JHTML::_('grid.sort', JText::_('HIKA_NUM'), 'a.order_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
<?php } ?>
				<th class="hikamarket_product_name_title title">
					<?php
					if(isset($this->embbed))
						echo JText::_('HIKA_NAME');
					else
						echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.product_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
					?>
				</th>
				<th class="hikamarket_product_id_title title">
					<?php
					if(isset($this->embbed))
						echo JText::_('ID');
					else
						echo JHTML::_('grid.sort', JText::_('ID'), 'a.product_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
					?>
				</th>
			</tr>
		</thead>
<?php if( !isset($this->embbed) ) { ?>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
}

$k = 0;
$i = 0;
foreach($this->products as $product) {
?>
		<tr class="row<?php echo $k; ?>">
<?php if(!isset($this->embbed)) { ?>
			<td class="hikamarket_product_num_value" align="center">
			<?php
				if( !isset($this->embbed) )
					echo $this->pagination->getRowOffset($i);
				else
					echo ($i+1);
			?>
			</td>
<?php } ?>
			<td class="hikamarket_product_select_value">
				<a href="<?php echo hikamarket::completeLink('shop.product&task=edit&cid[]='.$product->product_id.'&cancel_redirect='.$this->cancelUrl); ?>"><?php
					if(!empty($product->product_name))
						echo $product->product_name;
					else
						echo '<em>'.JText::_('NO_NAME').'</em>';
				?></a>
			</td>
			<td class="hikamarket_product_id_value" align="center">
				<?php echo $product->product_id; ?>
			</td>
		</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
	</table>
<?php if( !isset($this->embbed) ) { ?>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="products" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->vendor_id ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
<?php } ?>
