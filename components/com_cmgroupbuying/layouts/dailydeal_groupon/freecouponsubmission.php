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
$locationList = $this->locationList;
$locationsOfCoupon = $this->locationsOfCoupon;
$form = $this->form;
$configuration = $this->configuration;

// We need to get itemid of Free Coupon menu item for preview
$db = JFactory::getDbo();
$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_cmgroupbuying&view=freecoupon' AND published = 1 LIMIT 1;";
$db->setQuery($query);
$freeCouponItemId = $db->loadResult();

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}

?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('free-coupon-form'))) {
			Joomla.submitform(task, document.getElementById('free-coupon-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function previewCoupon()
	{
		document.getElementById('name').value = document.getElementById('jform_name').value;
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
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<form action="<?php echo JURI::root() . 'index.php?option=com_cmgroupbuying&view=freecouponprevue&Itemid=' . $freeCouponItemId; ?>" method="post" name="previewForm" target="_blank">
	<input type="hidden" name="coupon[name]" id="name" value="" />
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
<div class="deal_submission_action_buttons">
	<button class="btn" onclick="Joomla.submitbutton('save')"><?php echo JText::_('COM_CMGROUPBUYING_SUBMIT'); ?></button>
	<button class="btn" onclick="Joomla.submitbutton('cancel')"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></button>
	<button class="btn" onclick="previewCoupon()"><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW'); ?></button>
</div>
<form action="index.php?option=com_cmgroupbuying&controller=freecouponsubmission&task=save" method="post" name="adminForm" id="free-coupon-form" class="form-validate">
	<table class="deal_submission">
		<tr>
			<td><?php echo $this->form->getLabel('name'); ?></td>
			<td><?php echo $this->form->getInput('name'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('alias'); ?></td>
			<td><?php echo $this->form->getInput('alias'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('type'); ?></td>
			<td><?php echo $this->form->getInput('type'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('coupon_path'); ?></td>
			<td><?php echo $this->form->getInput('coupon_path'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('coupon_code'); ?></td>
			<td><?php echo $this->form->getInput('coupon_code'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('discount'); ?></td>
			<td><?php echo $this->form->getInput('discount'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('start_date'); ?></td>
			<td><?php echo $this->form->getInput('start_date'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('end_date'); ?></td>
			<td><?php echo $this->form->getInput('end_date'); ?></td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_FIELD_LOCATION_ID_LABEL') . '::' . JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_FIELD_LOCATION_ID_DESC'); ?>"
					class="hasTip required"
					for="jform_location_id"
					id="jform_location_id-lbl"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_FIELD_LOCATION_ID_LABEL'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</td>
			<td>
				<select
					class="inputbox required"
					name="jform[location_id][]"
					id="jform_location_id"
					aria-required="true"
					required="required"
					multiple="multiple"
					size="10">
					<?php
					foreach ($locationList as $location)
					{
						echo '<option';
						if(in_array($location['id'], $locationsOfCoupon))
						{
							echo ' selected="selected"';
						}
						echo ' value="' . $location['id'] . '">' . $location['name'] . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<?php foreach($this->form->getFieldset('image_path') as $field): ?>
		<tr>
			<td><?php echo $field->label; ?></td>
			<td><?php echo $field->input; ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td><?php echo $this->form->getLabel('background_image'); ?></td>
			<td><?php echo $this->form->getInput('background_image'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('short_description'); ?></td>
			<td><?php echo $this->form->getInput('short_description'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('description'); ?></td>
			<td><?php echo $this->form->getInput('description'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('mobile_description'); ?></td>
			<td><?php echo $this->form->getInput('mobile_description'); ?></td>
		</tr>
	</table>
	<input type="hidden" name="jform[partner_id]" id="jform_partner_id" value="<?php echo $this->partnerId; ?>" />
	<input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $form->getValue('id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="deal_submission_action_buttons">
	<button class="btn" onclick="Joomla.submitbutton('save')"><?php echo JText::_('COM_CMGROUPBUYING_SUBMIT'); ?></button>
	<button class="btn" onclick="Joomla.submitbutton('cancel')"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></button>
	<button class="btn" onclick="previewCoupon()"><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW'); ?></button>
</div>