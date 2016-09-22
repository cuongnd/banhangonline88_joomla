<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikamarket_product_listing">
<form action="<?php echo hikamarket::completeLink('product&task=waitingapproval'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="hikamarket_products_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="inputbox"/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_products_listing_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span7">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_products_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="inputbox"/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_products_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span5">
			<div class="expand-filters" style="width:auto;float:right">
<?php }

	if(!empty($this->vendorType))
		echo $this->vendorType->display('filter_vendors', @$this->pageInfo->filter->vendors);

if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else {?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php } ?>
<?php
	$show_product_image = $this->config->get('front_show_product_image', 1);
	$acl_product_code = hikamarket::acl('product/edit/code');
	$cols = 6;
?>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="hikamarket_product_num_title title titlenum"><?php
					echo JText::_('HIKA_NUM'); // JHTML::_('grid.sort', JText::_('HIKA_NUM'), 'product.product_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
<?php if($show_product_image) { $cols++; ?>
				<th class="hikamarket_product_image_title title"><?php
					echo JText::_('HIKA_IMAGE');
				?></th>
<?php } ?>
				<th class="hikamarket_product_name_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'product.product_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
					if($acl_product_code) {
						echo ' / ' . JHTML::_('grid.sort', JText::_('PRODUCT_CODE'), 'product.product_code', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
					}
				?></th>
				<th class="hikamarket_product_quantity_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PRODUCT_QUANTITY'), 'product.product_quantity', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_product_price_title title"><?php
					echo JText::_('PRODUCT_PRICE');
				?></th>
<?php
		if(!empty($this->fields)) {
			foreach($this->fields as $fieldName => $oneExtraField) {
				$cols++;
?>
				<th class="hikamarket_product_custom_<?php echo $fieldName;?>_title title"><?php
					echo $this->fieldsClass->getFieldName($oneExtraField);
				?></th>
<?php
			}
		}
?>
<?php
		if($this->product_action_delete) {
			$cols++;
?>
				<th class="hikamarket_product_actions_title title"><?php
					echo JText::_('HIKA_ACTIONS');
				?></th>
<?php 	} ?>
				<th class="hikamarket_product_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'product.product_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols ;?>">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach($this->products as $product) {
	$publishedid = 'product_published-'.(int)$product->product_id;
	$rowId = 'market_product_'.(int)$product->product_id;

	if($this->manage)
		$url = hikamarket::completeLink('product&task=edit&cid='.(int)$product->product_id);
	else
		$url = hikamarket::completeLink('shop.product&task=show&cid='.(int)$product->product_id);
?>
		<tr class="row<?php echo $k; ?>" id="<?php echo $rowId; ?>">
			<td class="hikamarket_product_num_value" align="center">
			<?php
				if( !isset($this->embbed) )
					echo $this->pagination->getRowOffset($i);
				else
					echo ($i+1);
			?>
			</td>
<?php if($show_product_image) { ?>
			<td class="hikamarket_product_name_value" style="text-align:center"><?php
				$thumb = $this->imageHelper->getThumbnail(@$product->file_path, array(50,50), array('default' => 1, 'forcesize' => 1));
				if(!empty($thumb->path))
					echo '<a href="'.$url.'"><img src="'. $this->imageHelper->uploadFolder_url . str_replace('\\', '/', $thumb->path).'" alt=""/></a>';
			?></td>
<?php } ?>
			<td class="hikamarket_product_name_value">
				<a href="<?php echo $url; ?>"><?php
					if(empty($product->product_name) && !empty($product->parent_product_name))
						echo '<em>'.$this->escape($product->parent_product_name, true).'</em>';
					else if(empty($product->product_name))
						echo '<em>'.JText::_('HIKAM_NO_NAME').'</em>';
					else
						echo $this->escape($product->product_name, true);
				?></a>
<?php if($acl_product_code) { ?>
				<div class="hikamarket_product_code_value"><a href="<?php echo $url; ?>"><?php echo $this->escape($product->product_code, true); ?></a></div>
<?php } ?>
			</td>
			<td class="hikamarket_product_quantity_value"><?php
				echo ($product->product_quantity >= 0) ? $product->product_quantity : JText::_('UNLIMITED');
			?></td>
			<td class="hikamarket_product_price_value"><?php
				echo $this->currencyHelper->displayPrices($product->prices);
			?></td>
<?php
		if(!empty($this->fields)) {
			foreach($this->fields as $fieldName => $oneExtraField) {
?>
				<td class="hikamarket_product_custom_<?php echo $fieldName;?>_value"><?php
					echo $this->fieldsClass->show($oneExtraField, $product->$fieldName);
				?></td>
<?php
			}
		}
?>
<?php if($this->product_action_delete) { ?>
			<td class="hikamarket_product_actions_value"><?php
				echo $this->toggleClass->delete($rowId, (int)$product->product_id, 'product', true);
			?></td>
<?php } ?>
			<td class="hikamarket_product_id_value" align="center"><?php
				echo (int)$product->product_id;
			?></td>
		</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
	</table>

	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="waitingapproval" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
