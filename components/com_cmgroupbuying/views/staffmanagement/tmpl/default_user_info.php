<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$order = $this->order;
?>
<div class="row-fluid">
	<div class="span12">
		<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_CHANGE_USER_INFO'); ?></h3>
	<?php
	if(empty($order)):
		echo JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
	else:
		$buyerInfo = json_decode($order['buyer_info']);
		$friendInfo = json_decode($order['friend_info']);
		$orderLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $order['id']);
	?>
		<p><?php echo JText::sprintf('COM_CMGROUPBUYING_STAFF_USER_INFO_MESSAGE',
				'<a href="' . $orderLink . '">' . $order['id'] . '</a>'); ?></p>
		<form action="index.php" method="post" name="adminForm" id="user-info-form" class="form-horizontal">
			<input type="hidden" name="option" value="com_cmgroupbuying" />
			<input type="hidden" name="task" value="change_user_info" />
			<input type="hidden" name="controller" value="staffmanagement" />
			<input type="hidden" name="id" value="<?php echo $order['id']; ?>" />
			<div class="control-group">
				<h4><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_INFO'); ?></h4>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_NAME'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_name" value="<?php echo isset($buyerInfo->name) ? $buyerInfo->name : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_FIRSTNAME'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_first_name" value="<?php echo isset($buyerInfo->first_name) ? $buyerInfo->first_name : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_LASTNAME'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_last_name" value="<?php echo isset($buyerInfo->last_name) ? $buyerInfo->last_name : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_ADDRESS'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_address" value="<?php echo isset($buyerInfo->address) ? $buyerInfo->address : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_CITY'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_city" value="<?php echo isset($buyerInfo->city) ? $buyerInfo->city : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_STATE'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_state" value="<?php echo isset($buyerInfo->state) ? $buyerInfo->state : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_ZIP'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_zip_code" value="<?php echo isset($buyerInfo->zip_code) ? $buyerInfo->zip_code : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_PHONE'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_phone" value="<?php echo isset($buyerInfo->phone) ? $buyerInfo->phone : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_USER_EMAIL'); ?></label>
				<div class="controls">
					<input type="text" name="buyer_email" value="<?php echo isset($buyerInfo->email) ? $buyerInfo->email : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<h4><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></h4>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?></label>
				<div class="controls">
					<input type="text" name="receiver_full_name" value="<?php echo isset($friendInfo->full_name) ? $friendInfo->full_name : ''; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?></label>
				<div class="controls">
					<input type="text" name="receiver_email" value="<?php echo isset($friendInfo->email) ? $friendInfo->email : ''; ?>">
				</div>
			</div>
			<div class="pull-right actions">
				<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_SAVE'); ?>" class="btn btn-primary">
				<a href="<?php echo $orderLink; ?>" class="btn btn-warning"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></a>
			</div>
		</form>
	<?php
	endif;
	?>
	</div>
</div>
