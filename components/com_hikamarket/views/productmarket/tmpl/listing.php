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
<form action="<?php echo hikamarket::completeLink('product&task=listing'); ?>" method="post" name="adminForm" id="adminForm">
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

	if(!empty($this->productType))
		echo $this->productType->display('filter_product_type', @$this->pageInfo->filter->filter_product_type);

	if($this->config->get('show_category_explorer', 1))
		echo $this->childdisplayType->display('filter_type', $this->pageInfo->selectedType, false, false);

if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else {?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php }
if(!empty($this->breadcrumb)) {
?>
	<div class="hikam_breadcrumb">
<?php
	foreach($this->breadcrumb as $i => $breadcrumb) {
		if($i > 0)
			echo '<span class="breadcrumb_sep">/</span>';
		if($breadcrumb->category_id != $this->cid) {
			echo '<span class="breadcrumb_el"><a href="'.hikamarket::completeLink('category&task=listing&cid='.$breadcrumb->category_id).'">'.JText::_($breadcrumb->category_name).'</a></span>';
		} else {
			echo '<span class="breadcrumb_el">'.JText::_($breadcrumb->category_name).'</span>';
		}
	}
?>
	</div>
<?php
}
?>
<?php if($this->config->get('show_category_explorer', 1)) { ?>
	<table id="hikam_product_listing" style="border:0px;width:100%">
		<tr>
			<td style="vertical-align:top;width:1%">
				<div id="category_explorer_btn" class="category_explorer_btn_hide">
					<a href="#" onclick="return category_listing_hideshow(this);"><span><?php echo JText::_('EXPLORER'); ?></span></a>
				</div>
				<?php echo $this->shopCategoryType->displayTree('hikam_categories', $this->rootCategory, null, true, true); ?>
			</td>
			<td style="vertical-align:top;" id="hikam_product_main_listing">
<?php } else
		echo '<div id="hikam_product_main_listing">';

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
		if($this->product_actions) {
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
<?php if($product->product_type == 'waiting_approval') echo '<img src="'.HIKAMARKET_IMAGES.'icon-16/unpublish.png" data-toggle="hk-tooltip" data-title="'.JText::_('HIKAM_PRODUCT_NOT_APPROVED', true).'" alt="X"/>'; ?>
				<a href="<?php echo $url; ?>"><?php
					if(empty($product->product_name) && !empty($product->parent_product_name))
						echo '<em>'.$this->escape($product->parent_product_name, true).'</em>';
					else if(empty($product->product_name))
						echo '<em>'.JText::_('HIKAM_NO_NAME').'</em>';
					else
						echo $this->escape($product->product_name, true);
				?></a>
<?php if(!$this->product_action_publish) { ?>
				<div class="hikamarket_product_published_value" style="float:right"><?php echo $this->toggleClass->display('product', (int)$product->product_published); ?></div>
<?php } ?>
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
<?php if($this->product_actions) { ?>
			<td class="hikamarket_product_actions_value"><?php
				if($this->product_action_publish)
					echo $this->toggleClass->toggle($publishedid, (int)$product->product_published, 'product') . ' ';
				if($this->product_action_copy)
					echo '<a href="#copy" onclick="return window.localPage.copyProduct('.(int)$product->product_id.', \''.urlencode(strip_tags($product->product_name)).'\');"><img src="'.HIKAMARKET_IMAGES.'icon-16/plus.png" alt="Copy" /></a>';
				if($this->product_action_delete)
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

if($this->config->get('show_category_explorer', 1)) { ?>
	</table>
		</td>
	</tr>
</table>
<script type="text/javascript">
hikam_categories.sel(hikam_categories.find(<?php echo $this->cid; ?>));
hikam_categories.callbackSelection = function(tree,id) {
	var d = document, node = tree.get(id);
	if(node.value && node.name) {
		var u = "<?php echo hikamarket::completeLink('product&task=listing&cid=HIKACID', false, false, true);?>";
		window.location = u.replace('HIKACID', node.value);
	}
};
function category_listing_hideshow(el, state) {
	var d = document, w = window, o = w.Oby, tree = d.getElementById("hikam_categories_otree"), p = el.parentNode;
	if((state !== true && o.hasClass(p, "category_explorer_btn_hide")) || state === false) {
		tree.style.display = "none";
		o.removeClass(p, "category_explorer_btn_hide");
		o.addClass(p, "category_explorer_btn_show");
		state = 0;
	} else {
		o.removeClass(p, "category_explorer_btn_show");
		o.addClass(p, "category_explorer_btn_hide");
		tree.style.display = "";
		state = 1;
	}
	w.hikamarket.dataStore("hikamarket_product_listing_explorer", state);
	return false;
}
(function(){
	var el = document.getElementById('category_explorer_btn'),
		data = window.hikamarket.dataGet("hikamarket_product_listing_explorer");
	if(el && el.parentNode && el.parentNode.offsetHeight > 0)
		el.parentNode.style.height = (el.parentNode.offsetHeight + 20) + 'px';
	if(el && el.firstChild && (data == 0 || data == '0'))
		category_listing_hideshow(el.firstChild, false);
})();
</script>
<?php } else
		echo '</table></div>';
?>
<?php if($this->product_action_copy) { ?>
<script type="text/javascript">
if(!window.localPage) window.localPage = {};
window.localPage.copyProduct = function(id, name) {
	var confirmMsg = "<?php echo JText::_('CONFIRM_COPY_PRODUCT_X'); ?>";
	if(!confirm(confirmMsg.replace('{PRODUCT}', decodeURI(name))))
		return false;
	var url = '<?php echo hikamarket::completeLink('product&task=copy&product_id=HIKAID&'.hikamarket::getFormToken().'=1&return_url=HIKAURL'); ?>';
	url = url.replace('HIKAID', id).replace('HIKAURL', btoa(encodeURI(window.location)));
	window.location = url;
	return false;
}
</script>
<?php } ?>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
