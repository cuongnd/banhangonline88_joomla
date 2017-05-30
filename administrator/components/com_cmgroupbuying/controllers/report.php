<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

jimport('joomla.application.component.controller');

class CMGroupBuyingControllerReport extends JControllerLegacy
{
	public function download()
	{
		$jinput = JFactory::getApplication()->input;
		$report = $jinput->get('report', '', 'word');

		if($report == 'deal')
		{
			$dealId = $jinput->get('deal_id', 0, 'int');
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($dealId);

			if(!empty($deal))
			{
				$items = JModelLegacy::getInstance('OrderItem','CMGroupBuyingModel')->getItemForReport($dealId);
				$filename = JText::_('COM_CMGROUPBUYING_REPORT_DEAL_REPORT_FILE_NAME') . date('Ymd') . ".xls";
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<table border="1">
	<tr>
		<td colspan="11"><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $deal['name']); ?></td>
	</tr>
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
		$option = JModelLegacy::getInstance('DealOption', 'CMGroupBuyingModel')->getOption($item['deal_id'], $item['option_id']);
	?>
		<tr>
			<td><?php echo $count; ?></td>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo $option['name']; ?></td>
			<td>
				<?php if(isset($buyerInfo->name) && $buyerInfo->name != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_NAME'); ?>: <?php echo $buyerInfo->name; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->first_name) && $buyerInfo->first_name != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_FIRSTNAME'); ?>: <?php echo $buyerInfo->first_name; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->last_name) && $buyerInfo->last_name != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_LASTNAME'); ?>: <?php echo $buyerInfo->last_name; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->address) && $buyerInfo->address != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_ADDRESS'); ?>: <?php echo $buyerInfo->address; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->city) && $buyerInfo->city != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_CITY'); ?>: <?php echo $buyerInfo->city; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->state) && $buyerInfo->state != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_STATE'); ?>: <?php echo $buyerInfo->state; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->zip_code) && $buyerInfo->zip_code != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_ZIP'); ?>: <?php echo $buyerInfo->zip_code; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->phone) && $buyerInfo->phone != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_PHONE'); ?>: <?php echo $buyerInfo->phone; ?>&#10;
				<?php endif; ?>

				<?php if(isset($buyerInfo->email) && $buyerInfo->email != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_EMAIL'); ?>: <?php echo $buyerInfo->email; ?>&#10;
				<?php endif; ?>
				</ul>
			</td>
			<td>
				<?php if($friendInfo->full_name != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>: <?php echo $friendInfo->full_name; ?>&#10;
				<?php endif; ?>
				<?php  if($friendInfo->email != ''): ?>
				<?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>: <?php echo $friendInfo->email; ?>&#10;
				<?php endif; ?>
			</td>
			<td><?php echo $item['quantity']; ?></td>
			<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($item['quantity'] * $item['unit_price'] + $item['quantity'] * $item['shipping_cost']); ?></td>
			<td><?php echo $order['created_date']; ?></td>
			<td><?php if($order['paid_date'] != '0000-00-00 00:00:00') echo $order['paid_date']; ?></td>
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
			<td>
			<?php
			$coupons = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($item['id']);
			if(count($coupons) == 0):
				echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			else:
			?>
				<?php foreach($coupons as $coupon): ?>
					<?php echo $coupon['coupon_code']; ?>&#10;
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
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				exit(0);
			}
		}
		elseif($report == 'partner')
		{
			$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
			$partnerId = $jinput->get('partner_id', 0, 'int');
			$partner = JModelLegacy::getInstance('Partner','CMGroupBuyingModel')->getPartnerById($partnerId);

			if(!empty($partner))
			{
				$deals = JModelLegacy::getInstance('Deals','CMGroupBuyingModel')->getDealsByPartnerId($partnerId);
				$filename = JText::_('COM_CMGROUPBUYING_REPORT_PARTNER_REPORT_FILE_NAME') . date('Ymd') . ".xls";
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<table border="1">
	<tr>
		<td colspan="5"><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $partner['name']); ?></td>
	</tr>
	<tr>
		<th>#</th>
		<th><?php echo JTEXT::_('COM_CMGROUPBUYING_DEAL_FIELD_NAME_LABEL'); ?></th>
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
			$tipped = '';
		}
	?>
	<tr>
		<td><?php echo $count; ?></td>
		<td><?php echo $deal['name']; ?></td>
		<td><?php echo $numOfOrders; ?></td>
		<td><?php echo $numOfCoupons; ?></td>
		<td><?php echo $tipped; ?></td>
		<td class="center_aligned"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($dealTotal, $configuration); ?></td>
	</tr>
	<?php
	$count++;
	endforeach;
	?>
	<tr>
		<td colspan="5"><?php echo JText::_('COM_CMGROUPBUYING_TOTAL'); ?></td>
		<td class="center_aligned"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($partnerTotal, $configuration); ?></td>
	</tr>
</table>
<?php
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				exit(0);
			}
		}
		elseif($report == 'aggregator_site')
		{
			$siteId = $jinput->get('site_id', 0, 'int');
			$aggregatorSite = JModelLegacy::getInstance('AggregatorSite ','CMGroupBuyingModel')->getAggregatorSiteById($siteId);

			if(!empty($aggregatorSite))
			{
				$deals = CMGroupBuyingHelperDeal::getDealsByRefId($aggregatorSite['ref']);
				$filename = JText::_('COM_CMGROUPBUYING_REPORT_AGG_SITE_REPORT_FILE_NAME') . date('Ymd') . ".xls";
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<table border="1">
	<tr>
		<td colspan="3"><?php echo JTEXT::sprintf('COM_CMGROUPBUYING_REPORT_TITLE_MESSAGE', $aggregatorSite['name']); ?></td>
	</tr>
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
			<td><?php echo $count; ?></td>
			<td><?php echo $deal['name']; ?></td>
			<td><?php echo $deal['view']; ?></td>
		</tr>
	<?php
	$count++;
	endforeach;
	?>
</table>
<?php
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				exit(0);
			}
		}
		else
		{
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=reports';
			$this->setRedirect($redirectUrl);
			$this->redirect();
		}
	}
}