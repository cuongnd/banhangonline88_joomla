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
<!-- Avatar wrappers -->
<div class="blog-avatar float-l prel">
	<div class="avatar-wrap-a">
		<div class="author-avatar clearfix">
			<div class="float-l prel mls">
			<?php
				if( isset( $row->team_id ) )
				{
					$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
					$teamBlog->load( $row->team_id );
			?>
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id ); ?>" class="avatar isTeamBlog float-l prel">
				<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" class="avatar" width="60" height="60" />
			</a>
			<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar isBlogger float-l pabs">
				<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" class="avatar" style="width:30px !important; height:30px !important;" width="30" height="30" />
			</a>
			<?php
				} else {
			?>
			<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar float-l">
				<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" class="avatar isBlogger" style="width:60px !important; height:60px !important;" width="60" height="60" />
			</a>
			<?php } ?>
			</div>
		</div>

		<?php if( $this->getParam( 'show_author' ) ){ ?>
		<div class="blog-author">
			<?php echo JText::sprintf( 'COM_EASYBLOG_BY_AUTHOR_NAME_LINK', $row->blogger->getProfileLink(), $row->blogger->getName()); ?>
		</div>
		<?php echo EasyBlogTooltipHelper::getBloggerHTML( $row->created_by, array('my'=>'left top','at'=>'right bottom','of'=>array('traverseUsing'=>'prev')) ); ?>
		<?php } ?>

	</div>
	<div class="avatar-wrap-b"></div>
</div>