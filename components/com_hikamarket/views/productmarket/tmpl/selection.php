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
		<button class="btn" type="button" onclick="hikamarket_setId(this);"><img style="vertical-align:middle" src="<?php echo HIKASHOP_IMAGES; ?>add.png"/><?php echo JText::_('OK'); ?></button>
	</div>
<script type="text/javascript">
function hikamarket_setId(el) {
	if(document.hikamarket_form.boxchecked.value==0){
		alert('<?php echo JText::_('PLEASE_SELECT_SOMETHING', true); ?>');
	}else{
		el.form.ctrl.value = '<?php echo $this->ctrl ?>';
		hikamarket.submitform("<?php echo $this->task; ?>",el.form);
	}
}
</script>
</fieldset>
<?php } else { ?>
<script type="text/javascript">
function hikamarket_setId(id) {
	var form = document.getElementById("hikamarket_form");
	form.cid.value = id;
	form.ctrl.value = '<?php echo $this->ctrl ?>';
	hikamarket.submitform("<?php echo $this->task; ?>",form);
}
</script>
<?php } ?>
<form action="<?php echo hikamarket::completeLink('product'); ?>" method="post" name="hikamarket_form" id="hikamarket_form">
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
			echo '<span class="sbreadcrumb_ep">/</span> ';
		if($breadcrumb->category_id != $this->cid) {
			echo '<span class="breadcrumb_el"><a href="'.hikamarket::completeLink('category&task=listing&cid='.$breadcrumb->category_id).'">'.JText::_($breadcrumb->category_name).'</a></span> ';
		} else {
			echo '<span class="breadcrumb_el">'.JText::_($breadcrumb->category_name).'</span> ';
		}
	}
?>
	</div>
<?php
}

if($this->config->get('show_category_explorer', 1)) { ?>
	<table id="hikam_product_listing" style="border:0px;width:100%">
		<tr>
			<td style="vertical-align:top;width:1%">
				<div id="category_explorer_btn" class="category_explorer_btn_hide">
					<a href="#" onclick="return category_listing_hideshow(this);"><span><?php echo JText::_('EXPLORER'); ?></span></a>
				</div>
				<?php echo $this->shopCategoryType->displayTree('hikam_categories', $this->rootCategory, null, true, true); ?>
			</td>
			<td style="vertical-align:top;" id="hikam_product_main_listing">
<?php } ?>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%;cell-spacing:1px">
		<thead>
			<tr>
				<th class="title titlenum"><?php
					echo JText::_('HIKA_NUM');
				?></th>
<?php
$cols = 6;
if( !$this->singleSelection ) {
	$cols = 7;
?>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikamarket.checkAll(this);" />
				</th>
<?php } ?>
				<th class="hikamarket_product_name_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.product_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_product_code_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PRODUCT_CODE'), 'a.product_code', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_product_quantity_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PRODUCT_QUANTITY'), 'a.product_quantity', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_product_price_title title"><?php
					echo JText::_('PRODUCT_PRICE');
				?></th>
				<th class="hikamarket_product_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'a.product_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols ?>">
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

	$lbl1 = ''; $lbl2 = '';
	$extraTr = '';
	if( $this->singleSelection ) {
		if($this->confirm) {
			$data = '{id:'.$product->product_id;
			foreach($this->elemStruct as $s) {
				if($s == 'id')
					continue;
				$data .= ','.$s.':\''. str_replace(array('\'','"'),array('\\\'','\\\''),$product->$s).'\'';
			}
			$data .= '}';
			$extraTr = ' style="cursor:pointer" onclick="window.top.hikamarket.submitBox('.$data.');"';
		} else {
			$extraTr = ' style="cursor:pointer" onclick="hikamarket_setId(\''.$product->product_id.'\');"';
		}

		if(!empty($this->pageInfo->search)) {
			$row = hikamarket::search($this->pageInfo->search, $product, 'product_id');
		}
	} else {
		$lbl1 = '<label for="cb'.$i.'">';
		$lbl2 = '</label>';
		$extraTr = ' onclick="hikamarket.checkRow(\'cb'.$i.'\');"';
	}
?>
			<tr class="row<?php echo $k; ?>"<?php echo $extraTr; ?>>
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
<?php if( !$this->singleSelection ) { ?>
				<td align="center"><input type="checkbox" onclick="this.clicked = true; this.checked = !this.checked;" value="<?php echo $product->product_id; ?>" name="cid[]" id="cb<?php echo $i; ?>"/></td>
<?php } ?>
				<td class="hikamarket_product_name_value"><?php
					if(empty($product->product_name) && !empty($product->parent_product_name))
						echo '<em>'.htmlentities($product->parent_product_name).'</em>';
					else if(empty($product->product_name))
						echo '<em>'.JText::_('HIKAM_NO_NAME').'</em>';
					else
						echo htmlentities($product->product_name);
				?></td>
				<td class="hikamarket_product_code_value"><?php
					echo htmlentities($product->product_code);
				?></td>
				<td class="hikamarket_product_quantity_value"><?php
					echo ((int)$product->product_quantity >= 0) ? (int)$product->product_quantity : JText::_('UNLIMITED');
				?></td>
				<td class="hikamarket_product_price_value"><?php
					if(!empty($product->prices))
						echo $this->currencyHelper->displayPrices($product->prices);
				?></td>
				<td class="hikamarket_product_id_value" align="center"><?php
					echo (int)$product->product_id;
				?></td>
			</tr>
<?php
	$k = 1-$k;
	$i++;
}
?>
		</tbody>
<?php if($this->config->get('show_category_explorer', 1)) { ?>
	</table>
		</td>
	</tr>
<script type="text/javascript">
hikam_categories.sel(hikam_categories.find(<?php echo $this->cid; ?>));
hikam_categories.callbackSelection = function(tree,id) {
	var d = document, node = tree.get(id);
	if( node.value && node.name) {
		var form = document['hikamarket_form'];
		form['cid'].value = node.value;
		form.submit();
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
	if(el && el.parentNode)
		el.parentNode.style.height = (el.parentNode.offsetHeight) + 'px';
	if(el && el.firstChild && (data == 0 || data == '0'))
		category_listing_hideshow(el.firstChild, false);
})();
</script>
<?php } ?>
	</table>
<?php if( $this->singleSelection ) { ?>
	<input type="hidden" name="pid" value="0" />
<?php } ?>
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="selection" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="selection" value="products" />
	<input type="hidden" name="after" value="<?php echo JRequest::getVar('after', ''); ?>" />
	<input type="hidden" name="afterParams" value="<?php echo JRequest::getVar('afterParams', ''); ?>" />
	<input type="hidden" name="confirm" value="<?php echo $this->confirm ? '1' : '0'; ?>" />
	<input type="hidden" name="single" value="<?php echo $this->singleSelection ? '1' : '0'; ?>" />
	<input type="hidden" name="ctrl" value="product" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
<?php
	if(!empty($this->afterParams)) {
		foreach($this->afterParams as $p) {
			if(empty($p[0]) || !isset($p[1]))
				continue;
			echo '<input type="hidden" name="'.$this->escape($p[0]).'" value="'.$this->escape($p[1]).'"/>' . "\r\n";
		}
	}
?>
	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
document.adminForm = document.getElementById("hikamarket_form");
</script>
