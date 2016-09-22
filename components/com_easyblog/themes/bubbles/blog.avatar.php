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
<div class="blog-avatar float-l prel mrl">
	<?php
		if( isset( $row->team_id ) )
		{
			$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
			$teamBlog->load( $row->team_id );
	?>
	<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id ); ?>" class="avatar isTeamBlog float-l prel">
		<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" class="avatar" style="width:38px !important; height:38px !important;" width="38" height="38" />
	</a>
	<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar isBlogger float-l pabs">
		<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $row->blogger->getName(); ?>" class="avatar" style="width:20px !important; height:20px !important;" width="20" height="20" />
	</a>
	<?php
		} else {
	?>
	<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar float-l">
		<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $row->blogger->getName(); ?>" class="avatar isBlogger" style="width:38px !important; height:38px !important;" width="38" height="38" />
	</a>
	<?php } ?>
	<meta itemprop="name" value="<?php echo $this->escape( $row->blogger->getName() ); ?>" />
</div>
