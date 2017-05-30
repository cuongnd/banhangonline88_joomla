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

$form = $this->form;
$configuration = $this->configuration;

// We need to get itemid of Free Coupon menu item for preview
$db = JFactory::getDbo();
$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_cmgroupbuying&view=freecoupon' AND published = 1 LIMIT 1;";
$db->setQuery($query);
$freeCouponItemId   = $db->loadResult();

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}
?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'cancel' || document.formvalidator.isValid(document.id('free-coupon-form')))
		{
			Joomla.submitform(task, document.getElementById('free-coupon-form'));
		}
	}

	function getMapLink()
	{
		jQuery("a#mapLink").attr("href", '');
		var link = 'index.php?option=com_cmgroupbuying&view=googlemaps&tmpl=component';
		link += '&latitude=' + jQuery("input#jform_map_latitude").val();
		link += '&longitude=' + jQuery("input#jform_map_longitude").val();
		link += '&zoom=' + jQuery("input#jform_map_zoom_level").val();
		jQuery("a#mapLink").attr("href", link);
	}

	function previewCoupon()
	{
		document.getElementById('name').value = document.getElementById('jform_name').value;
		document.getElementById('type').value = document.getElementById('jform_type').value;
		document.getElementById('start_date').value = document.getElementById('jform_start_date').value;
		document.getElementById('end_date').value = document.getElementById('jform_end_date').value;
		document.getElementById('discount').value = document.getElementById('jform_discount').value;
		document.getElementById('partner_id').value = document.getElementById('jform_partner_id').value;
		document.getElementById('image_path_1').value = document.getElementById('jform_image_path_1').value;
		document.getElementById('image_path_2').value = document.getElementById('jform_image_path_2').value;
		document.getElementById('image_path_3').value = document.getElementById('jform_image_path_3').value;
		document.getElementById('image_path_4').value = document.getElementById('jform_image_path_4').value;
		document.getElementById('image_path_5').value = document.getElementById('jform_image_path_5').value;
		document.getElementById('background_image').value = document.getElementById('jform_background_image').value;
		document.getElementById('short_description').value = document.getElementById('jform_short_description').value;
		var content = tinyMCE.get('jform_description').getContent();
		document.getElementById('description').value = content;
		document.previewForm.submit();
	}
</script>
<h3><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_PAGE_TITLE'); ?></h3>
<form action="<?php echo JURI::root() . 'index.php?option=com_cmgroupbuying&view=freecouponprevue&Itemid=' . $freeCouponItemId; ?>" method="post" name="previewForm" target="_blank">
	<input type="hidden" name="coupon[name]" id="name" value="" />
	<input type="hidden" name="coupon[type]" id="type" value="" />
	<input type="hidden" name="coupon[start_date]" id="start_date" value="" />
	<input type="hidden" name="coupon[end_date]" id="end_date" value="" />
	<input type="hidden" name="coupon[discount]" id="discount" value="" />
	<input type="hidden" name="coupon[partner_id]" id="partner_id" value="" />
	<input type="hidden" name="coupon[image_path_1]" id="image_path_1" value="" />
	<input type="hidden" name="coupon[image_path_2]" id="image_path_2" value="" />
	<input type="hidden" name="coupon[image_path_3]" id="image_path_3" value="" />
	<input type="hidden" name="coupon[image_path_4]" id="image_path_4" value="" />
	<input type="hidden" name="coupon[image_path_5]" id="image_path_5" value="" />
	<input type="hidden" name="coupon[background_image]" id="background_image" value="" />
	<textarea name="coupon[short_description]" id="short_description" style="display: none"></textarea>
	<textarea name="coupon[description]" id="description" style="display: none"></textarea>
</form>
<div class="row-fluid">
	<div class="span12">
		<div class="pull-right">
			<button class="btn btn-primary" onclick="Joomla.submitbutton('save')"><?php echo JText::_('COM_CMGROUPBUYING_SUBMIT'); ?></button>
			<button class="btn btn-danger" onclick="Joomla.submitbutton('cancel')"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></button>
			<button class="btn btn-info" onclick="previewCoupon()"><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW'); ?></button>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<form action="index.php?option=com_cmgroupbuying&controller=freecouponsubmission&task=save" method="post" name="free-coupon-form" id="free-coupon-form" class="form-validate form-horizontal">
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
					<?php echo $this->form->getLabel('type'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('type'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('coupon_path'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('coupon_path'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('coupon_code'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('coupon_code'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('discount'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('discount'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('start_date'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('start_date'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('end_date'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('end_date'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('category_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('category_id'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('location_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('location_id'); ?>
				</div>
			</div>
			<?php foreach($this->form->getFieldset('image_path') as $field): ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label; ?>
				</div>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			</div>
			<?php endforeach; ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('background_image'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('background_image'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('short_description'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('short_description'); ?>
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
					<?php echo $this->form->getLabel('mobile_description'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('mobile_description'); ?>
				</div>
			</div>
			<input type="hidden" name="jform[partner_id]" id="jform_partner_id" value="<?php echo $this->partner['id']; ?>" />
			<input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $form->getValue('id'); ?>" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
