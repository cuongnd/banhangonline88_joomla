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

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('bootstrap.tooltip');
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'profile.cancel' || document.formvalidator.isValid(document.id('profile-form')))
		{
			Joomla.submitform(task, document.getElementById('profile-form'));
		}
	}
</script>
<div class="cmgroupbuying">
	<form action="index.php" method="post" name="adminForm" id="profile-form" class="form-validate form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="profile" />
		<input type="hidden" name="id" value="1" />
	<?php if(!empty( $this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10 form-horizontal">
	<?php else : ?>
		<div id="j-main-container" class="form-horizontal">
	<?php endif; ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('optional_text'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('optional_text'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('required_text'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('required_text'); ?>
					</div>
				</div>
			</div>
			<div class="control-group" style="clear: both; padding-top: 20px">
				<strong><?php echo JText::_('COM_CMGROUPBUYING_USER_PROFILE_INSTRUCTION_1'); ?></strong>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_name_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_name_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_firstname_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_firstname_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_lastname_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_lastname_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_address_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_address_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_city_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_city_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_state_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_state_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_zip_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_zip_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_phone_value'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_phone_value'); ?>
					</div>
				</div>
			</div>
			<div class="control-group" style="clear: both; padding-top: 20px">
				<strong><?php echo JText::_('COM_CMGROUPBUYING_USER_PROFILE_INSTRUCTION_2'); ?></strong>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_name_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_name_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_firstname_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_firstname_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_lastname_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_lastname_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_address_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_address_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_city_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_city_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_state_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_state_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_zip_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_zip_attribute'); ?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('profile_phone_attribute'); ?>
				</div>
				<div class="controls">
					<div class="input-prepend input-append">
						<?php echo $this->form->getInput('profile_phone_attribute'); ?>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>