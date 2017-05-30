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

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'aggregatorsite.cancel' || document.formvalidator.isValid(document.id('aggregatorsite-form')))
		{
			Joomla.submitform(task, document.getElementById('aggregatorsite-form'));
		}
		else
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<style>
	input, textarea {
		width: auto !important;
	}
</style>
<div class="cmgroupbuying row-fluid">
	<form action="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="aggregatorsite-form" class="form-validate form-horizontal">
	<?php if(!empty( $this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('name'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('url'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('url'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('description'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('xml_tree_header'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('xml_tree_header'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('xml_tree_deals'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('xml_tree_deals'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('xml_tree_footer'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('xml_tree_footer'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('published'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('published'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('ordering'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('ordering'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('ref'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('ref'); ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_NAMES'); ?>
					</div>
					<div class="controls">
						<ul>
							<li>{deal_id}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_ID'); ?></li>
							<li>{deal_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME'); ?></li>
							<li>{deal_link}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LINK'); ?></li>
							<li>{deal_original_price}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_ORIGINAL_PRICE'); ?></li>
							<li>{deal_price}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_PRICE'); ?></li>
							<li>{deal_discount}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_DISCOUNT'); ?></li>
							<li>{deal_short_description}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_SHORT_DESC'); ?></li>
							<li>{deal_description}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_DESC'); ?></li>
							<li>{deal_start_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_START_DATE'); ?></li>
							<li>{deal_end_date}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_END_DATE'); ?></li>
							<li>{deal_highlights}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_HIGHLIGHTS'); ?></li>
							<li>{deal_terms}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_TERMS'); ?></li>
							<li>{deal_image}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_IMAGE'); ?></li>
							<li>{deal_bought}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_BOUGHT'); ?></li>
							<li>{deal_tipped}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_TIPPED'); ?></li>
							<li>{deal_category}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_CATEGORY'); ?></li>
							<li>{deal_location}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_LOCATION'); ?></li>
							<li>{partner_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME'); ?></li>
							<li>{partner_website}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_WEBSITE'); ?></li>
							<li>{partner_address}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_ADDRESS'); ?></li>
							<li>{partner_telephone}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_PHONE'); ?></li>
							<li>{partner_map_latitude}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_LAT'); ?></li>
							<li>{partner_map_longitude}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_LONG'); ?></li>
							<li>{partner_about}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_ABOUT'); ?></li>
							<li>{category_name}: <?php echo JText::_('COM_CMGROUPBUYING_VARIABLE_CATEGORY_NAME'); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
