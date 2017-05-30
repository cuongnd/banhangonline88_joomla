<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>

<!-- Location services -->
<script type="text/javascript">

EasyBlog.require()
	.script(
		"location"
	)
	.done(function($){

		$("#blog-map-<?php echo $uid;?>").implement(
			"EasyBlog.Controller.Location.Map",
			{
				locations: [
					{
						"latitude": "<?php echo $latitude; ?>",
						"longitude": "<?php echo $longitude; ?>",
						"address": "<?php echo $address; ?>"
					}
				],
				mapType: "<?php echo $mapType;?>",
				width: "<?php echo $width; ?>",
				height: "<?php echo $height; ?>",
				language: "<?php echo $lang; ?>",
				zoom: <?php echo $defaultZoom;?>,
				maxZoom: <?php echo $maxZoom;?>,
				minZoom: <?php echo $minZoom;?>
			}
		)
	});
</script>
<div class="blog-map mtl">
	<div class="blog-map" id="blog-map-<?php echo $uid;?>">
		<div class="map-address mbs">
			<?php echo $tooltips; ?> <a href="http://www.google.com/maps?q=<?php echo urlencode( $address );?>&amp;hl=<?php echo $locale;?>" target="_blank" class="map-link fwb"><?php echo JText::_( 'COM_EASYBLOG_LOCATION_LARGER_MAP' );?></a>
		</div>
		<div class="map-images">
			<div id="<?php echo $elementId;?>" class="locationMap" style="width: <?php echo $width;?>px !important;height: <?php echo $height;?>px !important;"></div>
		</div>
	</div>
</div>