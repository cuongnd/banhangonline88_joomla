<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DEAL_LIST_HEADER'); ?></h3>
<?php if(empty($this->deals)): ?>
<?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_NO_DEAL_FOUND_MESSAGE'); ?>
<?php
else:
	$count = 1;
?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_ID'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_NAME'); ?></th>
					<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_STATUS'); ?></th>
					<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_BOUGHT'); ?></th>
					<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_ACTION'); ?></th>
				</tr>
			</thead>
			<tbody>
		<?php foreach($this->deals as $deal): ?>
		<?php
		$status = CMGroupBuyingHelperDeal::generateDealStatus($deal['id']);

		if($count % 2 == 0):
			echo '<tr class="row1">';
		else:
			echo '<tr class="row0">';
		endif;

		$count++;
		?>
					<td class="center"><?php echo $deal['id']; ?></td>
					<td><?php echo $deal['name']; ?></td>
					<td class="center hidden-phone"><?php echo $status; ?></td>
					<td class="center hidden-phone"><?php echo CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']); ?></td>
					<td class="center">
					<?php if($status == JText::_('COM_CMGROUPBUYING_DEAL_STATUS_PENDING')): ?>
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=deal_submission&id=' . $deal['id']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_EDIT_DEAL'); ?></a>
					<?php elseif($status == JText::_('COM_CMGROUPBUYING_DEAL_STATUS_ON_SALE')): ?>
					<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_VIEW_DEAL'); ?></a>
					</td>
					<?php endif; ?>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>