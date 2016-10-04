<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$filterBuyerState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.staff_coupon_buyer_filter", "staff_coupon_buyer_filter", '');
$filterCodeState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.staff_coupon_code_filter", "staff_coupon_code_filter", '');
$filterDealState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.staff_coupon_deal_filter", "staff_coupon_deal_filter", '');
$filterStatusState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.staff_coupon_status_filter", "staff_coupon_status_filter", '-1');

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

$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_list');
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_COUPON_LIST_HEADER'); ?></h3>
		<form class="form-inline" id="staff_filter_form" name="staff_filter_form" method="post" action="<?php echo $link; ?>">
			<input type="text" name="filter_code" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE') ?>" title="<?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE') ?>" value="<?php echo $filterCodeState; ?>"/>
			<input type="text" name="filter_buyer" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_NAME_OR_USERNAME') ?>" title="<?php echo JText::_('COM_CMGROUPBUYING_NAME_OR_USERNAME') ?>" value="<?php echo $filterBuyerState; ?>"/>
			<input type="text" name="filter_deal" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_NAME') ?>" title="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_NAME') ?>" value="<?php echo $filterDealState; ?>"/>
			<?php echo JHTML::_('select.genericList', $filterStatusOption, 'filter_status', null , 'value', 'text', $filterStatusState); ?>
			<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_SUBMIT_BUTTON'); ?>" />
		</form>
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_BUYER_NAME'); ?></th>
					<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_DEAL_NAME'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_STATUS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->coupons as $coupon): ?>
				<tr>
					<td class="nowrap center">
						<?php
						$couponLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_list&id=' . $coupon['coupon_code']);
						echo '<a href="' . $couponLink . '">' . $coupon['coupon_code'] . '</a>';
						?>
					</td>
					<td class="nowrap center">
						<?php
						$orderLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $coupon['order_id']);
						echo '<a href="' . $orderLink . '">' . $coupon['order_id'] . '</a>';
						?>
					</td>
					<td class="nowrap center">
						<?php
						if($coupon['user_id'] == 0)
						{
							echo JText::_('COM_CMGROUPBUYING_GUEST');
						}
						else
						{
							$user = JFactory::getUser($coupon['user_id']);
							if(!empty($user))
							{
								echo $user->username;
							}
						}
						?>
					</td>
					<td class="nowrap center hidden-phone">
						<?php
						$deal       = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);
						$dealLink   = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
						echo '<a href="' . $dealLink . '">' . $deal['name'] . '</a>';
						?>
					</td>
					<td class="nowrap center">
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
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo $this->pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>