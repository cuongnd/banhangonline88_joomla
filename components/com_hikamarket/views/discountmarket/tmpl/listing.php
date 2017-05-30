<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('discount&task=listing'); ?>" method="post" id="adminForm" name="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="hikamarket_discount_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_discount_listing_search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_discount_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_discount_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
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
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="hikamarket_discount_code_title title"><?php
					echo JHTML::_('grid.sort', JText::_('DISCOUNT_CODE'), 'discount.discount_code', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_discount_type_title title"><?php
					echo JHTML::_('grid.sort', JText::_('DISCOUNT_TYPE'), 'discount.discount_type', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_discount_dates_title title"><?php
					echo JHTML::_('grid.sort', JText::_('DISCOUNT_START_DATE'), 'discount.discount_start', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
					echo '<br/>';
					echo JHTML::_('grid.sort', JText::_('DISCOUNT_END_DATE'), 'discount.discount_end', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_discount_value_title title"><?php
					echo JText::_('DISCOUNT_VALUE');
				?></th>
<?php if(hikashop_level(1)){ ?>
				<th class="hikamarket_discount_quota_title title"><?php
					echo JText::_('DISCOUNT_QUOTA');
				?></th>
				<th class="hikamarket_discount_restrictions_title title"><?php
					echo JText::_('RESTRICTIONS');
				?></th>
<?php } ?>
<?php if($this->discount_actions) { ?>
				<th class="hikamarket_discount_actions_title title"><?php
					echo JText::_('HIKA_ACTIONS');
				?></th>
<?php } ?>
				<th class="hikamarket_discount_id_title title">
					<?php echo JHTML::_('grid.sort', JText::_( 'ID' ), 'discount.discount_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value ); ?>
				</th>
			</tr>
		</thead>
<?php if(!isset($this->embbed)) {
	$columns = 5;
	if(hikashop_level(1))
		$columns += 2;
	if($this->discount_actions)
		$columns += 1;
?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $columns; ?>">
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
foreach($this->discounts as $discount) {
	$publishedid = 'discount_published-'.$discount->discount_id;
	$rowId = 'market_discount_'.$discount->discount_id;
	if($this->manage)
		$url = hikamarket::completeLink('discount&task=show&cid='.$discount->discount_id);
?>
			<tr class="row<?php echo $k; ?>" id="<?php echo $rowId; ?>">
				<td class="hikamarket_discount_code_value"><?php
					if(!empty($url))
						echo '<a href="'.$url.'">';
					if(!empty($discount->discount_code))
						echo $discount->discount_code;
					else
						echo '<em>' . JText::_('HIKA_NONE') . '</em>';
					if(!empty($url))
						echo '</a>';
				?></td>
				<td class="hikamarket_discount_type_value"><?php echo $discount->discount_type; ?></td>
				<td class="hikamarket_discount_dates_value"><?php
					if(!empty($discount->discount_start) || !empty($discount->discount_end)) {
						if(!empty($discount->discount_start))
							echo hikamarket::getDate($discount->discount_start, '%Y-%m-%d %H:%M');
						else
							echo '-';
						echo '<br/>';
						if(!empty($discount->discount_end))
							echo hikamarket::getDate($discount->discount_end, '%Y-%m-%d %H:%M');
						else
							echo '-';
					}
				?></td>
				<td class="hikamarket_discount_value_value"><?php
					if(isset($discount->discount_flat_amount) && $discount->discount_flat_amount > 0) {
						echo $this->currencyClass->displayPrices(array($discount),'discount_flat_amount','discount_currency_id');
					} elseif(isset($discount->discount_percent_amount) && $discount->discount_percent_amount > 0) {
						echo $discount->discount_percent_amount. '%';
					}
				?></td>
<?php if(hikashop_level(1)) { ?>
				<td class="hikamarket_discount_quote_value"><?php
					if(empty($discount->discount_quota))
						echo JText::_('UNLIMITED');
					else
						echo $discount->discount_quota. ' (' . JText::sprintf('X_LEFT', $discount->discount_quota - $discount->discount_used_times) . ')';
				?></td>
				<td class="hikamarket_discount_restrictions_value"><?php
					$restrictions = array();
					if(!empty($discount->discount_minimum_order) && hikamarket::toFloat($discount->discount_minimum_order) != 0)
						$restrictions[] = JText::_('MINIMUM_ORDER_VALUE') . ': ' . $this->currencyClass->displayPrices(array($discount), 'discount_minimum_order', 'discount_currency_id');
					if(!empty($discount->product_name))
						$restrictions[] = JText::_('PRODUCT') . ': ' . $discount->product_name;
					if(!empty($discount->category_name)) {
						$restriction = JText::_('CATEGORY') . ': ' . $discount->category_name;
						if($discount->discount_category_childs)
							$restriction .= ' ' . JText::_('INCLUDING_SUB_CATEGORIES');
						$restrictions[] = $restriction;
					}
					if(!empty($discount->zone_name_english))
						$restrictions[] = JText::_('ZONE') . ': ' . $discount->zone_name_english;
					if($discount->discount_type == 'coupon') {
						if(!empty($discount->discount_coupon_product_only))
							 $restrictions[] = JText::_('HIKA_COUPON_PRODUCT_ONLY'); // 'Percentage for product only'

						if(!empty($discount->discount_coupon_nodoubling)) {
							switch($discount->discount_coupon_nodoubling) {
								case 1:
									$restrictions[] = JText::_('IGNORE_DISCOUNTED_PRODUCTS'); // 'Ignore discounted products'
									break;
								case 2:
									$restrictions[] = JText::_('COUPON_OVERRIDE_DISCOUNT_PRODUCTS'); // 'Override discounted products'
									break;
								default:
									break;
							}
						}
					}
					echo implode('<br/>', $restrictions);
				?></td>
<?php } ?>
<?php if($this->discount_actions) { ?>
				<td class="hikamarket_discount_actions_value"><?php
					if($this->discount_action_publish)
						echo $this->toggleClass->toggle($publishedid, (int)$discount->discount_published, 'discount').' ';
					if($this->discount_action_delete)
						echo $this->toggleClass->delete($rowId, (int)$discount->discount_id, 'discount', true);
				?></td>
<?php } ?>
				<td class="hikamarket_discount_id_value"><?php echo $discount->discount_id; ?></td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
