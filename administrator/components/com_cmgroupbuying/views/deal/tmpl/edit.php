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
	JHtml::_('formbehavior.chosen', 'select');
}

$autoPostPlugin = JPluginHelper::getPlugin('cmgroupbuying', 'autopost');
$optionsOfDeal = $this->optionsOfDeal;

// We need to get itemid of Today Deal menu item for preview
$db = JFactory::getDbo();
$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_cmgroupbuying&view=todaydeal' AND published = 1 LIMIT 1;";
$db->setQuery($query);
$todayDealItemId = $db->loadResult();
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'deal.cancel' || document.formvalidator.isValid(document.id('deal-form')))
		{
			<?php if(!empty($autoPostPlugin)): ?>
			if(task == 'deal.apply' || task == 'deal.save' || task == 'deal.save2new')
			{
				var autoPost = confirm('<?php echo JText::_('COM_CMGROUPBUYING_DEAL_AUTO_POST_CONFIRMATION'); ?>');
				if(autoPost == true)
				{
					document.getElementById('autopost').value = "1";
				}
			}
			<?php endif; ?>
			Joomla.submitform(task, document.getElementById('deal-form'));
		}
		else
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function getLink()
	{
		elements = jQuery("input#jform_coupon_elements").val();
		elements = elements.replace(/\s/g, "");
		elements = elements.replace(/fontsize/g,"f");
		elements = elements.replace(/size/g, "s");
		elements = elements.replace(/width/g,"w");
		elements = elements.replace(/height/g,"h");
		elements = elements.replace(/top/g,"t");
		elements = elements.replace(/left/g,"l");
		elements = elements.replace(/align/g,"a");
		elements = elements.replace(/visible/g,"v");
		elements = elements.replace(/:{/g,"--");
		elements = elements.replace(/},/g,"+");
		elements = elements.replace(/{/g,"");
		elements = elements.replace(/}/g,"");
		elements = elements.replace(/"/g,"");
		jQuery("a#designLink").attr("href", '');
		var link = 'index.php?option=com_cmgroupbuying&view=coupondesign&tmpl=component';
		link += '&elements=' + encodeURIComponent(elements);
		link += '&coupon=' + encodeURIComponent(jQuery("input#jform_coupon_path").val());
		jQuery("a#designLink").attr("href", link);
	}

	function previewDeal()
	{
		document.getElementById('name').value = document.getElementById('jform_name').value;
		document.getElementById('start_date').value = document.getElementById('jform_start_date').value;
		document.getElementById('end_date').value = document.getElementById('jform_end_date').value;
		document.getElementById('min_bought').value = document.getElementById('jform_min_bought').value;
		document.getElementById('max_bought').value = document.getElementById('jform_max_bought').value;
		document.getElementById('max_coupon').value = document.getElementById('jform_max_coupon').value;
		document.getElementById('partner_id').value = document.getElementById('jform_partner_id').value;
		document.getElementById('image_path_1').value = document.getElementById('jform_image_path_1').value;
		document.getElementById('image_path_2').value = document.getElementById('jform_image_path_2').value;
		document.getElementById('image_path_3').value = document.getElementById('jform_image_path_3').value;
		document.getElementById('image_path_4').value = document.getElementById('jform_image_path_4').value;
		document.getElementById('image_path_5').value = document.getElementById('jform_image_path_5').value;
		document.getElementById('option_name_1').value = document.getElementById('jform_option_name_1').value;
		document.getElementById('option_original_price_1').value = document.getElementById('jform_option_original_price_1').value;
		document.getElementById('option_price_1').value = document.getElementById('jform_option_price_1').value;
		document.getElementById('option_name_2').value = document.getElementById('jform_option_name_2').value;
		document.getElementById('option_original_price_2').value = document.getElementById('jform_option_original_price_2').value;
		document.getElementById('option_price_2').value = document.getElementById('jform_option_price_2').value;
		document.getElementById('option_name_3').value = document.getElementById('jform_option_name_3').value;
		document.getElementById('option_original_price_3').value = document.getElementById('jform_option_original_price_3').value;
		document.getElementById('option_price_3').value = document.getElementById('jform_option_price_3').value;
		document.getElementById('option_name_4').value = document.getElementById('jform_option_name_4').value;
		document.getElementById('option_original_price_4').value = document.getElementById('jform_option_original_price_4').value;
		document.getElementById('option_price_4').value = document.getElementById('jform_option_price_4').value;
		document.getElementById('option_name_5').value = document.getElementById('jform_option_name_5').value;
		document.getElementById('option_original_price_5').value = document.getElementById('jform_option_original_price_5').value;
		document.getElementById('option_price_5').value = document.getElementById('jform_option_price_5').value;
		document.getElementById('option_name_6').value = document.getElementById('jform_option_name_6').value;
		document.getElementById('option_original_price_6').value = document.getElementById('jform_option_original_price_6').value;
		document.getElementById('option_price_6').value = document.getElementById('jform_option_price_6').value;
		document.getElementById('option_name_7').value = document.getElementById('jform_option_name_7').value;
		document.getElementById('option_original_price_7').value = document.getElementById('jform_option_original_price_7').value;
		document.getElementById('option_price_7').value = document.getElementById('jform_option_price_7').value;
		document.getElementById('option_name_8').value = document.getElementById('jform_option_name_8').value;
		document.getElementById('option_original_price_8').value = document.getElementById('jform_option_original_price_8').value;
		document.getElementById('option_price_8').value = document.getElementById('jform_option_price_8').value;
		document.getElementById('option_name_9').value = document.getElementById('jform_option_name_9').value;
		document.getElementById('option_original_price_9').value = document.getElementById('jform_option_original_price_9').value;
		document.getElementById('option_price_9').value = document.getElementById('jform_option_price_9').value;
		document.getElementById('option_name_10').value = document.getElementById('jform_option_name_10').value;
		document.getElementById('option_original_price_10').value = document.getElementById('jform_option_original_price_10').value;
		document.getElementById('option_price_10').value = document.getElementById('jform_option_price_10').value;
		document.getElementById('background_image').value = document.getElementById('jform_background_image').value;
		document.getElementById('short_description').value = document.getElementById('jform_short_description').value;
		var content = tinyMCE.get('jform_description').getContent();
		document.getElementById('description').value = content;
		content = tinyMCE.get('jform_highlights').getContent();
		document.getElementById('highlights').value = content;
		content = tinyMCE.get('jform_terms').getContent();
		document.getElementById('terms').value = content;
		document.getElementById('metakey').value = document.getElementById('jform_metakey').value;
		document.getElementById('metadesc').value = document.getElementById('jform_metadesc').value;
		document.previewForm.submit();
	}

	function getMapLink()
	{
		jQuery("a#mapLink").attr("href", '');
		var link = 'index.php?option=com_cmgroupbuying&view=googlemaps&tmpl=component';
		link += '&latitude=' + jQuery("input#jform_map_latitude").val();
		link += '&longitude=' + jQuery("input#jform_map_longitude").val();
		jQuery("a#mapLink").attr("href", link);
	}

	function getCoordinatesFromAddress()
	{
		var address = jQuery("input#address").val();

		if(address != '')
		{

			var geocoder = new google.maps.Geocoder();
			address = address.replace(/~/g, '');

			geocoder.geocode( { 'address': address}, function(results, status) {

				if(status == google.maps.GeocoderStatus.OK)
				{
					jQuery("input#jform_map_latitude").val(results[0].geometry.location.lat());
					jQuery("input#jform_map_longitude").val(results[0].geometry.location.lng());
				}
				else
				{
					alert("<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_FAILED_TO_GET_COORDINATES'); ?>");
				}

			});
		}
	}
</script>
<div class="cmgroupbuying">
	<form action="<?php echo JURI::root() . 'index.php?option=com_cmgroupbuying&view=dealprevue&Itemid=' . $todayDealItemId; ?>" method="post" name="previewForm" target="_blank">
		<input type="hidden" name="deal[name]" id="name" value="" />
		<input type="hidden" name="deal[start_date]" id="start_date" value="" />
		<input type="hidden" name="deal[end_date]" id="end_date" value="" />
		<input type="hidden" name="deal[min_bought]" id="min_bought" value="" />
		<input type="hidden" name="deal[max_bought]" id="max_bought" value="" />
		<input type="hidden" name="deal[max_coupon]" id="max_coupon" value="" />
		<input type="hidden" name="deal[partner_id]" id="partner_id" value="" />
		<input type="hidden" name="deal[image_path_1]" id="image_path_1" value="" />
		<input type="hidden" name="deal[image_path_2]" id="image_path_2" value="" />
		<input type="hidden" name="deal[image_path_3]" id="image_path_3" value="" />
		<input type="hidden" name="deal[image_path_4]" id="image_path_4" value="" />
		<input type="hidden" name="deal[image_path_5]" id="image_path_5" value="" />
		<input type="hidden" name="deal[option_name_1]" id="option_name_1" value="" />
		<input type="hidden" name="deal[option_original_price_1]" id="option_original_price_1" value="" />
		<input type="hidden" name="deal[option_price_1]" id="option_price_1" value="" />
		<input type="hidden" name="deal[option_name_2]" id="option_name_2" value="" />
		<input type="hidden" name="deal[option_original_price_2]" id="option_original_price_2" value="" />
		<input type="hidden" name="deal[option_price_2]" id="option_price_2" value="" />
		<input type="hidden" name="deal[option_name_3]" id="option_name_3" value="" />
		<input type="hidden" name="deal[option_original_price_3]" id="option_original_price_3" value="" />
		<input type="hidden" name="deal[option_price_3]" id="option_price_3" value="" />
		<input type="hidden" name="deal[option_name_4]" id="option_name_4" value="" />
		<input type="hidden" name="deal[option_original_price_4]" id="option_original_price_4" value="" />
		<input type="hidden" name="deal[option_price_4]" id="option_price_4" value="" />
		<input type="hidden" name="deal[option_name_5]" id="option_name_5" value="" />
		<input type="hidden" name="deal[option_original_price_5]" id="option_original_price_5" value="" />
		<input type="hidden" name="deal[option_price_5]" id="option_price_5" value="" />
		<input type="hidden" name="deal[option_name_6]" id="option_name_6" value="" />
		<input type="hidden" name="deal[option_original_price_6]" id="option_original_price_6" value="" />
		<input type="hidden" name="deal[option_price_6]" id="option_price_6" value="" />
		<input type="hidden" name="deal[option_name_7]" id="option_name_7" value="" />
		<input type="hidden" name="deal[option_original_price_7]" id="option_original_price_7" value="" />
		<input type="hidden" name="deal[option_price_7]" id="option_price_7" value="" />
		<input type="hidden" name="deal[option_name_8]" id="option_name_8" value="" />
		<input type="hidden" name="deal[option_original_price_8]" id="option_original_price_8" value="" />
		<input type="hidden" name="deal[option_price_8]" id="option_price_8" value="" />
		<input type="hidden" name="deal[option_name_9]" id="option_name_9" value="" />
		<input type="hidden" name="deal[option_original_price_9]" id="option_original_price_9" value="" />
		<input type="hidden" name="deal[option_price_9]" id="option_price_9" value="" />
		<input type="hidden" name="deal[option_name_10]" id="option_name_10" value="" />
		<input type="hidden" name="deal[option_original_price_10]" id="option_original_price_10" value="" />
		<input type="hidden" name="deal[option_price_10]" id="option_price_10" value="" />
		<input type="hidden" name="deal[background_image]" id="background_image" value="" />
		<textarea name="deal[short_description]" id="short_description" style="display: none"></textarea>
		<textarea name="deal[description]" id="description" style="display: none"></textarea>
		<textarea name="deal[highlights]" id="highlights" style="display: none"></textarea>
		<textarea name="deal[terms]" id="terms" style="display: none"></textarea>
		<input type="hidden" name="deal[metakey]" id="metakey" value="" />
		<input type="hidden" name="deal[metadesc]" id="metadesc" value="" />
	</form>
	<div style="text-align: right">
		<button class="btn" onclick="previewDeal()"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_PREVIEW'); ?></button>
	</div>
	<form action="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="deal-form" class="form-validate form-horizontal">
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
				<li><a href="#image_coupon" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_TAB_IMAGE_COUPON');?></a></li>
				<li><a href="#option_price" data-toggle="tab"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_TAB_OPTION_PRICE');?></a></li>
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
							<?php echo $this->form->getLabel('min_bought'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('min_bought'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('max_bought'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('max_bought'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('max_coupon'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('max_coupon'); ?>
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
							<?php echo $this->form->getLabel('category_id'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('category_id'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('product_id'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('product_id'); ?>
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
							<label
								title="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_FIELD_ADDRESS_LABEL') . '::' . JText::_('COM_CMGROUPBUYING_DEAL_FIELD_ADDRESS_DESC'); ?>"
								class="hasTip"
								for="address"
								id="address-lbl"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_FIELD_ADDRESS_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							 <input type="text" id="address" value="" size="30"/>
							<a onclick="getCoordinatesFromAddress()" class="btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GET_COORDINATES_BUTTON'); ?></a>
							<a id="mapLink" rel="{handler: 'iframe', size: {x: 630, y: 440}}" onclick="getMapLink()" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GOOGLE_MAPS_BUTTON'); ?></a>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('map_latitude'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('map_latitude'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('map_longitude'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('map_longitude'); ?>
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
				<div class="tab-pane" id="image_coupon">
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
							<?php echo $this->form->getLabel('coupon_path'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('coupon_path'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('coupon_elements'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('coupon_elements'); ?>
							<a id="designLink" onclick="getLink()" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_DESIGN_COUPON_BUTTON'); ?></a>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('coupon_expiration'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('coupon_expiration'); ?>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="option_price">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('commission_rate'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('commission_rate'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('shipping_cost'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('shipping_cost'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('advance_payment'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('advance_payment'); ?>
						</div>
					</div>
					<hr>
					<?php for($i = 1; $i <=10; $i++): ?>
					<div class="control-group">
						<div class="control-label">
							<label
								title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_NAME_LABEL', $i) . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_NAME_DESC', $i); ?>"
								class="hasTip<?php if($i == 1) echo " required"; ?>"
								for="jform_option_name_<?php echo $i; ?>"
								id="jform_option_name_<?php echo $i; ?>-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_NAME_LABEL', $i); ?>
								<?php if($i == 1): ?><span class="star">&nbsp;*</span><?php endif; ?>
							</label>
						</div>
						<div class="controls">
							<input
								type="text"
								name="jform[option_name_<?php echo $i; ?>]"
								id="jform_option_name_<?php echo $i; ?>"
								value="<?php echo isset($optionsOfDeal[$i]['name']) ? $optionsOfDeal[$i]['name'] : ''; ?>"
								class="inputbox<?php if($i == 1) echo " required"; ?>"
								size="40"
								<?php if($i == 1): ?>
								aria-required="true"
								required="required"
								<?php endif; ?>
								/>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<label
								title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ORIGINAL_PRICE_LABEL', $i) . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ORIGINAL_PRICE_DESC', $i); ?>"
								class="hasTip<?php if($i == 1) echo " required"; ?>"
								for="jform_option_original_price_<?php echo $i; ?>"
								id="jform_option_original_price_<?php echo $i; ?>-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ORIGINAL_PRICE_LABEL', $i); ?>
								<?php if($i == 1): ?><span class="star">&nbsp;*</span><?php endif; ?>
							</label>
						</div>
						<div class="controls">
							<input
								type="text"
								name="jform[option_original_price_<?php echo $i; ?>]"
								id="jform_option_original_price_<?php echo $i; ?>"
								value="<?php echo isset($optionsOfDeal[$i]['original_price']) ? $optionsOfDeal[$i]['original_price'] : ''; ?>"
								class="inputbox<?php if($i == 1) echo " required"; ?>"
								size="40"
								<?php if($i == 1): ?>
								aria-required="true"
								required="required"
								<?php endif; ?>
								/>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<label
								title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_PRICE_LABEL', $i) . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_PRICE_DESC', $i); ?>"
								class="hasTip<?php if($i == 1) echo " required"; ?>"
								for="jform_option_price_<?php echo $i; ?>"
								id="jform_option_price_<?php echo $i; ?>-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_PRICE_LABEL', $i); ?>
								<?php if($i == 1): ?><span class="star">&nbsp;*</span><?php endif; ?>
							</label>
						</div>
						<div class="controls">
							<input
								type="text"
								name="jform[option_price_<?php echo $i; ?>]"
								id="jform_option_price_<?php echo $i; ?>"
								value="<?php echo isset($optionsOfDeal[$i]['price']) ? $optionsOfDeal[$i]['price'] : ''; ?>"
								class="inputbox<?php if($i == 1) echo " required"; ?>"
								size="40"
								<?php if($i == 1): ?>
								aria-required="true"
								required="required"
								<?php endif; ?>
								/>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<label
								title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ADVANCE_PRICE_LABEL', $i) . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ADVANCE_PRICE_DESC', $i); ?>"
								class="hasTip"
								for="jform_option_advance_price_<?php echo $i; ?>"
								id="jform_option_advance_price_<?php echo $i; ?>-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_FIELD_OPTION_ADVANCE_PRICE_LABEL', $i); ?>
							</label>
						</div>
						<div class="controls">
							<input
								type="text"
								name="jform[option_advance_price_<?php echo $i; ?>]"
								id="jform_option_advance_price_<?php echo $i; ?>"
								value="<?php echo isset($optionsOfDeal[$i]['advance_price']) ? $optionsOfDeal[$i]['advance_price'] : ''; ?>"
								class="inputbox"
								size="40"
								/>
						</div>
					</div>
					<?php if($i != 10) echo '<hr>'; ?>
					<?php endfor; ?>
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
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('highlights'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('highlights'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('terms'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('terms'); ?>
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
							<?php echo $this->form->getLabel('published'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('published'); ?>
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
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php if(!empty($autoPostPlugin)): ?>
		<input type="hidden" id="autopost" name="autopost" value="0" />
		<?php endif; ?>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>