<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$coupon = $this->coupon;
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_COUPON_DETAIL_HEADER'); ?></h3>
	<?php
	if(empty($coupon)):
		echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
	else:
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($coupon['order_id']);

		if(empty($order))
		{
			echo JText::_('COM_CMGROUPBUYING_STAFF_COUPON_ORDER_NOT_FOUND');
			return;
		}

		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);

		if(empty($deal))
		{
			echo JText::_('COM_CMGROUPBUYING_STAFF_COUPON_DEAL_NOT_FOUND');
			return;
		}

		$option = JModelLegacy::getInstance('DealOption','CMGroupBuyingModel')->getOption($deal['id'], $coupon['option_id']);

		if(empty($option))
		{
			echo JText::_('COM_CMGROUPBUYING_STAFF_COUPON_OPTION_NOT_FOUND');
			return;
		}

	?>
		<div class="row-fluid">
			<div class="span12">
				<?php if($this->permissions['send_coupon'] == true): ?>
				<div class="pull-right actions">
					<form method="POST" action="index.php?option=com_cmgroupbuying&controller=staffmanagement&task=send_coupon&coupon=<?php echo $coupon['coupon_code']; ?>">
						<input type="submit" class="btn btn-warning btn-small" value="<?php echo JText::_('COM_CMGROUPBUYING_SEND_COUPON'); ?>" />
					</form>
				</div>
				<?php endif; ?>
				<?php if($this->permissions['view_coupon'] == true): ?>
				<div class="pull-right">
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_view&id=' . $coupon['coupon_code']); ?>" class="btn btn-primary btn-small"><?php echo JText::_('COM_CMGROUPBUYING_VIEW_COUPON'); ?></a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<table class="table table-striped">
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_DEAL'); ?></td>
						<td>
						<?php
						$dealLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
						echo '<a href="' . $dealLink . '">' . $deal['name'] . '</a>';
						?>
						</td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_OPTION_NAME'); ?></td>
						<td>
							<?php echo $option['name'] . " (" . CMGroupBuyingHelperDeal::displayDealPrice($option['price']) . ")"; ?>
						</td>
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

					if($friendInfo->email != '' && $friendInfo->full_name != ''):
					?>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></td>
						<td>
							<ul>
								<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $buyerInfo->first_name; ?></li>
								<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $buyerInfo->email; ?></li>
							</ul>
						</td>
					</tr>
					<?php endif; ?> 
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME'); ?></td>
						<td><?php echo $order['payment_name']; ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></td>
						<td>
						<?php
						$orderLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $coupon['order_id']);
						echo '<a href="' . $orderLink . '">' . $coupon['order_id'] . '</a>';
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
