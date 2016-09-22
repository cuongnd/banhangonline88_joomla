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
<?php if( $position == 'bottom' ){ ?>
<div class="social-button pinit">
<?php } else { ?>
<div class="socialbutton-vertical align<?php echo $position;?>">
<?php } ?>
	<a href="http://pinterest.com/pin/create/button/?url=<?php echo $url;?>&media=<?php echo $image;?>&description=<?php echo $text;?>" class="pin-it-button" count-layout="<?php echo $style;?>">Pin It</a>
	<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
</div>