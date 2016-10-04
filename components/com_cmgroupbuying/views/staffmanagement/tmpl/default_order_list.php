<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;


JFactory::getDocument()->addScript('media/system/js/mootools-core.js');

$app = JFactory::getApplication();
$payments = CMGroupBuyingHelperPlugin::getPaymentPlugins();

$filterOrderStatus = array(
	"-1"=>JText::_('COM_CMGROUPBUYING_ALL_ORDER_STATUS'),
	"0"=>JText::_('COM_CMGROUPBUYING_ORDER_UNPAID_ORDER'),
	"1"=>JText::_('COM_CMGROUPBUYING_ORDER_PAID_ORDER'),
	"2"=>JText::_('COM_CMGROUPBUYING_ORDER_LATE_PAID_ORDER'),
	"4"=>JText::_('COM_CMGROUPBUYING_ORDER_REFUNDED_ORDER')
);
$filterOrderOption = array();

foreach($filterOrderStatus as $key=>$value)
{
	array_push($filterOrderOption, JHTML::_('select.option', $key, $value));
}

$filterPaymentGateway = array();
array_push($filterPaymentGateway, JHTML::_('select.option', '', JText::_('COM_CMGROUPBUYING_ALL_PAYMENT_GATEWAYS')));

foreach($payments as $payment)
{
	array_push($filterPaymentGateway, JHTML::_('select.option', $payment['name'], $payment['name']));
}

array_push($filterPaymentGateway, JHTML::_('select.option', '-1', JText::_('COM_CMGROUPBUYING_NO_PAYMENT_GATEWAY_USED')));

$filterDate = array(
	""=>JText::_('COM_CMGROUPBUYING_STAFF_DATE_FILTER_NONE'),
	"created_date"=>JText::_('COM_CMGROUPBUYING_STAFF_DATE_FILTER_CREATED_DATE'),
	"paid_date"=>JText::_('COM_CMGROUPBUYING_STAFF_DATE_FILTER_PAID_DATE'),
);
$filterDateOption = array();

foreach($filterDate as $key=>$value)
{
	array_push($filterDateOption, JHTML::_('select.option', $key, $value));
}

$filterBuyerState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_buyer_filter", "staff_order_buyer_filter", '');
$filterStatusState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_status_filter", "staff_order_status_filter", '-1');
$filterGatewayState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_gateway_filter", "staff_order_gateway_filter", '');
$filterDateState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_date_filter", "staff_order_date_filter", '');
$filterFromState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_from_filter", "staff_order_from_filter", '');
$filterToState = $app->getUserStateFromRequest("cmgroupbuying.staff_order_to_filter", "staff_order_to_filter", '');

$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list');
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_ORDER_LIST_HEADER'); ?></h3>
		<form class="form-inline" id="staff_filter_form" name="staff_filter_form" method="post" action="<?php echo $link; ?>">
			<input type="text" name="filter_buyer" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_NAME_OR_USERNAME') ?>" title="<?php echo JText::_('COM_CMGROUPBUYING_NAME_OR_USERNAME') ?>" value="<?php echo $filterBuyerState; ?>"/>
			<?php echo JHTML::_('select.genericList', $filterOrderStatus, 'filter_status', null , 'value', 'text', $filterStatusState); ?>
			<?php echo JHTML::_('select.genericList', $filterPaymentGateway, 'filter_gateway', null , 'value', 'text', $filterGatewayState); ?>
			<?php echo JHTML::_('select.genericList', $filterDate, 'filter_date', null , 'value', 'text', $filterDateState); ?>
			<?php echo JHTML::_('calendar', $filterFromState, 'filter_from', 'filter_from', '%Y-%m-%d', array('placeholder' => JText::_('COM_CMGROUPBUYING_STAFF_DATE_FILTER_FROM'), 'readonly' => '')); ?>
			<?php echo JHTML::_('calendar', $filterToState, 'filter_to', 'filter_to', '%Y-%m-%d', array('placeholder' => JText::_('COM_CMGROUPBUYING_STAFF_DATE_FILTER_TO'), 'readonly' => '')); ?>
			<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_SUBMIT_BUTTON'); ?>" />
		</form>
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></th>
					<th class="nowrap center"><?php echo JText::_('JGLOBAL_USERNAME'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_BUYER_NAME'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_STATUS'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_NAME'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_CREATED_DATE'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_DATE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->orders as $order): ?>
				<tr>
					<td class="nowrap center">
						<?php
						$orderLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $order['id']);
						echo '<a href="' . $orderLink . '">' . $order['id'] . '</a>';
						?>
					</td>
					<td class="nowrap center">
						<?php
						if($order['buyer_id'] == 0)
						{
							echo JText::_('COM_CMGROUPBUYING_GUEST');
						}
						else
						{
							$user = JFactory::getUser($order['buyer_id']);
							if(!empty($user))
							{
								echo $user->username;
							}
						}
						?>
					</td>
					<td class="nowrap center">
						<?php
						$buyerInfo = json_decode($order['buyer_info']);
						if(isset($buyerInfo->name) && $buyerInfo->name != '')
						{
							echo $buyerInfo->name;
						}
						elseif(isset($buyerInfo->first_name) && $buyerInfo->first_name != ''
								&& isset($buyerInfo->last_name) && $buyerInfo->last_name != '')
						{
							echo $buyerInfo->first_name . ' ' . $buyerInfo->last_name;
						}
						?>
					</td>
					<td class="nowrap center"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($order['value'], $this->configuration); ?></td>
					<td class="nowrap center">
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
					<td class="nowrap center"><?php echo $order['payment_name']; ?></td>
					<td class="nowrap center"><?php echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['created_date'], $this->configuration['datetime_format']); ?></td>
					<td class="nowrap center">
						<?php
						if($order['paid_date'] != '0000-00-00 00:00:00')
							echo CMGroupBuyingHelperDateTime::changeDateTimeFormat($order['paid_date'], $this->configuration['datetime_format']);
						?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
