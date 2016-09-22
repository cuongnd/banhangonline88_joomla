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
<div id="section-comments" class="blog-section">
	<h3 class="section-title"><span><?php echo JText::_('COM_EASYBLOG_COMMENTS'); ?></span></h3>

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/<?php echo $language[0];?>_<?php echo JString::strtoupper( $language[1] );?>/all.js#xfbml=1&appId=<?php echo $system->config->get( 'main_facebook_like_appid' );?>";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<div class="fb-comments" data-width="100%" data-colorscheme="<?php echo $system->config->get( 'comment_facebook_colourscheme' );?>" data-href="<?php echo EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );?>"></div>
</div>

