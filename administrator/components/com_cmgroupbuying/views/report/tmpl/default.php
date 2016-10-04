<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$report = $this->report;

if($report == 'deal'):
	$deal = $this->deal;
	$items = $this->items;

	if(empty($items)):
		echo JTEXT::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
	else:
?>
<div class="cmgroupbuying">
	<h4><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $deal['name']); ?></h4>
	<form name='download_form' method="post">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="report.download" />
		<input type="hidden" name="report" value='<?php echo $report; ?>' />
		<input type="hidden" name="deal_id" value='<?php echo $deal['id']; ?>' />
		<input class="btn" type="submit" value="<?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_EXPORT_BUTTON'); ?>" />
	</form>
	<br />
	<table class="table table-striped">
		<tr>
			<th>#</th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_ID_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_OPTION_NAME_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_BUYER'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_FRIEND'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_QUANTITY_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_VALUE_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_CREATED_DATE_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_PAID_DATE_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_STATUS_LABEL'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_ORDER_COUPON'); ?></th>
		</tr>
		<?php
		$count = 1;
		foreach($items as $item):
			$order = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderById($item['order_id']);
			$buyerInfo = json_decode($order['buyer_info']);
			$friendInfo = json_decode($order['friend_info']);
			$option = JModelLegacy::getInstance('Dealoption', 'CMGroupBuyingModel')->getOption($item['deal_id'], $item['option_id']);
		?>
		<tr>
			<td class="center_aligned"><?php echo $count; ?></td>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo $option['name']; ?></td>
			<td>
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
			<td>
				<ul>
					<?php  if($friendInfo->full_name != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $friendInfo->full_name; ?></li>
					<?php endif; ?>
					<?php  if($friendInfo->email != ''): ?>
					<li><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $friendInfo->email; ?></li>
					<?php endif; ?>
				</ul>
			</td>
			<td class="center_aligned"><?php echo $item['quantity']; ?></td>
			<td class="center_aligned"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($item['quantity'] * $item['unit_price'] + $item['quantity'] * $item['shipping_cost']); ?></td>
			<td class="center_aligned"><?php echo $order['created_date']; ?></td>
			<td class="center_aligned"><?php if($order['paid_date'] != '0000-00-00 00:00:00') echo $order['paid_date']; ?></td>
			<td class="center_aligned"> 
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
			<td class="center_aligned">
			<?php
			$coupons = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($item['id']);
			if(count($coupons) == 0): 
				echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			else:
			?>
				<?php foreach($coupons as $coupon): ?>
					<?php echo $coupon['coupon_code']; ?><br />
				<?php endforeach; ?>
			<?php endif; ?>
			</td>
		</tr>
		<?php
		$count++;
		endforeach;
		?>
	</table>
	<?php
		endif;
	elseif($report == 'partner'):
		$deals = $this->deals;
		$partner = $this->partner;
		if(empty($deals)):
			echo JTEXT::_('COM_CMGROUPBUYING_REPORT_NO_DEAL_FOUND');
		else:
	?>
	<h4><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $partner['name']); ?></h4>
	<form name='download_form' method="post">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="report.download" />
		<input type="hidden" name="report" value='<?php echo $report; ?>' />
		<input type="hidden" name="partner_id" value='<?php echo $partner['id']; ?>' />
		<input class="btn" type="submit" value="<?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_EXPORT_BUTTON'); ?>" />
	</form>
	<br />
	<table class="table table-striped">
		<tr>
			<th>#</th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_DEAL_NAME'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_DEAL_NUMBER_OF_PAID_ORDERS'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_DEAL_NUMBER_OF_COUPONS'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_TIPPED'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_COMMISSION'); ?></th>
		</tr>
		<?php
		$count = 1;
		$partnerTotal = 0;

		foreach($deals as $deal):
			$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
			$couponTotal = $dealTotal = 0;
			$numOfOrders = CMGroupBuyingHelperDeal::countPaidItemForReport($deal['id']);

			foreach($optionsOfDeal as $option)
			{
				if($deal['advance_payment'])
					$value = $option['advance_price'];
				else
					$value = $option['price'];

				$numOfCoupons = CMGroupBuyingHelperDeal::countPaidOption($deal['id'], $option['option_id']);
				$couponTotal += $numOfCoupons;
				$partnerCommission = $value * $numOfCoupons * (100 - $deal['commission_rate']) / 100;
				$dealTotal += $partnerCommission;
			}

			$partnerTotal += $dealTotal;

			if($deal['tipped'] == 1)
			{
				$tipped = JTEXT::_('COM_CMGROUPBUYING_TIPPED');
			}
			else
			{
				$tipped =  '';
			}
		?>
		<tr>
			<td class="center_aligned"><?php echo $count; ?></td>
			<td><?php echo $deal['name']; ?></td>
			<td class="center_aligned"><?php echo $numOfOrders; ?></td>
			<td class="center_aligned"><?php echo $numOfCoupons; ?></td>
			<td class="center_aligned"><?php echo $tipped; ?></td>
			<td class="center_aligned"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($dealTotal, $this->configuration); ?></td>
		</tr>
		<?php
		$count++;
		endforeach;
		?>
		<tr>
			<td colspan="5"><?php echo JText::_('COM_CMGROUPBUYING_TOTAL'); ?></td>
			<td class="center_aligned"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($partnerTotal, $this->configuration); ?></td>
		</tr>
	</table>
	<?php
		endif;
	elseif($report == 'aggregator_site'):
		$deals = $this->deals;
		$aggregatorSite = $this->aggregatorSite;
		if(empty($deals)):
			echo JTEXT::_('COM_CMGROUPBUYING_REPORT_NO_DEAL_FOUND');
		else:
	?>
	<h4><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $aggregatorSite['name']); ?></h4>
	<form name='download_form' method="post">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="report.download" />
		<input type="hidden" name="report" value='<?php echo $report; ?>' />
		<input type="hidden" name="site_id" value='<?php echo $aggregatorSite['id']; ?>' />
		<input class="btn" type="submit" value="<?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_EXPORT_BUTTON'); ?>" />
	</form>
	<br />
	<table class="table table-striped">
		<tr>
			<th>#</th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_DEAL_NAME'); ?></th>
			<th><?php echo JTEXT::_('COM_CMGROUPBUYING_REPORT_VIEW'); ?></th>
		</tr>
		<?php
		$count = 1;

		foreach($deals as $deal):
		?>
		<tr>
			<td class="center_aligned"><?php echo $count; ?></td>
			<td><?php echo $deal['name']; ?></td>
			<td class="center_aligned"><?php echo $deal['view']; ?></td>
		</tr>
		<?php
		$count++;
		endforeach;
		?>
	</table>
</div>
<?php
	endif;
endif;