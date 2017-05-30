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


$indentDirection  = ($this->getDirection() == 'rtl') ? 'right' : 'left';

?>

<?php
$created	= EasyBlogDateHelper::dateWithOffSet($comment->created);
$counter	= (empty($totalComment)) ? 1 : $totalComment;
?>

<li id="comment-<?php echo $comment->id; ?>" class="comment-row comment-style-<?php echo $counter % 2; ?> comment-box<?php echo ($comment->depth > 0) ? ' comment-replied' : '';?>" <?php echo ($comment->depth > 0) ? 'style="margin-'.$indentDirection.':' . ($comment->depth * 30) . 'px;"' : ''; ?> >
	<a name="comment-<?php echo $comment->id; ?>"></a>
	<div class="comment-block clearfix">

		<?php if ( $config->get('layout_avatar') ) : ?>
			<?php
			    $avatarLink 	= 'javascript:void(0);';
			    $avatarTitle    = '';
				if( $comment->poster->id != 0 ){
				    $avatarLink		= $comment->poster->getProfileLink();
				}
			?>
			<a href="<?php echo $avatarLink; ?>" class="comment-avatar avatar float-l">
				<img src="<?php echo $comment->poster->getAvatar(); ?>" alt="<?php echo $comment->poster->getName(); ?>" class="avatar" />
			</a>
		<?php endif; ?>

		<div class="comment-content">

			<div class="comment-head prel fsm">
			    <i class="comment-arrow pabs"></i>
				<span class="comment-author">
                        <?php
                            $commentAuthor  = ($comment->created_by != 0) ? $comment->poster->getName() : $comment->name;
                            if( !empty($comment->url))
                            {
                                $commentAuthor    = EasyBlogHelper::getHelper( 'String' )->htmlAnchorLink( $comment->url, $commentAuthor);
                            }
                        ?>
					<b><?php echo $commentAuthor; ?></b>
				</span>
				<span class="comment-date">
					<?php echo EasyBlogDateHelper::toFormat($created, $config->get('layout_dateformat', '%A, %d %B %Y')); ?>
				</span>
                <span class="comment-action small fsm">
					<?php if( ($my->id == $comment->created_by && $this->acl->rules->edit_comment || $system->admin ) && $my->id != 0 ) { ?>
						<b>&middot;</b>
						<a href="javascript:eblog.comments.edit( '<?php echo $comment->id;?>' );"><?php echo JText::_( 'COM_EASYBLOG_COMMENT_EDIT' ); ?></a>
					<?php } ?>
					<?php if( $system->admin || ( $my->id == $comment->created_by && $this->acl->rules->delete_comment ) && $my->id != 0  ) { ?>
						<b>&middot;</b>
						<a href="javascript:eblog.comments.remove( '<?php echo $comment->id;?>' );"><?php echo JText::_( 'COM_EASYBLOG_COMMENT_DELETE' ); ?></a>
					<?php } ?>
                </span>
			</div>

			<div class="comment-body prel">
				<i class="comment-arrow pabs"></i>
				<div>
					<?php if( !empty( $comment->title ) ){ ?>
					<h4 class="comment-title rip mbs" id="comment-title-<?php echo $comment->id;?>"><?php echo $comment->title; ?></h4>
					<?php } ?>
					<div class="comment-text"><?php echo $comment->comment; ?></div>
				</div>
			</div>


			<?php
			/**
			* ----------------------------------------------------------------------------------------------------------
			* User control
			* ----------------------------------------------------------------------------------------------------------
			*/
			?>
			<div class="comment-control fsm">
				<?php if ( ($this->acl->rules->allow_comment || (empty($my->id) && $config->get('main_allowguestcomment'))) && ( ($comment->depth + 1) < $config->get('comment_maxthreadedlevel')) ) { ?>
					<span id="toolbar-<?php echo $comment->id; ?>" class="comment-reply">
						<span id="toolbar-reply-<?php echo $comment->id; ?>" class="comment-reply-yes show-this">
							<a href="javascript:eblog.comment.reply('<?php echo $comment->id; ?>', '<?php echo $comment->depth + 1;?>', <?php echo $config->get('comment_autotitle', 0); ?>);" class="reply"><?php echo JText::_('COM_EASYBLOG_REPLY'); ?></a>
						</span>
						<span id="toolbar-cancel-<?php echo $comment->id; ?>" class="comment-reply-no">
							<a href="javascript:eblog.comment.cancel('<?php echo $comment->id; ?>');" class="cancel"><?php echo JText::_('COM_EASYBLOG_CANCEL'); ?></a>
						</span>
					</span>
				<?php } ?>


			<?php if($config->get('comment_likes')) { ?>
				<span class="comment-like">
					<span id="likes-container-<?php echo $comment->id;?>" class="likes-container" style="display:<?php echo (empty($comment->likesAuthor)) ? 'none': 'inline';?>;" >
						<b>&middot;</b>
						<?php echo $comment->likesAuthor;?>
					</span>
					<?php if($config->get('comment_likes') && $my->id != 0) { ?>
						<span id="likes-<?php echo $comment->id;?>">
						<?php if(empty($comment->isLike)) { ?>
							<b>&middot;</b>
							<a href="javascript:eblog.comment.likes('<?php echo $comment->id; ?>', '1', '0');" class="likes"><?php echo JText::_('COM_EASYBLOG_LIKES');?></a>
						<?php } else { ?>
							<a href="javascript:eblog.comment.likes('<?php echo $comment->id; ?>', '0', '<?php echo $comment->isLike;?>');" class="likes"><?php echo JText::_('COM_EASYBLOG_UNLIKE');?></a>
						<?php } ?>
						</span>
					<?php } ?>
				</span>
			<?php } ?>
			</div>
		</div>

		<div id="comment-reply-form-<?php echo $comment->id; ?>" style="display:none;"></div>
	</div>
</li>
