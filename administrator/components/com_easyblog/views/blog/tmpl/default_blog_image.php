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

<script type="text/javascript">
EasyBlog
	.require()
	.script( 'dashboard/blogimage' )
	.done(function($){
		$(".blogImage").implement(EasyBlog.Controller.Dashboard.BlogImage, {});
	});
</script>

<div class="write-blogimage blogImage empty">
    <div class="mbl"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_DESC' ); ?></div>
	<div class="blogImagePlaceHolder">
		<input type="hidden" class="imageData" name="image" value='<?php echo $this->blog->image;?>' />
	</div>
	<div class="blogImageControl clearfix">
		<a href="javascript:void(0);" class="selectBlogImage buttons"><?php echo JText::_( 'COM_EASYBLOG_SELECT_BLOG_IMAGE' );?></a>
		<a href="javascript:void(0);" class="removeBlogImage buttons float-r"><?php echo JText::_( 'COM_EASYBLOG_REMOVE_BLOG_IMAGE' );?></a>
	</div>
</div>
