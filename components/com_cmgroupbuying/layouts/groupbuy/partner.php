<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;

if(isset($this->couponCode))
{
	$couponCode = $this->couponCode;
}
else
{
	$couponCode = '';
}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<p><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_CHECK_COUPON_CODE_MESSAGE'); ?></p>
<form class="form-inline" name="partner_form" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
	<input type="hidden" name="option" value="com_cmgroupbuying" />
	<input type="hidden" name="view" value="partner" />
	<label><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_COUPON_CODE'); ?></label>
	<input type="text" name="coupon_code" value="<?php echo $couponCode; ?>" />
	<input class="btn" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_CHECK_BUTTON'); ?>" />
</form>
<?php
if($this->couponCode != '' && isset($this->coupon)):
	$coupon = $this->coupon;
	$order = $this->order;
	$configuration  = $this->configuration;
?>
	<h3><?php echo JText::sprintf('COM_CMGROUPBUYING_PARTNER_COUPON_INFO', $coupon['coupon_code']); ?></h3>
	<form name="partner_form" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="controller" value="partner" />
		<input type="hidden" name="coupon_code" value="<?php echo $coupon['coupon_code']; ?>" />
		<input type="hidden" name="task" value="change_coupon_status" />
		<?php if ($coupon['coupon_status'] == 1): ?>
		<input class="btn" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_SET_EXCHANGED_BUTTON'); ?>" />
		<?php elseif ($coupon['coupon_status'] == 2): ?>
		<input class="btn" type="submit" name="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_SET_WAITING_BUTTON'); ?>" />
		<?php endif; ?>
	</form>
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
			<td><?php echo JText::_('COM_CMGROUPBUYING_ORDER_TRANSACTION_INFO'); ?></td>
			<td>
			<?php
			$transactionInfo = json_decode($order['transaction_info']);
			if(!empty($transactionInfo)):
			?>
				<ul>
				<?php foreach($transactionInfo as $key=>$value): ?>
					<li><?php echo $key ?>: <?php echo $value; ?></li>
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
		<tr>
			<td colspan="2"><a class="float_right" href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partner'); ?>"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_BACK_TO_LIST'); ?></a></td>
		</tr>
	</table>
<?php elseif(isset($_POST['submit'])): ?>
	<h3><?php echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON'); ?></h3>
	<a class="float_right" href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partner'); ?>"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_BACK_TO_LIST'); ?></a>
<?php else: ?>
	<?php
	$filterStatus = array(
		"-1"=>JText::_('COM_CMGROUPBUYING_ALL_COUPON_STATUS'),
		"0"=>JText::_('COM_CMGROUPBUYING_COUPON_UNPAID_COUPON'),
		"1"=>JText::_('COM_CMGROUPBUYING_COUPON_WAITING_COUPON'),
		"2"=>JText::_('COM_CMGROUPBUYING_COUPON_EXCHANGED_COUPON'),
	);
	$filterStatusOption = array();

	foreach($filterStatus as $key=>$value)
	{
		$option = JHTML::_('select.option', $key, $value);
		array_push($filterStatusOption, $option);
	}
	
	$filterDealOption = array();
	$option = JHTML::_('select.option', "0", JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_ALL_DEALS'));
	array_push($filterDealOption, $option);

	if(!empty($this->deals))
	{
		foreach($this->deals as $deal)
		{
			$option = JHTML::_('select.option', $deal['id'], $deal['name']);
			array_push($filterDealOption, $option);
		}
	}

	$filterDealState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.partner_deal_filter", 'partner_deal_filter', 0);
	$filterStatusState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.partner_status_filter", 'partner_status_filter', 0);
	?>
	<h3><?php echo JText::_('COM_CMGROUPBUYING_COUPON_LIST_TITLE'); ?></h3>
	<form id="partner_filter_form" name="partner_filter_form" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="view" value="partner" />
		<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get('Itemid', '', 'int'); ?>" />
		<?php echo JHTML::_('select.genericList', $filterDealOption, 'filter_deal', null , 'value', 'text', $filterDealState); ?>
		<?php echo JHTML::_('select.genericList', $filterStatusOption, 'filter_status', null , 'value', 'text', $filterStatusState); ?>
		<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_SUBMIT_BUTTON'); ?>" />
	</form>
	<?php if(empty($this->coupons)): ?>
		<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_NO_COUPON_FOUND'); ?>
	<?php else: ?>
		<table class="table table-striped">
			<tr>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE'); ?></th>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></th>
				<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_DEAL_NAME'); ?></th>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_STATUS'); ?></th>
			<?php
			$count = 1;

			foreach($this->coupons as $coupon):

			if($count % 2 == 0):
				echo '<tr class="row1">';
			else:
				echo '<tr class="row0">';
			endif;

			$count++;
			?>
				<td class="center">
					<form name="coupon_code_<?php echo $coupon['coupon_code']; ?>" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
						<input type="hidden" name="option" value="com_cmgroupbuying" />
						<input type="hidden" name="view" value="partner" />
						<input type="hidden" name="coupon_code" value="<?php echo $coupon['coupon_code']; ?>" />
					</form>
					<a href="javascript:void()" onClick="<?php echo "coupon_code_" . $coupon['coupon_code']; ?>.submit();"><?php echo $coupon['coupon_code']; ?></a>
				</td>
				<td class="center"><?php echo $coupon['order_id']; ?></td>
				<td class="center hidden-phone">
					<?php
					if(isset($this->deals[$coupon['deal_id']]))
					{
						$deal = $this->deals[$coupon['deal_id']];
						$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
						$dealName = '<a href="' . $link . '">' . $deal['name'] . '</a>';
						echo $dealName;
					}
					?>
				</td>
				<td class="center">
					<?php
					if($coupon['coupon_status'] == 0)
					{
						echo JText::_('COM_CMGROUPBUYING_COUPON_UNPAID_COUPON');
					}
					elseif($coupon['coupon_status'] == 1)
					{
						echo JText::_('COM_CMGROUPBUYING_COUPON_WAITING_COUPON');
					}
					elseif($coupon['coupon_status'] == 2)
					{
						echo JText::_('COM_CMGROUPBUYING_COUPON_EXCHANGED_COUPON');
					}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
			</tr>
		</table>
		<div class="cmgroupbuying_pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
	<?php endif; ?>
<?php endif; ?>