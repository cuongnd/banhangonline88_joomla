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
EasyBlog.require().script("legacy").done(function(){
	eblog.blog.tab.init();
});
</script>

<?php if(!empty($related) || $system->config->get('main_trackbacks') ) { ?>
<ul class="tab_button reset-ul float-li clearfix">
	<?php if( !empty( $related ) ) { ?>
	<li id="button-related" class="tab_item related"><a href="javascript:void(0);" class="ico"><span><?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_RELATED_POST_TAB'); ?></span></a></li>
	<?php } ?>

	<?php if( $system->config->get('main_trackbacks') ) { ?>
	<li id="button-trackbacks" class="tab_item trackbacks"><a href="javascript:void(0);" class="ico"><span><?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_TRACKBACKS_TAB'); ?></span></a></li>
	<?php } ?>
</ul>
<?php } ?>

<?php if(!empty($related) || $system->config->get('main_trackbacks') ) { ?>
<div class="tab-wrapper">
	<?php if(!empty($related)) { ?>
		<?php echo $this->fetch( 'blog.related.php' ); ?>
	<?php } ?>

	<?php if( $system->config->get('main_trackbacks') ) { ?>
		<?php echo $this->fetch( 'blog.trackback.php' ); ?>
	<?php } ?>
</div>
<?php } ?>

