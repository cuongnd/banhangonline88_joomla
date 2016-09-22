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
<?php if($system->config->get( 'layout_dashboard_blogimage') ){ ?>
<script type="text/javascript">

EasyBlog.require()
.script(
	"dashboard/blogimage"
)
.done(function($){

	$(".blogImage").implement(EasyBlog.Controller.Dashboard.BlogImage, {});
});
</script>
<?php if( $this->acl->rules->upload_image ) {?>
<div class="blogImage empty clearfix">
	<div class="blogImagePreview">
		<div class="blogImagePlaceHolder">
			<input type="hidden" class="imageData" name="image" value='<?php echo $blog->image;?>' />
		</div>
		<div class="blogImageNote">
			<i></i>
			<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_HEADING' );?></b>
			<div><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_DESC' );?></div>
		</div>
		<div class="blogImageControl">
			<a href="javascript:void(0);" class="selectBlogImage buttons butt-orange float-r"><?php echo JText::_( 'COM_EASYBLOG_SELECT_BLOG_IMAGE' );?></a>
			<a href="javascript:void(0);" class="removeBlogImage buttons float-l"><i></i><span><?php echo JText::_( 'COM_EASYBLOG_REMOVE_BLOG_IMAGE' );?></span></a>
		</div>
	</div>
</div>
<?php }?>
<?php } ?>
