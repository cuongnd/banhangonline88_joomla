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
<form action="<?php echo hikamarket::completeLink('order&task=listing'); ?>" method="post" id="adminForm" name="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td style="width:100%;">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="hikamarket_order_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_order_listing_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span12">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_order_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_order_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="expand-filters" style="width:auto;">
<?php }

echo $this->orderStatusType->display('filter_status', $this->pageInfo->filter->filter_status, ' onchange="document.adminForm.submit();"', true);
echo $this->paymentType->display('filter_payment', $this->pageInfo->filter->filter_payment, false);

if(!empty($this->pageInfo->filter->filter_user)) {
	$userClass = hikamarket::get('shop.class.user');
	$user_filter = $userClass->get($this->pageInfo->filter->filter_user);
?>
	<input type="hidden" name="filter_user" value="<?php echo (int)$this->pageInfo->filter->filter_user; ?>" id="hikamarket_order_listing_filter_user" />
	<button class="btn" onclick="var el = document.getElementById('hikamarket_order_listing_filter_user'); if(el) el.value = ''; document.adminForm.submit(); return false;"><?php echo $user_filter->user_email; ?> <img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt="X" /></button>
<?php
}

foreach($this->extrafilters as $name => $filterObj) {
	echo $filterObj->displayFilter($name, $this->pageInfo->filter);
}

if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php }

	$oldDesign = (bool)$this->config->get('legacy_orderlisting', false);

	if(!empty($this->order_stats)) {
?><table class="order_statistics table table-bordered" style="width:100%">
	<tr>
<?php
		$width = floor(100 / (count($this->order_stats)+1));
		$total_orders = 0;
		foreach($this->order_stats as $status => $obj) {
			if(empty($status))
				continue;
			$total = (int)$obj->total;
			$total_orders += $total;

			$class = ($this->pageInfo->filter->filter_status == $status) ? 'order_statistics_active' : '';

?>		<td style="width:<?php echo $width;?>%" class="<?php echo $class; ?>">
			<a href="<?php echo hikamarket::completeLink('order&task=listing&filter_status='.$status); ?>">
				<span class="value"><?php echo $total; ?></span>
				<span class="order-label order-label-<?php echo preg_replace('#[^a-z_0-9]#i', '_', str_replace(' ','_',$status)); ?>"><?php
					echo hikamarket::orderStatus($status);
				?></span>
			</a>
		</td>
<?php
		}
?>
		<td style="width:<?php echo $width;?>%">
			<a href="<?php echo hikamarket::completeLink('order&task=listing&filter_status='); ?>">
				<span class="value"><?php echo $total_orders; ?></span>
				<span class="order-label order-label-all"><?php echo JText::_('HIKAM_STAT_ALL'); ?></span>
			</a>
		</td>
	</tr>
</table>
<?php
	}
