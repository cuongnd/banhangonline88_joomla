<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$order = $this->order;
$items = $this->items;
$configuration = $this->configuration;
?>
<div class="cmgroupbuying">
	<table class="table table-striped">
		<tr>
			<th class="nowrap" width="15%"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID_LABEL'); ?></th>
			<td><?php echo $order['id']; ?></td>
		</tr>
		<tr>
			<th class="nowrap" width="15%"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_LABEL'); ?></th>
			<td>
			<?php
			if($order['buyer_id'] == 0)
			{
				echo JText::_('COM_CMGROUPBUYING_GUEST');
			}
			else
			{
				$buyer = JFactory::getUser($order['buyer_id']);
				echo $buyer->username;
			}
			?>
			</td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEMS'); ?></th>
			<td>
				<?php if(!empty($items)): ?>
				<ol>
					<?php foreach($items as $item): ?>
					<?php
					$unitPrice = CMGroupBuyingHelperDeal::displayDealPrice($item['unit_price'], true, $configuration);
					$shippingCost = CMGroupBuyingHelperDeal::displayDealPrice($item['shipping_cost'], true, $configuration);
					?>
					<li>
						<div class="order_item_option_name"><?php echo $item['option_name']; ?></div>
						<ul class="order_item_info">
							<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_DEAL') . $item['deal_name']; ?></li>
							<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_UNIT_PRICE'); ?><?php echo $unitPrice; ?></li>
							<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_QUANTITY'); ?><?php echo $item['quantity']; ?></li>
							<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_SHIPPING_COST'); ?><?php echo $shippingCost; ?></li>
							<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_COUPONS'); ?>
								<?php
								if(empty($item['coupons'])):
									echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON_FOUND');
								else:
									echo '<ul>';
									foreach($item['coupons'] as $coupon):
										echo '<li><a href="index.php?option=com_cmgroupbuying&view=coupon&cid[]=' . $coupon['coupon_code'] . '">' . $coupon['coupon_code'] . '</a></li>';
									endforeach;
									echo '</ul>';
								endif;
								?>
							</li>
						</ul>
					</li>
					<?php endforeach; ?>
				</ol>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_REFERRER_LABEL'); ?></th>
			<td><?php echo $order['referrer_name']; ?></td>
		</tr> 
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE_LABEL'); ?></th>
			<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($order['value'], true, $configuration); ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_POINTS_LABEL'); ?></th>
			<td><?php echo $order['points']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_INFO'); ?></th>
			<td>
			<?php $buyerInfo = json_decode($order['buyer_info']); ?>
				<ul>
					<?php if(isset($buyerInfo->name) && $buyerInfo->name != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_NAME'); ?>: <?php echo $buyerInfo->name; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->first_name) && $buyerInfo->first_name != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_FIRSTNAME'); ?>: <?php echo $buyerInfo->first_name; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->last_name) && $buyerInfo->last_name != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_LASTNAME'); ?>: <?php echo $buyerInfo->last_name; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->address) && $buyerInfo->address != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_ADDRESS'); ?>: <?php echo $buyerInfo->address; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->city) && $buyerInfo->city != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_CITY'); ?>: <?php echo $buyerInfo->city; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->state) && $buyerInfo->state != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_STATE'); ?>: <?php echo $buyerInfo->state; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->zip_code) && $buyerInfo->zip_code != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_ZIP'); ?>: <?php echo $buyerInfo->zip_code; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->phone) && $buyerInfo->phone != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_PHONE'); ?>: <?php echo $buyerInfo->phone; ?></li>
					<?php endif; ?>

					<?php if(isset($buyerInfo->email) && $buyerInfo->email != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_BUYER_EMAIL'); ?>: <?php echo $buyerInfo->email; ?></li>
					<?php endif; ?>
				</ul>
			</td>
		</tr>
		<?php
		$friendInfo = json_decode($order['friend_info']);
		if($friendInfo->email != '' && $friendInfo->full_name != ''):
		?>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></th>
			<td>
				<ul>
					<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $friendInfo->full_name; ?></li>
					<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $friendInfo->email; ?></li>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME_LABEL'); ?></th>
			<td><?php echo $order['payment_name']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_TRANSACTION_INFO'); ?></th>
			<td>
			<?php
			$transactionInfo = json_decode($order['transaction_info']);
			if(!empty($transactionInfo)):
			?>
				<ul>
				<?php foreach($transactionInfo as $key=>$value): ?>
					<li><strong><?php echo $key ?></strong>: <?php echo $value; ?></li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_CREATED_DATE_LABEL'); ?></th>
			<td><?php echo $order['created_date']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_EXPIRED_DATE_LABEL'); ?></th>
			<td><?php echo $order['expired_date']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_DATE_LABEL'); ?></th>
			<td><?php if($order['paid_date'] != '0000-00-00 00:00:00') echo $order['paid_date']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_STATUS_LABEL'); ?></th>
			<td>
			<?php
			switch($order['status'])
			{
				case 0:
					echo JText::_('COM_CMGROUPBUYING_ORDER_UNPAID_ORDER');
					break;
				case 1:
					echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_ORDER');
					break;
				case 2:
					echo JText::_('COM_CMGROUPBUYING_ORDER_LATE_PAID_ORDER');
					break;
				case 3:
					echo JText::_('COM_CMGROUPBUYING_ORDER_DELIVERED_ORDER');
					break;
				case 4:
					echo JText::_('COM_CMGROUPBUYING_ORDER_REFUNDED_ORDER');
					break;
			}
			?>
			</td>
		</tr>
	</table>
</div>
<form action="index.php?option=com_cmgroupbuying" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="cid" id="cid" value="<?php echo $order['id']; ?>" />
	<input type="hidden" name="task" value="" />
</form>