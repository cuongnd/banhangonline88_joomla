<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;

if(version_compare(JVERSION, '3.0.0', 'lt') && $configuration['jquery_loading'] != "")
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}
?>
<style type="text/css">
	#map_canvas
	{
		height: 400px;
		width: 600px;
	}
</style>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
	function initialize()
	{
		var latlng = new google.maps.LatLng(<?php echo $this->latitude; ?>, <?php echo $this->longitude; ?>);
		
		var myOptions = {
			zoom: <?php echo $this->zoom; ?>,
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

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			draggable: true
		});
		
		var infoWindow = new google.maps.InfoWindow({
			content: marker.getPosition().toUrlValue(6)
		});
		
		google.maps.event.addListener(marker, 'dragend', function() {
			var markerTmp = marker.getPosition();
			marker.setPosition(markerTmp);
			infoWindow.close(map, marker);
			exportPoint(markerTmp);
		});
		
		google.maps.event.addListener(map, 'click', function(event) {
			var markerTmp = event.latLng;
			marker.setPosition(markerTmp);
			infoWindow.close(map, marker);
			exportPoint(markerTmp);
		});
		
		google.maps.event.addListener(map, "zoom_changed", function() {
			var zoom = map.getZoom();
			if (window.parent) window.parent.jInsertFieldValue(zoom,'jform_map_zoom_level');
		});        
	}     

	function exportPoint(markerTmp)
	{
		if (window.parent) window.parent.jInsertFieldValue(markerTmp.lat(),'jform_map_latitude');
		if (window.parent) window.parent.jInsertFieldValue(markerTmp.lng(),'jform_map_longitude');
	}
	
	jQuery(document).ready(function()
	{
		initialize();
	});
</script>
<div id="map_canvas"></div>