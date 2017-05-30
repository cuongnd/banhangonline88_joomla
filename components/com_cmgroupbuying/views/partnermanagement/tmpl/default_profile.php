<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$partner = $this->partner;
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task);
		}
	}

	function getLink(locationId)
	{
		jsonString = jQuery("input#jform_location" + locationId).val();
		jQuery("a#location" + locationId).attr("href", '');
		var link = 'index.php?option=com_cmgroupbuying&view=partnerlocation&tmpl=component';
		link += '&locationId=' + locationId + '&elements=' + encodeURIComponent(jsonString);
		jQuery("a#location" + locationId).attr("href", link);
	}

	function removeLocation(locationId)
	{
		jQuery("input#jform_location" + locationId).val("");
		jQuery("input#jform_location" + locationId + "_name").val("");
	}
</script>
<h3><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_EDIT_PROFILE_PAGE_TITLE'); ?></h3>
<div class="row-fluid">
	<div class="span12">
		<div class="pull-right">
			<button class="btn btn-primary" onclick="Joomla.submitbutton('save')"><?php echo JText::_('COM_CMGROUPBUYING_SUBMIT'); ?></button>
			<button class="btn btn-danger" onclick="Joomla.submitbutton('cancel')"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></button>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<form action="index.php?option=com_cmgroupbuying&controller=partnerprofile" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
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
					<?php echo $this->form->getLabel('alias'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('alias'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('website'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('website'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('map_zoom_level'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('map_zoom_level'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('about'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('about'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('logo'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('logo'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location1'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location1'); ?>
					<a id="location1" onclick="getLink('1')" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_ADD_LOCATION'); ?></a>
					<a onclick="removeLocation('1')" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_REMOVE_LOCATION'); ?></a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location2'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location2'); ?>
					<a id="location2" onclick="getLink('2')" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_ADD_LOCATION'); ?></a>
					<a onclick="removeLocation('2')" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_REMOVE_LOCATION'); ?></a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location3'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location3'); ?>
					<a id="location3" onclick="getLink('3')" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_ADD_LOCATION'); ?></a>
					<a onclick="removeLocation('3')" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_REMOVE_LOCATION'); ?></a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location4'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location4'); ?>
					<a id="location4" onclick="getLink('4')" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_ADD_LOCATION'); ?></a>
					<a onclick="removeLocation('4')" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_REMOVE_LOCATION'); ?></a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location5'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location5'); ?>
					<a id="location5" onclick="getLink('5')" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_ADD_LOCATION'); ?></a>
					<a onclick="removeLocation('5')" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_REMOVE_LOCATION'); ?></a>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?> 
		</form>
	</div>
</div>