<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" /> 
<?php if(JFactory::getLanguage()->isRTL()): ?> 
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
		var customIcons = {
				<?php
				foreach($this->categories as $category)
				{
						echo "'" . $category['alias'] . "': { icon: '" . JURI::root() . $category['map_icon'] . "', shadow: '" . JURI::root() . $category['map_icon_shadow'] . "' },";
				}
				?>
		};

		function load() {
			var map = new google.maps.Map(document.getElementById("map"), {
				center: new google.maps.LatLng(<?php echo $this->defaultLatitude; ?>,<?php echo $this->defaultLongitude; ?>),
				zoom: <?php echo $this->configuration['deal_map_zoom']; ?>,
				mapTypeId: 'roadmap'
			});
			var infoWindow = new google.maps.InfoWindow;

			downloadUrl("index.php?option=com_cmgroupbuying&controller=dealmap&task=generateXML", function(data) {
				var xml = data.responseXML;
				var markers = xml.documentElement.getElementsByTagName("marker");
				for (var i = 0; i < markers.length; i++) {
						var name = markers[i].getAttribute("name");
						var image = markers[i].getAttribute("image");
						var description = markers[i].getAttribute("description");
						var original_price = markers[i].getAttribute("original_price");
						var price = markers[i].getAttribute("price");
						var save = markers[i].getAttribute("save");
						var bought = markers[i].getAttribute("bought");
						var category = markers[i].getAttribute("category");
						var link = markers[i].getAttribute("link");
						var point = new google.maps.LatLng(
								parseFloat(markers[i].getAttribute("latitude")),
								parseFloat(markers[i].getAttribute("longitude")));
						var html = "<table class='deal_map'>";
						html = html + "<tr><td class='left'><img class='deal_image' src='" + image + "'></td>";
						html = html + "<td class='right'><div class='deal_name'>" + name + "</div><div class='deal_description'>" + description + "</div>";
						html = html + "<table class='deal_price_info'>";
						html = html + "<tr><td>" + original_price + "<div class='text'><?php echo JText::_('COM_CMGROUPBUYING_DEAL_ORIGINAL_PRICE'); ?></div></td>";
						html = html + "<td>" + price + "<div class='text'><?php echo JText::_('COM_CMGROUPBUYING_DEAL_PRICE'); ?></div></td>";
					html = html + "<td>" + save + "<div class='text'><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SAVED_VALUE'); ?></div></td></tr>";
					html = html + "</table>";
					html = html + "</td></tr>";
					html = html + "<tr><td><div class='deal_bought'>" + bought + " <?php echo JText::_('COM_CMGROUPBUYING_BOUGHT'); ?></div></td><td><div class='popup_view_button'><div class='cm_button'><a href='" + link + "' target='_blank'><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MAP_MORE_INFO'); ?></a></div></div></td></tr>";
					html = html + "</table>";
					var icon = customIcons[category] || {};
					var marker = new google.maps.Marker({
						map: map,
						position: point,
						icon: icon.icon,
						shadow: icon.shadow
					});
					bindInfoWindow(marker, map, infoWindow, html);
				}
			});
		}

		function bindInfoWindow(marker, map, infoWindow, html) {
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
			});
		}

		function downloadUrl(url, callback) {
			var request = window.ActiveXObject ?
					new ActiveXObject('Microsoft.XMLHTTP') :
					new XMLHttpRequest;

			request.onreadystatechange = function() {
				if (request.readyState == 4) {
					request.onreadystatechange = doNothing;
					callback(request, request.status);
				}
			};

			request.open('GET', url, true);
			request.send(null);
		}

		function doNothing() {}

		window.onload = function(){ load(); }
</script>
<div id="map" style="width: <?php echo $this->configuration['deal_map_width']; ?>px; height: <?php echo $this->configuration['deal_map_height']; ?>px"></div>