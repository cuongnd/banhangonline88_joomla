<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$order          = $this->order;
$configuration  = $this->configuration;
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_ORDER_DETAIL_HEADER'); ?></h3>
	<?php
	if(empty($order)):
		echo JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
	else:
		$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

		if(!empty($items))
		{
			foreach($items as $key=>$item)
			{
				$deal = CMGroupBuyingHelperDeal::getDealByItemId($item['id']);

				if(empty($deal))
				{
					$item['option_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					$item['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
				}
				else
				{
					$option = JModelLegacy::getInstance('DealOption', 'CMGroupBuyingModel')->getOption($item['deal_id'], $item['option_id']);

					if(empty($option))
					{
						$item['option_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					}
					else
					{
						$item['option_name'] = $option['name'];
					}

					if(empty($deal))
					{
						$item['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
					}
					else
					{
						$item['deal_name'] = $deal['name'];
						$item['coupons'] = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($item['id']);
					}
				}

				$items[$key] = $item;
			}
		}
	?>
		<div class="row-fluid">
			<div class="span12 actions btn-toolbar">
				<?php if($this->permissions['change_order_to_paid'] == true
						|| $this->permissions['change_order_to_unpaid'] == true): ?>
				<div class="pull-right btn-group">
					<form class="form-inline" id="order-status-form" method="post" action="<?php echo JRoute::_('index.php', true); ?>">
						<input type="hidden" name="option" value="com_cmgroupbuying" />
						<input type="hidden" name="controller" value="staffmanagement" />
						<input type="hidden" name="id" value="<?php echo $order['id']; ?>" />
						<?php if($order['status'] == 0 && $this->permissions['change_order_to_paid'] == true): ?>
						<input type="hidden" name="task" value="set_paid" />
						<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_STAFF_ORDER_TO_PAID'); ?>" class="btn btn-primary btn-small">
						<?php elseif($order['status'] == 1 && $this->permissions['change_order_to_unpaid'] == true): ?>
						<input type="hidden" name="task" value="set_unpaid" />
						<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_STAFF_ORDER_TO_UNPAID'); ?>" class="btn btn-warning btn-small">
						<?php endif; ?>
					</form>
				</div>
				<?php endif; ?>
				<?php if($this->permissions['change_user_info'] == true): ?>
				<div class="pull-right btn-group">
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=user_info&id=' . $order['id']); ?>" class="btn btn-small"><?php echo JText::_('COM_CMGROUPBUYING_STAFF_CHANGE_USER_INFO'); ?></a>
				</div>
				<?php endif; ?>
				<?php if($this->permissions['send_coupon'] == true && $order['status'] == 1): ?>
				<div class="pull-right btn-group">
					<form method="POST" action="index.php?option=com_cmgroupbuying&controller=staffmanagement&task=send_coupons&id=<?php echo  $order['id']; ?>">
						<input type="submit" class="btn btn-warning btn-small" value="<?php echo JText::_('COM_CMGROUPBUYING_SEND_COUPON'); ?>" />
					</form>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<table class="table table-striped">
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></td>
						<td><?php echo $order['id']; ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEMS'); ?></td>
						<td>
							<?php if(!empty($items)): ?>
							<ol>
								<?php foreach($items as $item): ?>
								<?php
								$unitPrice = CMGroupBuyingHelperDeal::displayDealPrice($item['unit_price'], true, $configuration);
								$shippingCost = CMGroupBuyingHelperDeal::displayDealPrice($item['shipping_cost'], true, $configuration);
								?>
								<li>
									<div><span class="order_item_option_name"><?php echo $item['option_name']; ?></span></div>
									<ul>
										<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_DEAL') . $item['deal_name']; ?></li>
										<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_UNIT_PRICE'); ?><?php echo $unitPrice; ?></li>
										<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_QUANTITY'); ?><?php echo $item['quantity']; ?></li>
										<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_SHIPPING_COST'); ?><?php echo $shippingCost; ?></li>
										<li><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEM_COUPONS'); ?>
											<?php
											if(empty($item['coupons'])):
												echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
											else:
												echo '<ul>';
												foreach($item['coupons'] as $coupon):
													$couponLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_list&id=' . $coupon['coupon_code']);
													echo '<li><a href="' . $couponLink . '">' . $coupon['coupon_code'] . '</a></li>';
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
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_REFERRER'); ?></td>
						<td><?php echo CMGroupBuyingHelperAlphauserpoints::getUserNameByReferrerID($order['referrer']); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE'); ?></td>
						<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($order['value'], true, $configuration); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_POINTS'); ?></td>
						<td><?php echo $order['points']; ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_INFO'); ?></td>
						<td>
						<?php
						$buyerInfo = json_decode($order['buyer_info']);
						?>
							<ul>
								<?php if(isset($buyerInfo->name) && $buyerInfo->name != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_NAME'); ?>: <?php echo $buyerInfo->name; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->first_name) && $buyerInfo->first_name != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_FIRSTNAME'); ?>: <?php echo $buyerInfo->first_name; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->last_name) && $buyerInfo->last_name != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_LASTNAME'); ?>: <?php echo $buyerInfo->last_name; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->address) && $buyerInfo->address != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_ADDRESS'); ?>: <?php echo $buyerInfo->address; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->city) && $buyerInfo->city != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_CITY'); ?>: <?php echo $buyerInfo->city; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->state) && $buyerInfo->state != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_STATE'); ?>: <?php echo $buyerInfo->state; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->zip_code) && $buyerInfo->zip_code != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_ZIP'); ?>: <?php echo $buyerInfo->zip_code; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->phone) && $buyerInfo->phone != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_PHONE'); ?>: <?php echo $buyerInfo->phone; ?></li>
								<?php endif; ?>

								<?php if(isset($buyerInfo->email) && $buyerInfo->email != ''): ?>
								<li><?php echo JText::_('COM_CMGROUPBUYING_USER_EMAIL'); ?>: <?php echo $buyerInfo->email; ?></li>
								<?php endif; ?>
							</ul>
						</td>
					</tr>
					<?php
					$friendInfo = json_decode($order['friend_info']);
					if((isset($friendInfo->email) && $friendInfo->email != '')
						&& (isset($friendInfo->full_name) && $friendInfo->full_name != '')):
					?>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></td>
						<td>
							<ul>
								<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $friendInfo->full_name; ?></li>
								<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $friendInfo->email; ?></li>
							</ul>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME'); ?></td>
						<td><?php echo $order['payment_name']; ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_CREATED_DATE'); ?></td>
						<td><?php echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['created_date'], $configuration['datetime_format']); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_EXPIRED_DATE'); ?></td>
						<td><?php if($order['expired_date'] != '0000-00-00 00:00:00') echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['expired_date'], $configuration['datetime_format']); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_DATE'); ?></td>
						<td><?php if($order['paid_date'] != '0000-00-00 00:00:00') echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_STATUS'); ?></td>
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
		</div>
	<?php
	endif;
	?>
	</div>
</div>
