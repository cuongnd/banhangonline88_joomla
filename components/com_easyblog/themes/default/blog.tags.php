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
	<div id="ezblog-label">
		<?php if( $system->config->get( 'main_rss' ) ){ ?>
		<a href="<?php echo  EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=tags&layout=listings&id=' . $tag->id, false, 'tag' );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS'); ?>" class="float-r link-rss"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS'); ?></a>
		<?php } ?>
		<span><?php echo JText::sprintf('COM_EASYBLOG_VIEWING_ENTRIES_TAGGED' , JText::_( $tag->title ) ); ?></span>
	</div>

	<?php if( $privateBlogCount > 0 || $teamBlogCount > 0 ) : ?>
    <div class="eblog-message info">
        <div>
		<?php if ( $privateBlogCount > 0 && $teamBlogCount > 0) { ?>
        	<div><?php echo JText::sprintf('COM_EASYBLOG_TAG_PRIVATE_AND_TEAM_BLOG_INFO', $privateBlogCount , $teamBlogCount ); ?></div>
        <?php } else if($privateBlogCount > 0) { ?>
        	<?php echo $this->getNouns( 'COM_EASYBLOG_TAG_PRIVATE_BLOG' , $privateBlogCount , true );?>
        <?php } else if($teamBlogCount > 0) { ?>
        	<?php echo $this->getNouns( 'COM_EASYBLOG_TAG_TEAM_BLOG_INFO' , $teamBlogCount , true );?>
        <?php } ?>
        </div>
    </div>
    <?php endif; ?>

    <div id="ezblog-posts" class="forTags">
		<?php if( $rows ){ ?>
			<?php foreach( $rows as $row ){ ?>
				<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
			<?php } ?>
		<?php } else { ?>
			<div class="mtl"><?php echo JText::sprintf( 'COM_EASYBLOG_TAGS_NO_ENTRIES_TAGGED' , $tag->title );?></div>
		<?php } ?>

		<?php if( $pagination ){ ?>
    	<div class="eblog-pagination"><?php echo $pagination->getPagesLinks(); ?></div>
    	<?php } ?>
    </div>
</div>
