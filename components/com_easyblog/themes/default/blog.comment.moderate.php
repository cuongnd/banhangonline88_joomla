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

<?php
$created	= EasyBlogDateHelper::dateWithOffSet($comment->created);
$counter	= (empty($totalComment)) ? 1 : $totalComment;
$indentDirection    = ( $this->getDirection() == 'rtl') ? 'right' : 'left';
?>

<div id="comment-<?php echo $comment->id; ?>" class="comment-style-<?php echo $counter % 2; ?> comment-row" <?php echo ($comment->depth > 0) ? 'style="margin-'.$indentDirection.':' . ($comment->depth * 30) . 'px;"' : ''; ?> >
	<a name="comment-<?php echo $comment->id; ?>"></a>
	<div class="comment-block clearfix">

		<?php
		/**
		* ----------------------------------------------------------------------------------------------------------
		* Comment Avatar
		* ----------------------------------------------------------------------------------------------------------
		*/
		?>
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
			<?php echo EasyBlogTooltipHelper::getBloggerHTML( $comment->poster->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'prev')) ); ?>
		<?php endif; ?>

		<?php
		/**
		* ----------------------------------------------------------------------------------------------------------
		* Comment content
		* ----------------------------------------------------------------------------------------------------------
		*/
		?>
		<div class="comment-content">

			<div class="comment-head prel">
			    <i class="comment-arrow pabs"></i>
				<span class="comment-author">
					<b><?php echo ($comment->created_by != 0) ? $comment->poster->getName() : $comment->name ?></b>
				</span>
				<span class="comment-date">
					<?php echo EasyBlogDateHelper::toFormat($created, $config->get('layout_dateformat', '%A, %d %B %Y')); ?>
				</span>

			</div>

			<div class="comment-body prel">
				<i class="comment-arrow pabs"></i>
				<div>
					<?php if($config->get('comment_requiretitle', 0)) : ?>
					<h4 class="comment-title rip mbs" id="comment-title-<?php echo $comment->id;?>"><?php echo $comment->title; ?></h4>
					<?php endif; ?>
					<div class="comment-text"><?php echo $comment->comment; ?></div>

					<div class="comment-message eblog-message info">
						<?php echo JText::_('COM_EASYBLOG_COMMENT_UNDER_MODERATE'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>