<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
$count = 1;
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<?php if(empty($this->coupons)): ?>
<?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_NO_COUPON_FOUND_MESSAGE'); ?>
<?php else: ?>
<div class="row-fluid">
	<div class="span12">
		<div class="pull-right">
			<a class="btn btn-primary" href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecouponsubmission'); ?>"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_NEW_COUPON'); ?></a>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<tr>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_ID'); ?></th>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_NAME'); ?></th>
				<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_STATUS'); ?></th>
				<th class="nowrap center hidden-phone"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_GOT'); ?></th>
				<th class="nowrap center"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_COUPON_ACTION'); ?></th>
			</tr>
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
			<?php if($status == JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_PENDING')): ?>
			<td class="center"><a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecouponsubmission&id=' . $coupon['id']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_EDIT_COUPON'); ?></a></td>
			<?php else: ?>
			<td class="center"><a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecoupon&id=' . $coupon['id'] . '&alias=' . $coupon['alias']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGEMENT_VIEW_COUPON'); ?></a></td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>
		</table>
	</div>
</div>
<?php endif; ?>