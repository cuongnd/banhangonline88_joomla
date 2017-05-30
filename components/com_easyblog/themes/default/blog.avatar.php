<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<!-- Avatar wrappers -->
<div class="blog-avatar float-l prel" itemscope itemtype="http://schema.org/Person" itemprop="author">
	<?php if( isset( $row->team_id ) && $system->config->get('layout_teamavatar' ) ){ ?>
	<?php
			$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
			$teamBlog->load( $row->team_id );
	?>
		<!-- Team avatars -->
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamBlog->id ); ?>" class="avatar isTeamBlog float-l prel">
			<img src="<?php echo $teamBlog->getAvatar(); ?>" alt="<?php echo $teamBlog->title; ?>" title="<?php echo $teamBlog->title; ?>" class="avatar" width="60" height="60" />
		</a>
		<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar isBlogger float-l pabs">
			<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" itemprop="image" class="avatar" width="30" height="30" />
		</a>
	<?php } else { ?>
		<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar float-l">
			<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $this->escape( $row->blogger->getName() ); ?>" class="avatar isBlogger" width="60" height="60" itemprop="image" />
			<meta itemprop="name" content="<?php echo $row->blogger->getName();?>" />
		</a>
	<?php } ?>
</div>