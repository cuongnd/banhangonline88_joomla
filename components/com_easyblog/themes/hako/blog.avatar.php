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
<div class="blog-avatar float-l prel"><i class="folded pabs"></i>
	<?php if( isset( $row->team_id ) ){ ?>
	<?php
			$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
			$teamBlog->load( $row->team_id );
	?>
		<!-- Team avatars -->
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id ); ?>" class="avatar isTeamBlog float-l prel">
			<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" class="avatar" style="width:60px !important; height:60px !important;" width="60" height="60" />
		</a>
		<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar isBlogger float-l pabs">
			<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" style="width:30px !important; height:30px !important;" class="avatar" width="30" height="30" />
		</a>
	<?php } else { ?>
		<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar float-l">
			<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" class="avatar isBlogger" width="60" height="60" />
		</a>
	<?php } ?>
</div>
