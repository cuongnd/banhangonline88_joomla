<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.multiselect');

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

$filterTippedStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_TIPPED'),
	"0"=>JText::_('COM_CMGROUPBUYING_UNTIPPED'),
	"1"=>JText::_('COM_CMGROUPBUYING_TIPPED'),
);

$filterTippedOption = array();

foreach($filterTippedStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterTippedOption, $option);
}

$filterVoidedStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_DEAL'),
	"0"=>JText::_('COM_CMGROUPBUYING_ACTIVE'),
	"1"=>JText::_('COM_CMGROUPBUYING_VOIDED'),
);

$filterVoidedOption = array();

foreach($filterVoidedStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterVoidedOption, $option);
}

$filterPublishedStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_PUBLISHED'),
	"0"=>JText::_('COM_CMGROUPBUYING_UNPUBLISHED'),
	"1"=>JText::_('COM_CMGROUPBUYING_PUBLISHED'),
);

$filterPublishedOption = array();

foreach($filterPublishedStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterPublishedOption, $option);
}

$filterFeaturedStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_FEATURED'),
	"0"=>JText::_('COM_CMGROUPBUYING_UNFEATURED'),
	"1"=>JText::_('COM_CMGROUPBUYING_FEATURED'),
);

$filterFeaturedOption = array();

foreach($filterFeaturedStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterFeaturedOption, $option);
}

$filterApprovedStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_APPROVED'),
	"0"=>JText::_('COM_CMGROUPBUYING_PENDING'),
	"1"=>JText::_('COM_CMGROUPBUYING_APPROVED'),
);

$filterApprovedOption = array();

foreach($filterApprovedStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterApprovedOption, $option);
}
?>
<script>
	function clear_filter()
	{
		document.id('filter_search').value = '';
		document.id('filter_featured').value = '*';
		document.id('filter_tipped').value = '*';
		document.id('filter_voided').value = '*';
		document.id('filter_published').value = '*';
		document.id('filter_approved').value = '*';
		document.adminForm.submit();
	}
</script>
<div class="cmgroupbuying">
	<form action="index.php?option=com_cmgroupbuying" method="post" name="adminForm" id="adminForm" class="modelList">
	<?php if(!empty( $this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_SEARCH_IN_NAME'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CMGROUPBUYING_SEARCH_IN_NAME'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterTippedOption, 'filter_tipped', null , 'value', 'text', $this->state->get('filter.tipped'));?>
				</div>
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterApprovedOption, 'filter_approved', null , 'value', 'text', $this->state->get('filter.approved'));?>
				</div>
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterPublishedOption, 'filter_published', null , 'value', 'text', $this->state->get('filter.published'));?>
				</div>
				<div class="btn-group">
					<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</div>
			<div class="span12">
				<span class="text-warning"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_WARNING_1'); ?></span><br /><?php echo JText::_('COM_CMGROUPBUYING_DEAL_WARNING_2'); ?><br /><?php echo JText::_('COM_CMGROUPBUYING_DEAL_WARNING_3'); ?>
			</div>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_DEAL_FIELD_NAME_LABEL', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_DEAL_FIELD_PARTNER_ID_LABEL', 'a.partner_id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_DEAL_FIELD_CATEGORY_ID_LABEL', 'a.category_id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_FIELD_LOCATION_LABEL'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_DEAL_FIELD_PRODUCT_ID_LABEL', 'a.category_id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_DEAL_FIELD_END_DATE_LABEL', 'a.end_date', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_BOUGHT_COUPON_QUANTITY'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_TIPPED', 'a.tipped', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ACTIVE', 'a.voided', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_APPROVED', 'a.approved', $listDirn, $listOrder, NULL, 'desc'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
							<?php if ($saveOrder) :?>
								<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'deals.saveorder'); ?>
							<?php endif; ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="15">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					$ordering = $listOrder == 'a.ordering';
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&task=deal.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?></a>
							<p class="smallsub">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
						</td>
						<td class="center">
							<?php
							$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($item->partner_id);
							if(empty($partner))
							{
								echo JText::_('COM_CMGROUPBUYING_DEAL_NO_PARTNER_ASSIGNED');
							}
							else
							{
								echo $partner['name'];
							}
							?>
						</td>
						<td class="center">
							<?php
							$category = JModelLegacy::getInstance('Category', 'CMGroupBuyingModel')->getCategoryById($item->category_id);
							if(empty($category))
							{
								echo JText::_('COM_CMGROUPBUYING_DEAL_UNCATEGORIZED');
							}
							else
							{
								echo $category['name'];
							}
							?>
						</td>
						<td class="center">
							<?php
							$location = implode("<br/>", CMGroupBuyingHelperDeal::getLocationsOfDeal($item->id));
							if(empty($location))
							{
								echo JText::_('COM_CMGROUPBUYING_DEAL_UNLOCATED');
							}
							else
							{
								echo $location;
							}
							?>
						</td>
						<td class="center">
							<?php
							$product = JModelLegacy::getInstance('Product', 'CMGroupBuyingModel')->getItem($item->product_id);
							if(!empty($product))
							{
								echo $product->name;
							}
							?>
						</td>
						<td class="center">
							<?php echo $item->end_date; ?>
						</td>
						<td class="center">
							<?php echo CMGroupBuyingHelperDeal::countPaidCoupon($item->id); ?>
						</td>
						<td class="center">
							<?php if($item->tipped): ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_TIPPED'); ?>"><i class="icon-ok"></i></span></span>
							<?php else: ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_UNTIPPED'); ?>"><i class="icon-remove"></i></span></span>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php if($item->voided): ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_VOIDED'); ?>"><i class="icon-remove"></i></span>
							<?php else: ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_ACTIVE'); ?>"><i class="icon-ok"></i></span>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'deals.', true, 'cb'); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('deal.featured', $item->featured, $i, true); ?>
						</td>
						<td class="center">
							<?php if($item->approved): ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_APPROVED'); ?>"><i class="icon-ok"></i></span></span>
							<?php else: ?>
							<span class="jgrid" title="<?php echo JText::_('COM_CMGROUPBUYING_PENDING'); ?>"><i class="icon-remove"></i></span></span>
							<?php endif; ?>
						</td>
						<td class="order center">
							<?php if ($saveOrder) :?>
								<?php if ($listDirn == 'asc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, true,'deals.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'deals.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php elseif ($listDirn == 'desc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, true,'deals.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'deals.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order input-super-mini" />
						</td>
						<td class="center">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<div>
			<input type="hidden" name="view" value="deals" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>