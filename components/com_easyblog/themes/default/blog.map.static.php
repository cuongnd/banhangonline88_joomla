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
<div class="clearfix"></div>
<p></p>
<div class="blog-map">
    <div class="map-address mbs">
        <?php echo $tooltips;?> <a href="http://www.google.com/maps?q=<?php echo urlencode( $address );?>" target="_blank" class="map-link fwb"><?php echo JText::_( 'COM_EASYBLOG_LOCATION_LARGER_MAP' );?></a>
    </div>
    <div class="map-images">
    	<img src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $latitude;?>,<?php echo $longitude;?>&language=<?php echo $lang; ?>&maptype=<?php echo strtolower( $mapType );?>&zoom=<?php echo $defaultZoom;?>&size=<?php echo $width;?>x<?php echo $height;?>&sensor=<?php echo $sensor;?>&markers=color:red|label:S|<?php echo $latitude;?>,<?php echo $longitude;?>" />
    </div>
</div>
