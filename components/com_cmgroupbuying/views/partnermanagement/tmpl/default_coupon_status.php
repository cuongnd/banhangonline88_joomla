<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$couponCode = JFactory::getApplication()->input->post->get('coupon_code', '');
?>

<?php if($couponCode == '' && isset($_POST['coupon_code'])): ?>
<div class="alert alert-error">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<?php echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON'); ?>
</div>
<?php endif; ?>
<?php
$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_status');
?>
<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_COUPON_STATUS_HEADER'); ?></h3>
<p><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_CHECK_COUPON_CODE_MESSAGE'); ?></p>
<form class="form-inline" name="partner_form" method="post" action="<?php echo $link; ?>">
	<input type="hidden" name="option" value="com_cmgroupbuying" />
	<input type="hidden" name="view" value="partnermanagement" />
	<input type="hidden" name="navigation" value="coupon_status" />
	<label><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_COUPON_CODE'); ?></label>
	<input type="text" name="coupon_code" value="<?php echo $couponCode; ?>" />
	<input class="btn btn-primary" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_CHECK_BUTTON'); ?>" />
</form>
<?php
if($couponCode != ''):
	$coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponOfPartner($couponCode, $this->partner['id']);

	if(empty($coupon)):
		echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
	else:
		if(isset($coupon['order_id']) && is_numeric($coupon['order_id']))
		{
			$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($coupon['order_id']);
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);
			$order['deal_name'] = $deal['name'];
			$option = JModelLegacy::getInstance('DealOption','CMGroupBuyingModel')->getOption($deal['id'], $coupon['option_id']);
			$order['option_name'] = $option['name'];
			$order['option_price'] = $option['price'];
			$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
}
?>
<h3><?php echo JText::sprintf('COM_CMGROUPBUYING_PARTNER_COUPON_INFO', $coupon['coupon_code']); ?></h3>
<?php if($this->permissions['change_coupon_status'] == true): ?>
<form name="partner_form" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
	<input type="hidden" name="option" value="com_cmgroupbuying" />
	<input type="hidden" name="controller" value="partner" />
	<input type="hidden" name="coupon_code" value="<?php echo $coupon['coupon_code']; ?>" />
	<input type="hidden" name="task" value="change_coupon_status" />
	<input type="hidden" name="navigation" value="<?php echo $this->navigation; ?>" />
	<?php if ($coupon['coupon_status'] == 1): ?>
	<input class="btn btn-success" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_SET_EXCHANGED_BUTTON'); ?>" />
	<?php elseif ($coupon['coupon_status'] == 2): ?>
	<input class="btn btn-warning" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_SET_WAITING_BUTTON'); ?>" />
	<?php endif; ?>
</form>
<?php endif; ?>
<table class="table table-striped">
	<tr>
		<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></td>
		<td><?php echo $order['id']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_DEAL_NAME'); ?></td>
		<td>
			<?php echo $order['deal_name']; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_OPTION_NAME'); ?></td>
		<td>
			<?php echo $order['option_name'] . " (" . CMGroupBuyingHelperDeal::displayDealPrice($order['option_price']) . ")"; ?>
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

				<?php if($this->permissions['view_buyer_info'] == true): ?>
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
				<li><strong><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?></strong>: <?php echo $buyerInfo->first_name; ?></li>
				<li><strong><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?></strong>: <?php echo $buyerInfo->email; ?></li>
			</ul>
		</td>
	</tr>
	<?php endif; ?> 
	<tr>
		<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME'); ?></td>
		<td><?php echo $order['payment_name']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_TRANSACTION_INFO'); ?></td>
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
<?php endif; ?>
<?php endif; ?>