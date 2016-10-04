<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('bootstrap.tooltip');
}

$order = $this->order;
$buyerInfo  = json_decode($order['buyer_info']);
$friendInfo = json_decode($order['friend_info']);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'order.save_user_info')
		{
			Joomla.submitform(task, document.getElementById('user-info-form'));
		}
	}
</script>
<div class="cmgroupbuying">
	<form action="index.php" method="post" name="adminForm" id="user-info-form" class="form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="configuration" />
		<input type="hidden" name="id" value="<?php echo $order['id']; ?>" />
		<h3><?php echo JText::_('COM_CMGROUPBUYING_ORDER_BUYER_INFO'); ?></h3>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_NAME'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_name" value="<?php echo isset($buyerInfo->name) ? $buyerInfo->name : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_FIRSTNAME'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_first_name" value="<?php echo isset($buyerInfo->first_name) ? $buyerInfo->first_name : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_LASTNAME'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_last_name" value="<?php echo isset($buyerInfo->last_name) ? $buyerInfo->last_name : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_ADDRESS'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_address" value="<?php echo isset($buyerInfo->address) ? $buyerInfo->address : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_CITY'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_city" value="<?php echo isset($buyerInfo->city) ? $buyerInfo->city : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_STATE'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_state" value="<?php echo isset($buyerInfo->state) ? $buyerInfo->state : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_ZIP'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_zip_code" value="<?php echo isset($buyerInfo->zip_code) ? $buyerInfo->zip_code : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_PHONE'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_phone" value="<?php echo isset($buyerInfo->phone) ? $buyerInfo->phone : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_BUYER_EMAIL'); ?>
			</div>
			<div class="controls">
				<input type="text" name="buyer_email" value="<?php echo isset($buyerInfo->email) ? $buyerInfo->email : ''; ?>" class="inputbox">
			</div>
		</div>
		<h3><?php echo JText::_('COM_CMGROUPBUYING_ORDER_FRIEND_INFO'); ?></h3>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?>
			</div>
			<div class="controls">
				<input type="text" name="receiver_full_name" value="<?php echo isset($friendInfo->full_name) ? $friendInfo->full_name : ''; ?>" class="inputbox">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?>
			</div>
			<div class="controls">
				<input type="text" name="receiver_email" value="<?php echo isset($friendInfo->email) ? $friendInfo->email : ''; ?>" class="inputbox">
			</div>
		</div>
	</form>
</div>