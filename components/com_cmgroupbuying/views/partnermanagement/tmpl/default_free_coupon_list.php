<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_FREE_COUPON_LIST_HEADER'); ?></h3>
<?php if(empty($this->coupons)): ?>
<?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_NO_COUPON_FOUND_MESSAGE'); ?>
<?php
else:
	$count = 1;
?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_ID'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_NAME'); ?></th>
					<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_STATUS'); ?></th>
					<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_GOT'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_ACTION'); ?></th>
				</tr>
			</thead>
			<tbody>
		<?php foreach($this->coupons as $coupon): ?>
		<?php
		$status = CMGroupBuyingHelperFreeCoupon::generateFreeCouponStatus($coupon['id']);

		if($count % 2 == 0):
			echo '<tr class="row1">';
		else:
			echo '<tr class="row0">';
		endif;

		$count++;
		?>
					<td class="center"><?php echo $coupon['id']; ?></td>
					<td><?php echo $coupon['name']; ?></td>
					<td class="center hidden-phone"><?php echo $status; ?></td>
					<td class="center hidden-phone"><?php echo $coupon['view']; ?></td>
					<td class="center">
					<?php if($status == JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_PENDING')): ?>
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=free_coupon_submission&id=' . $coupon['id']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_EDIT_COUPON'); ?></a>
					<?php else: ?>
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecoupon&id=' . $coupon['id'] . '&alias=' . $coupon['alias']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_VIEW_COUPON'); ?></a>
					</td>
					<?php endif; ?>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>