<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$categories = $this->categories;

if(empty($categories)):
	echo JTEXT::_('COM_CMGROUPBUYING_CATEGORY_BROWSING_NO_CATEGORY');
else:
	$configuration = $this->configuration;

	if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
	{
		JFactory::getDocument()->addScript($configuration['jquery_loading']);
	}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
<script>
	var gmarkers = [];

	function getDeals(cateogryId)
	{
		jQuery("#deal_column").empty().html('<div class="loading"></div>');
		jQuery.ajax({
			url: "index.php",
			type: "post",
			data: {"option":"com_cmgroupbuying","controller":"categorybrowsing","task":"getDeals","categoryId":cateogryId},
			success:function(html){
				window.setTimeout(function() {
					jQuery("#deal_column").html(html);
				}, 500);
			}
		});
	}

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
				var original_price = markers[i].getAttribute("original_price");
				var price = markers[i].getAttribute("price");
				var save = markers[i].getAttribute("save");
				var bought = markers[i].getAttribute("bought");
				var category = markers[i].getAttribute("category");
				var link = markers[i].getAttribute("link");
				var point = new google.maps.LatLng(
					parseFloat(markers[i].getAttribute("latitude")),
					parseFloat(markers[i].getAttribute("longitude")));
				var html = '<table class="category_browsing_map">';
				html = html + '<tr><td colspan="2" class="deal_name">' + name + '</td></tr>';
				html = html + '<tr><td colspan="2" class="deal_image"><img src="' + image + '"></td></tr>';
				html = html + '<tr><td class="deal_bought" >' + bought + ' <?php echo JText::_('COM_CMGROUPBUYING_BOUGHT'); ?></td><td class="popup_view_button"><div class="cm_button"><a href="' + link + '" target="_blank"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MAP_MORE_INFO'); ?></a></div></td></tr>';
				html = html + '</table>';
				var icon = customIcons[category] || {};
				var marker = new google.maps.Marker({
					map: map,
					position: point,
					icon: icon.icon,
					shadow: icon.shadow
				});
				gmarkers[markers[i].getAttribute("latitude") + "," + markers[i].getAttribute("longitude")] = marker;
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

	function markerTrigger(id)
	{
		google.maps.event.trigger(gmarkers[id], "click");
	}

	jQuery(document).ready(function() {
		window.onload = function(){
			load();
			jQuery('#cm_category_browsing li.category_li').first().trigger('click');
			jQuery('#cm_category_browsing li.category_li').first().addClass('selected');
			jQuery('#cm_category_browsing li.category_li').click(function(){
				clickedLi = jQuery(this);
				jQuery('#cm_category_browsing li.category_li').each(function(index, value) {
					jQuery(this).removeClass('selected');
				});
				jQuery(this).addClass('selected');
			});
		}
	});
</script>
<div class="page_title"><p><?php echo $this->pageTitle; ?></p></div>
<div id="cm_category_browsing" style="height: <?php echo $this->configuration['deal_map_height']; ?>px">
	<div class="category_navigation clearfix">
		<div class="category_column" id="category_column" style="height: <?php echo $this->configuration['deal_map_height']; ?>px">
			<h2 class="category_title"><?php echo JText::_('COM_CMGROUPBUYING_CATEGORY_BROWSING_CATEGORY_TITLE'); ?></h2>
			<ul class="category_ul" id="category_ul">
				<?php foreach($categories as $category): ?>
				<li class="category_li" onClick="getDeals(<?php echo $category['id']; ?>)"><?php echo $category['name']; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="deal_column" id="deal_column" style="height: <?php echo $this->configuration['deal_map_height']; ?>px">
		</div>
		<div class="deal_map_container" style="height: <?php echo $this->configuration['deal_map_height']; ?>px">
			<div id="map"></div>
		</div>
	</div>
</div>
<?php
endif;
?>