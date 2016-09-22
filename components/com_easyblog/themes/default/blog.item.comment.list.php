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
<?php if(count($row->comments) > 0) { ?>
<div class="blog-show-comments">
	<div class="show-comment-title">
		<span class="show-totalcomment"><?php echo $this->getNouns( 'COM_EASYBLOG_RECENT_COMMENT' , count( $row->comments ) ); ?></span>
		<span class="show-morecomment">
			-
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id.'#comment'); ?>"><?php echo JText::_('COM_EASYBLOG_SHOW_ALL_COMMENTS'); ?></a>
		</span>
	</div>

	<ul class="comment-list reset-ul list-full ptl">
		<?php foreach($row->comments as $item) { ?>
		<?php
			$commentPosterName  = ($item->created_by) ? $item->poster->getName() : $item->name;
		?>
		<li>
			<?php if ( $system->config->get('layout_avatar') ){ ?>
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->post_id.'#comment-' . $item->id); ?>" class="avatar comment-avatar float-l">
					<img src="<?php echo $item->poster->getAvatar();?>" width="36" height="36" class="avatar float-l" alt="<?php echo $commentPosterName;?>" />
				</a>
			<?php } ?>
			<div class="comment-brief">
				<div class="comment-author">
					<span>
						<b><?php echo $commentPosterName; ?></b> <?php echo JText::_( 'COM_EASYBLOG_SAYS' );?>
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->post_id.'#comment-' . $item->id); ?>" class="small">#</a>
					</span>
				</div>
				<div class="comment-says prel">
					<i class="comment-arrow pabs"></i>
					
					<?php if($system->config->get('comment_requiretitle', 0) && !empty($item->title)){ ?>
						<b><?php echo (JString::strlen($item->title) > 30) ? JString::substr(strip_tags($item->title), 0, 30) . '...' : strip_tags($item->title) ; ?></b>
					<?php } ?>
					<div>
						<?php if( JString::strlen( $item->comment ) > 130 ){ ?>
						<?php echo JString::substr( strip_tags( EasyBlogCommentHelper::parseBBCode( $item->comment ) ) , 0 , 130 ); ?>
						<?php } else { ?>
						<?php echo strip_tags( EasyBlogCommentHelper::parseBBCode( $item->comment ) ); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>