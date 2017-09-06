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
$categoryList = $this->categoryList;
$locationList = $this->locationList;
$locationsOfDeal = $this->locationsOfDeal;
$optionsOfDeal = $this->optionsOfDeal;
$form = $this->form;
$configuration = $this->configuration;

// We need to get itemid of Today Deal menu item for preview
$db = JFactory::getDbo();
$query = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_cmgroupbuying&view=todaydeal' AND published = 1 LIMIT 1;";
$db->setQuery($query);
$todayDealItemId  = $db->loadResult();

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
		if (task == 'cancel' || document.formvalidator.isValid(document.id('deal-form'))) {
			Joomla.submitform(task, document.getElementById('deal-form'));
		}
		else {
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
		document.getElementById('background_image').value = document.getElementById('jform_background_image').value;
		document.getElementById('short_description').value = document.getElementById('jform_short_description').value;
		var content = tinyMCE.get('jform_description').getContent();
		document.getElementById('description').value = content;
		content = tinyMCE.get('jform_highlights').getContent();
		document.getElementById('highlights').value = content;
		content = tinyMCE.get('jform_terms').getContent();
		document.getElementById('terms').value = content;
		document.previewForm.submit();
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
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
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
	<input type="hidden" name="deal[background_image]" id="background_image" value="" />
	<textarea name="deal[short_description]" id="short_description" style="display: none"></textarea>
	<textarea name="deal[description]" id="description" style="display: none"></textarea>
	<textarea name="deal[highlights]" id="highlights" style="display: none"></textarea>
	<textarea name="deal[terms]" id="terms" style="display: none"></textarea>
</form>
<div class="deal_submission_action_buttons">
	<button class="btn" onclick="Joomla.submitbutton('save')"><?php echo JText::_('COM_CMGROUPBUYING_SUBMIT'); ?></button>
	<button class="btn" onclick="Joomla.submitbutton('cancel')"><?php echo JText::_('COM_CMGROUPBUYING_CANCEL'); ?></button>
	<button class="btn" onclick="previewDeal()"><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW'); ?></button>
</div>
<form action="index.php?option=com_cmgroupbuying&controller=dealsubmission&task=save" method="post" name="adminForm" id="deal-form" class="form-validate">
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
			<td><?php echo $this->form->getLabel('start_date'); ?></td>
			<td><?php echo $this->form->getInput('start_date'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('end_date'); ?></td>
			<td><?php echo $this->form->getInput('end_date'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('min_bought'); ?></td>
			<td><?php echo $this->form->getInput('min_bought'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('max_bought'); ?></td>
			<td><?php echo $this->form->getInput('max_bought'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('max_coupon'); ?></td>
			<td><?php echo $this->form->getInput('max_coupon'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('shipping_cost'); ?></td>
			<td><?php echo $this->form->getInput('shipping_cost'); ?></td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_CATEGORY_ID_LABEL') . '::' . JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_CATEGORY_ID_DESC'); ?>"
					class="hasTip required"
					for="jform_category_id"
					id="jform_category_id-lbl"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_CATEGORY_ID_LABEL'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</td>
			<td>
				<select
					class="inputbox required"
					name="jform[category_id]"
					id="jform_category_id"
					aria-required="true"
					required="required">
					<?php
					foreach($categoryList as $category)
					{
						echo '<option';
						if($this->item->category_id == $category['id'])
						{
							echo ' selected="selected"';
						}
						echo ' value="' . $category['id'] . '">' . $category['name'] . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_LOCATION_ID_LABEL') . '::' . JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_LOCATION_ID_DESC'); ?>"
					class="hasTip required"
					for="jform_location_id"
					id="jform_location_id-lbl"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_LOCATION_ID_LABEL'); ?>
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
						if(in_array($location['id'], $locationsOfDeal))
						{
							echo ' selected="selected"';
						}
						echo ' value="' . $location['id'] . '">' . $location['name'] . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_ADDRESS_LABEL') . '::' . JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_ADDRESS_DESC'); ?>"
					class="hasTip"
					for="address"
					id="address-lbl"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_ADDRESS_LABEL'); ?>
				</label>
			</td>
			<td><input type="text" id="address" value="" size="30"/></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('map_latitude'); ?></td>
			<td>
				<?php echo $this->form->getInput('map_latitude'); ?>
				<?php if(version_compare(JVERSION, '3.0.0', 'ge')): ?>
				<a class="btn cmbtn" onClick="getCoordinatesFromAddress()" href="javascript:void();"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GET_COORDINATES_BUTTON'); ?></a>
				<?php else: ?>
				<div class="button2-left">
					<div class="blank">
						<a onClick="getCoordinatesFromAddress()" href="javascript:void();"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GET_COORDINATES_BUTTON'); ?></a>
					</div>
				</div>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('map_longitude'); ?></td>
			<td>
				<?php echo $this->form->getInput('map_longitude'); ?>
				<?php if(version_compare(JVERSION, '3.0.0', 'ge')): ?>
				<a id="mapLink" rel="{handler: 'iframe', size: {x: 630, y: 440}}" onclick="getMapLink()" href="javascript:void();" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GOOGLE_MAPS_BUTTON'); ?></a>
				<?php else: ?>
				<div class="button2-left">
					<div class="blank">
						<a id="mapLink" rel="{handler: 'iframe', size: {x: 630, y: 440}}" onclick="getMapLink()" href="javascript:void();" class="modal"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GOOGLE_MAPS_BUTTON'); ?></a>
					</div>
				</div>
				<?php endif; ?>
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
			<td><?php echo $this->form->getLabel('coupon_path'); ?></td>
			<td><?php echo $this->form->getInput('coupon_path'); ?></td>
		</tr>


		<tr>
			<td><?php echo $this->form->getLabel('coupon_elements'); ?></td>
			<td>
				<?php echo $this->form->getInput('coupon_elements'); ?>
				<?php if(version_compare(JVERSION, '3.0.0', 'ge')): ?>
				<a id="designLink" onclick="getLink()" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_DESIGN_COUPON_BUTTON'); ?></a>
				<?php else: ?>
				<div class="button2-left">
					<div class="blank">
						<a id="designLink" onclick="getLink()" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_DESIGN_COUPON_BUTTON'); ?></a>
					</div>
				</div>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<td><?php echo $this->form->getLabel('coupon_expiration'); ?></td>
			<td><?php echo $this->form->getInput('coupon_expiration'); ?></td>
		</tr>

		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "1") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_DESC', "1"); ?>"
					class="hasTip required"
					for="jform_option_name_1"
					id="jform_option_name_1-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "1"); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_name_1]"
					id="jform_option_name_1"
					value="<?php echo $optionsOfDeal[1]['name']; ?>"
					class="inputbox required"
					size="40"
					aria-required="true"
					required="required"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "1") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_DESC', "1"); ?>"
					class="hasTip required"
					for="jform_option_original_price_1"
					id="jform_option_original_price_1-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "1"); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_original_price_1]"
					id="jform_option_original_price_1"
					value="<?php echo $optionsOfDeal[1]['original_price']; ?>"
					class="inputbox required"
					size="40"
					aria-required="true"
					required="required"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "1") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_DESC', "1"); ?>"
					class="hasTip required"
					for="jform_option_price_1"
					id="jform_option_price_1-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "1"); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_price_1]"
					id="jform_option_price_1"
					value="<?php echo $optionsOfDeal[1]['price']; ?>"
					class="inputbox required"
					size="40"
					aria-required="true"
					required="required"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "2") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_DESC', "2"); ?>"
					class="hasTip"
					for="jform_option_name_2"
					id="jform_option_name_2-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "2"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_name_2]"
					id="jform_option_name_2"
					value="<?php echo $optionsOfDeal[1]['name']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "2") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_DESC', "2"); ?>"
					class="hasTip"
					for="jform_option_original_price_2"
					id="jform_option_original_price_2-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "2"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_original_price_2]"
					id="jform_option_original_price_2"
					value="<?php echo $optionsOfDeal[1]['original_price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "2") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_DESC', "2"); ?>"
					class="hasTip"
					for="jform_option_price_2"
					id="jform_option_price_2-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "2"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_price_2]"
					id="jform_option_price_2"
					value="<?php echo $optionsOfDeal[1]['price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "3") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_DESC', "3"); ?>"
					class="hasTip"
					for="jform_option_name_3"
					id="jform_option_name_3-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "3"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_name_3]"
					id="jform_option_name_3"
					value="<?php echo $optionsOfDeal[2]['name']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "3") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_DESC', "3"); ?>"
					class="hasTip"
					for="jform_option_original_price_3"
					id="jform_option_original_price_3-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "3"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_original_price_3]"
					id="jform_option_original_price_3"
					value="<?php echo $optionsOfDeal[2]['original_price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "3") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_DESC', "3"); ?>"
					class="hasTip"
					for="jform_option_price_3"
					id="jform_option_price_3-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "3"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_price_3]"
					id="jform_option_price_3"
					value="<?php echo $optionsOfDeal[2]['price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "4") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_DESC', "4"); ?>"
					class="hasTip"
					for="jform_option_name_4"
					id="jform_option_name_4-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "4"); ?>
				</label>
			</td>
			<td>
					<input
						type="text"
						name="jform[option_name_4]"
						id="jform_option_name_4"
						value="<?php echo $optionsOfDeal[3]['name']; ?>"
						class="inputbox"
						size="40"
						/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "4") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_DESC', "4"); ?>"
					class="hasTip"
					for="jform_option_original_price_4"
					id="jform_option_original_price_4-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "4"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_original_price_4]"
					id="jform_option_original_price_4"
					value="<?php echo $optionsOfDeal[3]['original_price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "4") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_DESC', "4"); ?>"
					class="hasTip"
					for="jform_option_price_4"
					id="jform_option_price_4-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "4"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_price_4]"
					id="jform_option_price_4"
					value="<?php echo $optionsOfDeal[3]['price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "5") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_DESC', "5"); ?>"
					class="hasTip"
					for="jform_option_name_5"
					id="jform_option_name_5-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_NAME_LABEL', "5"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_name_5]"
					id="jform_option_name_5"
					value="<?php echo $optionsOfDeal[4]['name']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "5") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_DESC', "5"); ?>"
					class="hasTip"
					for="jform_option_original_price_5"
					id="jform_option_original_price_5-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_ORIGINAL_PRICE_LABEL', "5"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_original_price_5]"
					id="jform_option_original_price_5"
					value="<?php echo $optionsOfDeal[4]['original_price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
		</tr>
		<tr>
			<td>
				<label
					title="<?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "5") . '::' . JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_DESC', "5"); ?>"
					class="hasTip"
					for="jform_option_price_5"
					id="jform_option_price_5-lbl"><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_SUBMISSION_FIELD_OPTION_PRICE_LABEL', "5"); ?>
				</label>
			</td>
			<td>
				<input
					type="text"
					name="jform[option_price_5]"
					id="jform_option_price_5"
					value="<?php echo $optionsOfDeal[4]['price']; ?>"
					class="inputbox"
					size="40"
					/>
			</td>
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
		<tr>
			<td><?php echo $this->form->getLabel('highlights'); ?></td>
			<td><?php echo $this->form->getInput('highlights'); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->form->getLabel('terms'); ?></td>
			<td><?php echo $this->form->getInput('terms'); ?></td>
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
	<button class="btn" onclick="previewDeal()"><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW'); ?></button>
</div>