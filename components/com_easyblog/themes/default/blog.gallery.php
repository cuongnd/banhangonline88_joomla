<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$config = EasyBlogHelper::getConfig();
?>
<div class="clearfix"></div>
<script type="text/javascript">
EasyBlog.require()
.library( 'masonry' , 'imagesloaded' , 'fancybox' , 'fancybox/thumbs' )
.stylesheet('fancybox/default','fancybox/thumbs')
.done(function($){

	var container 	= $( '#gallery-<?php echo $uid;?>' );

	$( 'img' ).load(function(){
		container.imagesLoaded(function(){
			container.masonry({
				itemSelector : '.gallery-item',
				isRTL: false
			});
		});
	});

	$( '.gallery-thumb-<?php echo $uid;?>' ).fancybox({
		prevEffect	: 'none',
		nextEffect	: 'none',
		helpers	: {

			<?php if (!$config->get( 'main_media_show_lightbox_caption')) { ?>
			title: null,
			<?php } ?>

			overlay	: {
				opacity : 0.8,
				css : {
					'background-color' : '#000'
				}
			},
			thumbs	: {
				width	: 50,
				height	: 50
			}
		}
	});
});
</script>
<div class="blog-gallery-wrap mtm" id="gallery-<?php echo $uid;?>">
	<?php foreach( $images as $image ){ ?>
	<div class="gallery-item">
		<a title="<?php echo $this->escape( $image->original );?>" class="gallery-thumb-item gallery-thumb-<?php echo $uid;?> thumb-link" href="<?php echo $baseURI . '/' . $image->original;?>" rel="gallery-thumb-<?php echo $uid;?>"><img src="<?php echo $baseURI . '/' . $image->thumbnail;?>" style="max-width:<?php echo $system->config->get( 'main_image_gallery_maxwidth' );?>px;" /></a>
	</div>
	<?php } ?>
</div>
