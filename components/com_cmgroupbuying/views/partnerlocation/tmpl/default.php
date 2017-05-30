<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.framework');

$jinput = JFactory::getApplication()->input;

$locationId = $jinput->get('locationId', 0, 'int');

if($locationId < 1 || $locationId > 5)
{
	echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_ERROR_INVALID_LOCATION_ID');
	jexit();
}

$locationElementsJSON = $jinput->get('elements','', 'string');
$locationElementsJSON = urldecode($locationElementsJSON);
$locationElementsArray = json_decode(htmlspecialchars_decode($locationElementsJSON, ENT_QUOTES));

if(empty($locationElementsArray))
{
	$locationElementsJSON = '{"name":"","address":"","latitude":"","longitude":"","phone":""}';
	$locationElementsArray = json_decode($locationElementsJSON);
}

$name = $locationElementsArray->name;
$address = $locationElementsArray->address;
$latitude = $locationElementsArray->latitude;
$longitude = $locationElementsArray->longitude;
$phone = $locationElementsArray->phone;

if($latitude == "")
{
	$latitude = 0;
}

if($longitude == "")
{
	$longitude = 0;
}

$emptyNameMessage = JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_ERROR_EMPTY_NAME');
$emptyLatitudeMessage = JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_ERROR_EMPTY_LATITUDE');
$emptyLongitudeMessage = JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_ERROR_EMPTY_LONGITUDE');
?>
<style type="text/css">
	#map_canvas
	{
		height: 300px;
		width: 400px;
	}
</style>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<div class="cmgroupbuying">
	<table class="table">
		<tr>
			<td><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_NAME'); ?>:</td>
			<td><input id="name" size="50" value="<?php echo $name; ?>" /></td>
			<td rowspan="5"><div id="map_canvas"></div></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_ADDRESS'); ?>:</td>
			<td>
				<textarea id="address" cols="40" rows="5"><?php echo $address; ?></textarea><br />
				<a class="btn" onclick="getCoordinatesFromAddress()"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_GET_COORDINATES_BUTTON'); ?></a>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_LATITUDE'); ?>:</td>
			<td><input id="latitude" size="50" value="<?php echo $latitude; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_LONGITUDE'); ?>:</td>
			<td><input id="longitude" size="50" value="<?php echo $longitude; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_LOCATION_PHONE'); ?>:</td>
			<td><input id="phone" size="50" value="<?php echo $phone; ?>" /></td>
		</tr>
	</table>
	<input id="json" value="" type="hidden" />
	<button type="button" onclick="generateResult();"><?php echo JText::_('COM_CMGROUPBUYING_COUPON_DESIGN_SAVE'); ?></button> 
</div>
<script type="text/javascript">
	var marker;
	var map;

	jQuery(document).ready(function()
	{
		var latlng = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>);

		var myOptions = {
			zoom: 12,
			center: latlng,
			mapTypeControl: true,
			mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DEFAULT},
			navigationControl: true,
			navigationControlOptions: {style: google.maps.NavigationControlStyle.DEFAULT},
			scaleControl: true,
			scrollwheel: true,
			disableDoubleClickZoom: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		marker          = new google.maps.Marker();
		map             = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		window.marker   = new google.maps.Marker({
			position: latlng,
			map: map,
			draggable: true
		});
		google.maps.event.addListener(marker, 'dragend', function() {
			var markerTmp = marker.getPosition();
			marker.setPosition(markerTmp);
			exportPoint(markerTmp);
		});
		google.maps.event.addListener(map, 'click', function(event) {
			var markerTmp = event.latLng;
			marker.setPosition(markerTmp);
			exportPoint(markerTmp);
		});
	});
	function exportPoint(markerTmp)
	{
		jQuery("input#latitude").val(markerTmp.lat());
		jQuery("input#longitude").val(markerTmp.lng());
	}

	function getCoordinatesFromAddress()
	{
		var address = jQuery("textarea#address").val();

		if(address != '')
		{
			var geocoder = new google.maps.Geocoder();
			address = address.replace(/~/g, '');

			geocoder.geocode( { 'address': address}, function(results, status) {

				if(status == google.maps.GeocoderStatus.OK)
				{
					jQuery("input#latitude").val(results[0].geometry.location.lat());
					jQuery("input#longitude").val(results[0].geometry.location.lng());
					var point = new google.maps.LatLng(
						parseFloat(results[0].geometry.location.lat()),
						parseFloat(results[0].geometry.location.lng()));
					marker.setPosition(point);
					map.setCenter(point);
				}
				else
				{
					alert("<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_FAILED_TO_GET_COORDINATES'); ?>");
				}
			});
		}

		return false;
	}

	function generateResult()
	{
		var name = jQuery("input#name").val().replace('"', '&quot;');
		var address = jQuery("textarea#address").val().replace('"', '&quot;');
		var latitude = jQuery("input#latitude").val().replace('"', '&quot;');
		var longitude = jQuery("input#longitude").val().replace('"', '&quot;');
		var phone = jQuery("input#phone").val().replace('"', '&quot;');
		address = address.replace(/\n/g, "\\n");

		if(name == "") {
			alert("<?php echo str_replace('"', '\"', $emptyNameMessage); ?>");
		}
		else if(latitude == "") {
			alert("<?php echo str_replace('"', '\"', $emptyLatitudeMessage); ?>");
		}
		else if(longitude == "") {
			alert("<?php echo str_replace('"', '\"', $emptyLongitudeMessage); ?>");
		}
		else {
			jQuery("#json").val(
				'{"name":"' + name
					+ '","address":"' + address
					+ '","latitude":"' + latitude
					+ '","longitude":"' + longitude
					+ '","phone":"' + phone
					+ '"}');
			window.parent.jInsertFieldValue(document.id('json').value,'jform_location<?php echo $locationId; ?>');
			window.parent.jInsertFieldValue(document.id('name').value,'jform_location<?php echo $locationId; ?>_name');
			window.parent.SqueezeBox.close();
		}
	}
</script>