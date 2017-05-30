<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
jimport('joomla.html.html');

if(version_compare(JVERSION, '3.0.0', 'lt')):
?>
<style>
.form-horizontal .control-group {
	clear: both;
}

.form-horizontal .control-group {
	margin-bottom: 40px;
}

.form-horizontal .controls {
	margin-left: 160px;
}

.form-horizontal .control-header {
	float: left;
	margin-bottom: 10px;
}
</style>
<?php endif; ?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'management.cancel' || document.formvalidator.isValid(document.id('management-form')))
		{
			Joomla.submitform(task, document.getElementById('management-form'));
		}
	}
</script>
<div class="cmgroupbuying">
	<form action="index.php" method="post" name="adminForm" id="management-form" class="form-validate form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="management" />
		<input type="hidden" name="id" value="1" />
	<?php if(!empty( $this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10 ">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
			<ul class="nav nav-tabs" id="permissionTab">
				<li class="active"><a data-toggle="tab" href="#partner"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER'); ?></a></li>
				<li><a data-toggle="tab" href="#staff"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF'); ?></a></li>
			</ul>
			<div class="tab-content" id="permissionTabContent">
				<div id="partner" class="tab-pane active">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_welcome'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_welcome'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_footer'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_footer'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_view_deal_list'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_view_deal_list'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_submit_new_deal'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_submit_new_deal'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_check_coupon_status'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_check_coupon_status'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_change_coupon_status'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_change_coupon_status'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_view_coupon_list'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_view_coupon_list'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_view_buyer_info'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_view_buyer_info'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_view_commission_report'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_view_commission_report'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_edit_profile'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_edit_profile'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_view_free_coupon_list'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_view_free_coupon_list'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('partner_submit_new_free_coupon'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_submit_new_free_coupon'); ?>
						</div>
					</div>
				</div>
				<div id="staff" class="tab-pane">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_access_level'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_access_level'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_welcome'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_welcome'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_footer'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_footer'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_change_order_paid'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_change_order_paid'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_change_order_unpaid'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_change_order_unpaid'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_change_user_info'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_change_user_info'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_view_coupon'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_view_coupon'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('staff_send_coupon'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('staff_send_coupon'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>