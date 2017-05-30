<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

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

foreach($this->deals as $deal)
{
	$option = JHTML::_('select.option', $deal['id'], $deal['name']);
	array_push($filterDealOption, $option);
}

$filterDealState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.partner_deal_filter", 'partner_deal_filter', 0);
$filterStatusState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.partner_status_filter", 'partner_status_filter', 0);

$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_list');
$statusLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=coupon_status');
?>
<h3><?php echo JText::_('COM_CMGROUPBUYING_COUPON_LIST_TITLE'); ?></h3>
<form class="form-inline" id="partner_filter_form" name="partner_filter_form" method="post" action="<?php echo $link; ?>">
	<?php echo JHTML::_('select.genericList', $filterDealOption, 'filter_deal', null , 'value', 'text', $filterDealState); ?>
	<?php echo JHTML::_('select.genericList', $filterStatusOption, 'filter_status', null , 'value', 'text', $filterStatusState); ?>
	<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_SUBMIT_BUTTON'); ?>" />
</form>
<?php if(empty($this->coupons)): ?>
	<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_NO_COUPON_FOUND'); ?>
<?php else: ?>
	<table class="table table-striped coupon-list">
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
				<form name="coupon_code_<?php echo $coupon['coupon_code']; ?>" method="post" action="<?php echo $statusLink; ?>">
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