<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$coupon = $this->coupon;
$order = $this->order;
?>
<div class="cmgroupbuying">
	<table class="table table-striped">
		<tr>
			<th class="nowrap" width="15%"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE_LABEL'); ?></th>
			<td><?php echo $coupon['coupon_code']; ?></td>
		</tr>
		<tr>
			<th class="nowrap" width="15%"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID_LABEL'); ?></th>
			<td><?php echo $coupon['order_id']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_DEAL_NAME_LABEL'); ?></th>
			<td><?php echo $order['deal_name']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_OPTION_NAME_LABEL'); ?></th>
			<td><?php echo $order['option_name']; ?></td>
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
					<li><strong><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?></strong>: <?php echo $friendInfo->full_name; ?></li>
					<li><strong><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?></strong>: <?php echo $friendInfo->email; ?></li>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_POINTS_LABEL'); ?></th>
			<td><?php echo $order['points']; ?></td>
		</tr>
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_REFERRER_LABEL'); ?></th>
			<td><?php echo CMGroupBuyingHelperAlphauserpoints::getUserNameByReferrerID($order['referrer']); ?></td>
		</tr>
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
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_ORDER_STATUS_LABEL'); ?></th>
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
		<tr>
			<th class="nowrap"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_COUPON_STATUS_LABEL'); ?></th>
			<td>
			<?php
			switch($coupon['coupon_status'])
			{
				case 0:
					echo JText::_('COM_CMGROUPBUYING_COUPON_UNPAID_COUPON');
					break;
				case 1:
					echo JText::_('COM_CMGROUPBUYING_COUPON_WAITING_COUPON');
					break;
				case 2:
					echo JText::_('COM_CMGROUPBUYING_COUPON_EXCHANGED_COUPON');
					break;
			}
			?>
			</td>
		</tr>
	</table>
</div>
<form action="index.php?option=com_cmgroupbuying" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="cid" id="cid" value="<?php echo $coupon['coupon_code']; ?>" />
	<input type="hidden" name="task" value="" />
</form>