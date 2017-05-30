<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<h3><?php echo JText::_( 'COM_EASYDISCUSS_TAGS' );?></h3>
<hr />
<?php if( $tags ){ ?>
<ul class="discuss-tag-list unstyled">
	<?php foreach( $tags as $tag ) { ?>
	<li>
		<div class="discuss-tag">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id);?>" class="tag-name">
				<!-- <i class="icon-tag"></i> -->
				<?php echo $tag->title; ?>
			</a>
			<?php if( $system->config->get( 'main_rss') ){ ?>
			<a href="<?php echo DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag->id );?>" class="pull-right btn-tag-rss">
				<span><?php echo $tag->post_count;?></span>
				<i class="icon-rss"></i>
			</a>
			<?php } else { ?>
			<span class="pull-right btn-tag"><?php echo $tag->post_count;?></span>
			<?php } ?>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_TAGS_YET' );?>
</div>
<?php } ?>
