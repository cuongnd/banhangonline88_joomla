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

$team   = isset( $team ) ? $team : '';
?>
<?php if(count($row->comments) > 0) { ?>
<div class="blog-show-comments">
	<h4 class="rip">
		<span><?php echo $this->getNouns( 'COM_EASYBLOG_RECENT_COMMENT' , count( $row->comments ) ); ?></span>
		<span class="showmore"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id.'#comment'); ?>"><?php echo JText::_('COM_EASYBLOG_SHOW_ALL_COMMENTS'); ?></a></span>
	</h4>

	<ul class="comment-list reset-ul list-full ptm">
		<?php foreach($row->comments as $item) {
			$commentPosterName  = ($item->created_by) ? $item->poster->getName() : $item->name;
		?>
		<li>
		    <?php if ( $system->config->get('layout_avatar') ){ ?>
            <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->post_id.'#comment-' . $item->id); ?>" class="comment-avatar avatar float-l">
                <img src="<?php echo $item->poster->getAvatar();?>" width="35" height="35" class="avatar float-l" alt="<?php echo $commentPosterName;?>" />
            </a>
            <?php echo EasyBlogTooltipHelper::getBloggerHTML( $item->poster->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'prev')) ); ?>
                <?php } ?>
            <div class="comment-brief">
                <div class="comment-author fsm"><b><?php echo $commentPosterName; ?></b> <?php echo JText::_( 'COM_EASYBLOG_SAYS' );?></div>
                <div class="comment-says">
				    <?php if($system->config->get('comment_requiretitle', 0) && !empty($item->title)) : ?>
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->post_id.'#comment-' . $item->id); ?>">
						<?php echo (JString::strlen($item->title) > 30) ? JString::substr(strip_tags($item->title), 0, 30) . '...' : strip_tags($item->title) ; ?>
					</a>
					<span><?php echo (JString::strlen($item->comment) > 100) ? JString::substr(strip_tags($item->comment), 0, 100) . '...' : strip_tags($item->comment) ; ?></span>
					<?php else : ?>
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->post_id.'#comment-' . $item->id); ?>">
						<?php echo (JString::strlen($item->comment) > 130) ? JString::substr(strip_tags($item->comment), 0, 130) . '...' : strip_tags($item->comment) ; ?>
					</a>
					<?php endif; ?>
                </div>
            </div>
		</li>
		<?php }//end foreach ?>
	</ul>
</div>
<?php }//end if count ?>
