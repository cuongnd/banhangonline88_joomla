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
<div id="ezblog-body">
<script type="text/javascript">
EasyBlog.ready(function()
{
	eblog.tags.search.init();
});
</script>
	<div id="ezblog-section">
		<span><?php echo JText::_('COM_EASYBLOG_TAGS_PAGE_HEADING');?></span>
	</div>
	<div class="tag-sort-order clearfix pbm">
		<ul class="reset-ul float-li float-l">
			<li class="sorting-item tag-title">
				<a href="<?php echo EasyBlogRouter::_( $titleURL );?>"<?php echo $ordering == 'title' || empty( $ordering ) ? ' class="active"' : '';?> rel="nofollow"><?php echo JText::_( 'COM_EASYBLOG_TAGS_ORDER_BY_TITLE' );?></a>
			</li>
			<li class="sorting-item posts">
				<a href="<?php echo EasyBlogRouter::_( $postURL );?>"<?php echo $ordering == 'postcount' ? ' class="active"' : '';?> rel="nofollow"><?php echo JText::_( 'COM_EASYBLOG_TAGS_ORDER_BY_POST_COUNT' );?></a>
			</li>
			<li class="ordering-item asc">
				<a href="<?php echo EasyBlogRouter::_( $ascURL );?>"<?php echo $sorting == 'asc' ? ' class="active"' : '';?> rel="nofollow"><?php echo JText::_( 'COM_EASYBLOG_TAGS_SORT_BY_ASC' );?></a>
			</li>
			<li class="ordering-item des">
				<a href="<?php echo EasyBlogRouter::_( $descURL );?>"<?php echo $sorting == 'desc' ? ' class="active"' : '';?> rel="nofollow"><?php echo JText::_( 'COM_EASYBLOG_TAGS_SORT_BY_DESC' );?></a>
			</li>
			<li class="tag-search">
				<input type="text" id="filter-tags" name="filter-tags" class="ffa fsg fwb" />
			</li>
	    </ul>
	</div>

	<?php if( $tags ) { ?>
	<ul class="post-tags column3 reset-ul float-li clearfix ptm">
	    <?php foreach($tags as $tag) { ?>
	    <li>
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id);?>">
                <span><?php echo JText::_( $tag->title ); ?> (<?php echo $tag->post_count;?>)</span>
            </a>
			<?php if( $system->config->get( 'main_rss' ) ){ ?>
			<a href="<?php echo  EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=tags&layout=listings&id=' . $tag->id , false , 'tag' );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="ico link-rss-s"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></a>
			<?php } ?>
	    </li>
	    <?php } ?>
	</ul>
	<?php } else { ?>
		<div><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
	<?php } ?>
</div>