?>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
<?php if($oldDesign) { ?>
				<th class="hikamarket_order_num_title title titlenum"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NUM'), 'hkorder.order_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ORDER_NUMBER'), 'hkorder.order_number', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_customer_title title"><?php
					echo JHTML::_('grid.sort', JText::_('CUSTOMER'), 'hkuser.user_email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_status_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ORDER_STATUS'), 'hkorder.order_status', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_date_title title"><?php
					echo JHTML::_('grid.sort', JText::_('DATE'), 'hkorder.order_modified', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_total_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKASHOP_TOTAL'), 'hkorder.order_full_price', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
			</tr>
<?php } else { ?>
				<th class="hikamarket_order_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_ORDER'), 'hkorder.order_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_status_title title"><?php
					echo JHTML::_('grid.sort', JText::_('ORDER_STATUS'), 'hkorder.order_status', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_id_title title"><?php
					echo JText::_('HIKA_BILLING');
				?></th>
				<th class="hikamarket_order_id_title title"><?php
					echo JText::_('HIKA_SHIPPING');
				?></th>
				<th class="hikamarket_order_total_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKASHOP_TOTAL'), 'hkorder.order_full_price', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_order_date_title title"><?php
					echo JHTML::_('grid.sort', JText::_('DATE'), 'hkorder.order_modified', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
<?php } ?>
		</thead>
<?php if(!isset($this->embbed)) { ?>
		<tfoot>
			<tr>
				<td colspan="6">
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
foreach($this->orders as $order) {
?>
<?php if($oldDesign) { ?>
			<tr class="row<?php echo $k; ?>">
				<td class="hikamarket_order_num_value"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
				<td class="hikamarket_order_id_value" align="center">
					<a href="<?php echo hikamarket::completeLink('order&task=show&cid='.$order->order_id); ?>"><?php echo $order->order_number; ?></a>
				</td>
				<td class="hikamarket_order_customer_value"><?php echo $order->user_email; ?></td>
				<td class="hikamarket_order_status_value"><span class="order-label order-label-<?php echo preg_replace('#[^a-z_0-9]#i', '_', str_replace(' ','_',$order->order_status)); ?>"><?php
					echo hikamarket::orderStatus($order->order_status);
				?></span></td>
				<td class="hikamarket_order_date_value"><?php echo hikamarket::getDate($order->order_created,'%Y-%m-%d %H:%M');?></td>
				<td class="hikamarket_order_total_value"><?php echo $this->currencyHelper->format($order->order_full_price, $order->order_currency_id); ?></td>
			</tr>
<?php
	} else {
		if(HIKASHOP_RESPONSIVE)
			$label_classes = array('blue' => 'label label-info', 'grey' => 'label label-default');
		else
			$label_classes = array('blue' => 'hk-label hk-label-blue', 'grey' => 'hk-label hk-label-grey');
?>
			<tr class="row<?php echo $k; ?>">
				<td class="hikamarket_order_id_value">
					<a href="<?php echo hikamarket::completeLink('order&task=show&cid='.$order->order_id); ?>"><?php echo $order->order_number; ?></a>
					<?php if(!empty($order->order_invoice_id)) echo ' - ' . $order->order_invoice_number; ?>
					<?php if(hikamarket::acl('order/show/customer')) echo '<br/>'.$order->user_email; ?>
				</td>
				<td class="hikamarket_order_status_value"><span class="order-label order-label-<?php echo preg_replace('#[^a-z_0-9]#i', '_', str_replace(' ','_',$order->order_status)); ?>"><?php
					echo hikamarket::orderStatus($order->order_status);
				?></span></td>
				<td><?php
					if(hikamarket::acl('order/show/billingaddress') && !empty($order->order_billing_address_id)) {
						$full_address = $this->addressClass->maxiFormat($this->addresses[(int)$order->order_billing_address_id], $this->address_fields, true);
						$country = $this->addressClass->miniFormat($this->addresses[(int)$order->order_billing_address_id], $this->address_fields, '{address_city}, {address_state_code_3} {address_country_code_3}');
						echo hikamarket::tooltip($full_address, JText::_('HIKASHOP_BILLING_ADDRESS'), '', $country, '', 0);

						if(!empty($order->order_payment_method))
							echo '<br/>';
					}

					if(!empty($order->order_payment_method)) {
						$payment_price = $this->currencyHelper->format($order->order_payment_price, $order->order_currency_id);
						if(!empty($this->payments[$order->order_payment_id]))
							$payment_name = $this->payments[$order->order_payment_id]->payment_name;
						else
							$payment_name = $order->order_payment_method;

						echo '<span class="'.$label_classes['blue'].'">' .
							hikamarket::tooltip($payment_price, '', '', $payment_name, '', 0) .
							'</span>';
					}
				?></td>
				<td><?php
					if(hikamarket::acl('order/show/shippingaddress') && !empty($order->order_shipping_address_id) && (!empty($order->order_shipping_id) || $order->order_shipping_address_id != $order->order_billing_address_id)) {
						$full_address = $this->addressClass->maxiFormat($this->addresses[(int)$order->order_shipping_address_id], $this->address_fields, true);
						$country = $this->addressClass->miniFormat($this->addresses[(int)$order->order_shipping_address_id], $this->address_fields, '{address_city}, {address_state_code_3} {address_country_code_3}');
						echo hikamarket::tooltip($full_address, JText::_('HIKASHOP_SHIPPING_ADDRESS'), '', $country, '', 0);

						if(!empty($order->shipping_name))
							echo '<br/>';
					}

					if(!empty($order->shipping_name)) {
						if($this->shopConfig->get('price_with_tax'))
							$shipping_price = $this->currencyHelper->format($order->order_shipping_price, $order->order_currency_id);
						else
							$shipping_price = $this->currencyHelper->format($order->order_shipping_price - @$order->order_shipping_tax, $order->order_currency_id);

						echo '<span class="'.$label_classes['blue'].'">';
						if(is_string($order->shipping_name)) {
							echo hikamarket::tooltip($shipping_price, '', '', $order->shipping_name, '', 0);
						} else
							echo hikamarket::tooltip('- '.implode('<br/>- ',$order->shipping_name), JText::_('SHIPPING_PRICE').': '.$shipping_price, '', '<em>'.JText::_('HIKAM_SEVERAL_SHIPPING').' &raquo;</em>', '', 0);
						echo '</span>';
					}
				?></td>
				<td class="hikamarket_order_total_value"><?php
					echo $this->currencyHelper->format($order->order_full_price, $order->order_currency_id);
					if(!empty($order->order_discount_code)) {
						if($this->shopConfig->get('price_with_tax'))
							$discount_value = $this->currencyHelper->format($order->order_discount_price, $order->order_currency_id);
						else
							$discount_value = $this->currencyHelper->format($order->order_discount_price - @$order->order_discount_tax, $order->order_currency_id);

						echo '<br/><span class="'.$label_classes['grey'].'">' .
							hikamarket::tooltip($discount_value, JText::_('HIKASHOP_COUPON'), '', $this->escape($order->order_discount_code), '', 0) .
							'</span>';
					}
				?></td>
				<td class="hikamarket_order_date_value"><?php
					echo str_replace('_', '<br/>', hikamarket::getDate($order->order_created,'%Y-%m-%d_%H:%M'));
				?></td>
			</tr>
<?php } ?>
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
