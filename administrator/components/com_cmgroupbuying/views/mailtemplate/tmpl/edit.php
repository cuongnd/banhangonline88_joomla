<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'location.cancel' || document.formvalidator.isValid(document.id('mailtemplate-form'))) {
			Joomla.submitform(task, document.getElementById('mailtemplate-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<div class="cmgroupbuying">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>
		<form action="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="mailtemplate-form" class="form-validate form-horizontal">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('subject'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('subject'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('body'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('body'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_NAMES'); ?>
				</div>
				<div class="controls">
					<ul>
						<?php if($this->item->name == 'pay_buyer'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{order_value}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_VALUE'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{order_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_LINK'); ?></li>
						<?php elseif($this->item->name == 'pay_partner'): ?>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<?php elseif($this->item->name == 'coupon_for_buyer'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{friend_full_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FRIEND_FULL_NAME'); ?></li>
						<li>{friend_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FRIEND_EMAIL'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{coupon_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_COUPON_LINK'); ?></li>
						<li>{order_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_LINK'); ?></li>
						<?php elseif($this->item->name == 'coupon_for_friend'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{friend_full_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FRIEND_FULL_NAME'); ?></li>
						<li>{friend_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FRIEND_EMAIL'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{coupon_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_COUPON_LINK'); ?></li>
						<?php elseif($this->item->name == 'void_buyer'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{order_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_LINK'); ?></li>
						<?php elseif($this->item->name == 'void_partner'): ?>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
						<?php elseif($this->item->name == 'late_pay_buyer'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{order_created_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_CREATED_DATE'); ?></li>
						<li>{order_expired_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_EXPIRED_DATE'); ?></li>
						<li>{order_paid_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_PAID_DATE'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{order_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_LINK'); ?></li>
						<?php elseif($this->item->name == 'tip_partner'): ?>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
						<li>{deal_tipped_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_TIPPED_DATE'); ?></li>
						<?php elseif($this->item->name == 'cash_buyer'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{order_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_LINK'); ?></li>
						<li>{order_value}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_VALUE'); ?></li>
						<li>{order_expired_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_EXPIRED_DATE'); ?></li>
						<?php elseif($this->item->name == 'approve_partner'): ?>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<?php elseif($this->item->name == 'approve_coupon_partner'): ?>
						<li>{coupon_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FREE_COUPON_NAME'); ?></li>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<?php elseif($this->item->name == 'pending_admin'): ?>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
						<?php elseif($this->item->name == 'pending_coupon_admin'): ?>
						<li>{coupon_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_FREE_COUPON_NAME'); ?></li>
						<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
						<?php elseif($this->item->name == 'cash_admin'): ?>
						<li>{buyer_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_NAME'); ?></li>
						<li>{buyer_first_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_FIRST_NAME'); ?></li>
						<li>{buyer_last_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_LAST_NAME'); ?></li>
						<li>{buyer_email}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_BUYER_EMAIL'); ?></li>
						<li>{order_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_ID'); ?></li>
						<li>{order_value}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_ORDER_VALUE'); ?></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>