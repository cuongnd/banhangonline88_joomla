<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$order = $this->order;
$items = $this->items;
$configuration = $this->configuration;
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<table class="order_detail_table">
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></td>
		<td><?php echo $order['id']; ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ITEMS'); ?></td>
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
									echo '<li><a href="' . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=coupon&download=' . $coupon['coupon_code']) . '">' . $coupon['coupon_code'] . '</a></li>';
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
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_REFERRER'); ?></td>
		<td><?php echo CMGroupBuyingHelperAlphauserpoints::getUserNameByReferrerID($order['referrer']); ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE'); ?></td>
		<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($order['value']); ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_POINTS'); ?></td>
		<td><?php echo $order['points']; ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_INFO'); ?></td>
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
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></td>
		<td>
			<ul>
				<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $friendInfo->full_name; ?></li>
				<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $friendInfo->email; ?></li>
			</ul>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME'); ?></td>
		<td><?php echo $order['payment_name']; ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_CREATED_DATE'); ?></td>
		<td><?php echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['created_date'], $configuration['datetime_format']); ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_EXPIRED_DATE'); ?></td>
		<td><?php if($order['expired_date'] != '0000-00-00 00:00:00') echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['expired_date'], $configuration['datetime_format']); ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_DATE'); ?></td>
		<td><?php if($order['paid_date'] != '0000-00-00 00:00:00') echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $configuration['datetime_format']); ?></td>
	</tr>
	<tr>
		<td class="title"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_STATUS'); ?></td>
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