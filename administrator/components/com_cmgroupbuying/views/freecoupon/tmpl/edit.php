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

// We need to get itemid of Free Coupon menu item for preview
$db = JFactory::getDbo();
$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_cmgroupbuying&view=freecoupon' AND published = 1 LIMIT 1;";
$db->setQuery($query);
$previewItemId  = $db->loadResult();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'freecoupon.cancel' || document.formvalidator.isValid(document.id('free-coupon-form'))) {
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
		document.getElementById('metakey').value = document.getElementById('jform_metakey').value;
		document.getElementById('metadesc').value = document.getElementById('jform_metadesc').value;
		document.previewForm.submit();
	}
</script>
<div class="cmgroupbuying">
	<form action="<?php echo JURI::root() . 'index.php?option=com_cmgroupbuying&view=freecouponprevue&Itemid=' . $previewItemId; ?>" method="post" name="previewForm" target="_blank">
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
		<input type="hidden" name="coupon[metakey]" id="metakey" value="" />
		<input type="hidden" name="coupon[metadesc]" id="metadesc" value="" />
	</form>
	<div style="text-align: right">
		<button class="btn" onclick="previewCoupon()"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_PREVIEW'); ?></button>
	</div>
	<form action="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="free-coupon-form" class="form-validate form-horizontal">
	<?php if(!empty( $this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_TAB_GENERAL');?></a></li>
				<li><a href="#image" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_FREE_COUPON_TAB_IMAGE');?></a></li>
				<li><a href="#information" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_TAB_INFORMATION');?></a></li>
				<li><a href="#seo" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_TAB_SEO');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="general">
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
							<?php echo $this->form->getLabel('partner_id'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('partner_id'); ?>
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
							<?php echo $this->form->getLabel('featured'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('featured'); ?>
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
				</div>
				<div class="tab-pane" id="image">
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
				</div>
				<div class="tab-pane" id="information">
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
				</div>
				<div class="tab-pane" id="seo">
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
							 <?php echo $this->form->getLabel('metakey'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('metakey'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							 <?php echo $this->form->getLabel('metadesc'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="publishing">
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
							<?php echo $this->form->getLabel('approved'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('approved'); ?>
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
				</div>
			</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>