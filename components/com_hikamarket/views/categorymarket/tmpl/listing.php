<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('category&task=listing&cid='.$this->cid); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="hikamarket_category_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_category_listing_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_category_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_category_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
			<div class="expand-filters" style="width:auto;float:right">
<?php }
	echo $this->childdisplayType->display('filter_type', $this->pageInfo->selectedType, false);

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
	<table id="hikam_category_listing" style="border:0px;width:100%">
		<tr>
<?php if($this->config->get('show_category_explorer', 1)) { ?>
			<td style="vertical-align:top;width:1%">
				<div id="category_explorer_btn" class="category_explorer_btn_hide">
					<a href="#" onclick="return category_listing_hideshow(this);"><span><?php echo JText::_('EXPLORER'); ?></span></a>
				</div>
				<?php echo $this->shopCategoryType->displayTree('hikam_categories', $this->rootCategory, null, true, true); ?>
			</td>
<?php } ?>
			<td style="vertical-align:top;" id="hikam_category_main_listing">
				<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
					<thead>
						<tr>
							<th class="hikamarket_category_num_title title titlenum"><?php
								if($this->doOrdering) {
									echo JHTML::_('grid.sort', JText::_('HIKA_NUM'), 'category.category_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
								} else {
									echo JHTML::_('grid.sort', JText::_('HIKA_NUM'), 'category.category_left', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
								}
							?></th>
							<th class="hikamarket_category_image_title title"><?php
								echo JText::_('HIKA_IMAGE');
							?></th>
							<th class="hikamarket_category_name_title title"><?php
								echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'category.category_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
								if($this->doOrdering) {
									echo ' / ' . JHTML::_('grid.sort', JText::_('HIKA_ORDER'), 'category.category_ordering', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
								}
								?> / <?php
								echo JText::_('HIKA_PUBLISHED');
							?></th>
						</tr>
					</thead>
<?php if(!isset($this->embbed)) { ?>
					<tfoot>
						<tr>
							<td colspan="3">
								<?php echo $this->pagination->getListFooter(); ?>
								<?php echo $this->pagination->getResultsCounter(); ?>
							</td>
						</tr>
					</tfoot>
<?php } ?>
					<tbody>
<?php
	$k = 0;
	$i = 0;
	$nbCatagories = count($this->elements);
	foreach($this->elements as $category) {
		$publishedid = 'category_published-'.$category->category_id;
?>
						<tr class="row<?php echo $k; ?>">
							<td class="hikamarket_category_num_value"><?php
								echo $this->pagination->getRowOffset($i);
							?></td>
							<td class="hikamarket_category_image_value" align="center">
								<a href="<?php echo hikamarket::completeLink('category&task=show&cid='.$category->category_id); ?>"><?php
									$thumb = $this->imageHelper->getThumbnail($category->file_path, array(100,100), array('default' => 1, 'forcesize' => 1));
									if(!empty($thumb->path)) {
										echo '<img src="'. $this->imageHelper->uploadFolder_url . str_replace('\\', '/', $thumb->path).'" alt=""/>';
									}
								?></a>
							</td>
							<td class="hikamarket_category_data_value">
								<div class="hikamarket_category_name_value">
<?php if(hikamarket::acl('category/edit')) { ?>
									<a href="<?php echo hikamarket::completeLink('category&task=show&cid='.$category->category_id); ?>"><?php echo $category->category_name; ?></a>
<?php } else { ?>
									<span><?php echo $category->category_name; ?></span>
<?php } ?>
								</div>
<?php if($this->doOrdering) { ?>
								<div class="hikamarket_category_name_value">
									<span><?php echo JText::_('HIKA_ORDER'); ?></span>
									<input type="text" name="order[<?php echo $category->category_id;?>]" size="5" <?php if(!$this->ordering->ordering) echo 'disabled="disabled"'?> value="<?php echo $category->category_ordering; ?>" class="text_ordering" style="text-align: center" />
								</div>
<?php } ?>
								<div class="hikam_category_publish_value">
									<span><?php echo JText::_('HIKA_PUBLISHED'); ?></span>
									<?php
										if(hikamarket::acl('category/edit/published'))
											echo $this->toggleClass->toggle($publishedid,(int) $category->category_published, 'category');
										else
											echo $this->toggleClass->display($publishedid,(int) $category->category_published);
									?>
								</div>
<?php
		if(!empty($this->fields)) {
			foreach($this->fields as $fieldName => $oneExtraField) {
				if(empty($category->$fieldName))
					continue;
?>
								<div class="hikam_category_custom_value hikam_category_custom_<?php echo $fieldName;?>_value">
									<span><?php echo $this->fieldsClass->getFieldName($oneExtraField); ?></span>:
									<?php echo $this->fieldsClass->show($oneExtraField, $category->$fieldName); ?>
								</div>
<?php
			}
		}
?>
								<div class="hikam_category_open">
									<span><?php echo JText::_('HIKA_BROWSE_CATEGORY'); ?></span>
									<a href="<?php echo hikamarket::completeLink('category&task=listing&cid='.$category->category_id);?>"><img src="<?php echo HIKAMARKET_IMAGES; ?>otree/folder.gif" alt="<?php echo JText::_('HIKA_BROWSE_CATEGORY'); ?>"/></a>
								</div>
							</td>
						</tr>
<?php
		$i++;
		$k = 1 - $k;
	}
?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
<?php if($this->config->get('show_category_explorer', 1)) { ?>
<script type="text/javascript">
hikam_categories.sel(hikam_categories.find(<?php echo $this->cid; ?>));
hikam_categories.callbackSelection = function(tree,id) {
	var d = document, node = tree.get(id);
	if( node.value && node.name) {
		var u = "<?php echo hikamarket::completeLink('category&task=listing&cid={CID}', false, false, true);?>";
		window.location = u.replace('{CID}', node.value);
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
	w.hikamarket.dataStore("hikamarket_category_listing_explorer", state);
	return false;
}
(function(){
	var el = document.getElementById('category_explorer_btn'),
		data = window.hikamarket.dataGet("hikamarket_category_listing_explorer");
	if(el && el.parentNode && el.parentNode.offsetHeight > 0)
		el.parentNode.style.height = (el.parentNode.offsetHeight) + 'px';
	if(el && el.firstChild && (data == 0 || data == '0'))
		category_listing_hideshow(el.firstChild, false);
})();
</script>
<?php } ?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
