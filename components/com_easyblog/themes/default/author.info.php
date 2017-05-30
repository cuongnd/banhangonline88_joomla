<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<!-- Author wrapper -->
<div id="section-author" class="blog-section clearfix mts mbs">

	<div class="ptl pbl">
		<!-- Author avatar -->
		<?php if( $system->config->get( 'layout_avatar' ) ){ ?>
			<img src="<?php echo $blogger->getAvatar();?>" class="avatar float-l mrm" width="64" height="64" />
		<?php } ?>

		<div class="author-info">
			<div class="author-name rip mbs">
				<a href="<?php echo $blogger->getProfileLink();?>" rel="author" itemprop="author"><?php echo $blogger->getName(); ?></a>

				<?php echo EasyBlogHelper::getHelper( 'AUP' )->getPoints( $blogger->id ); ?>

				<?php echo EasyBlogHelper::getHelper( 'EasySocial' )->getPoints( $blogger->id ); ?>

				<?php if( $system->config->get('main_google_profiles' ) ){ ?>
				<?php
				$params 	= EasyBlogHelper::getRegistry();
				$params->load( $blogger->get( 'params' ) );

				$googleURL	= $params->get( 'google_profile_url');
				if( !empty( $googleURL ) && $params->get( 'show_google_profile_url' ) )
				{
				?>
					( <a href="<?php echo $this->escape( $googleURL );?>" rel="author" <?php echo $params->get( 'show_google_profile_url' ) ? '' : 'style="display: none;"';?>><?php echo JText::_('COM_EASYBLOG_VIEW_BLOGGER_ON_GOOGLE' );?></a> )
				<?php
				}
				?>
				<?php } ?>
			</div>

			<?php if ( $blogger->getBioGraphy() != '' ){ ?>
				<div class="author-about"><?php echo $blogger->getBioGraphy(); ?></div>
			<?php } ?>

			<?php if( $blogger->getWebsite() != '' ){ ?>
				<div class="author-url small mts"><a href="<?php echo $this->escape( $blogger->getWebsite() ); ?>" target="_blank" class="author-url" rel="nofollow"><?php echo $this->escape( $blogger->getWebsite() ); ?></a></div>
			<?php } ?>

			<?php echo EasyBlogHelper::getHelper( 'AUP' )->getMedals( $blogger->id ); ?>

			<?php echo EasyBlogHelper::getHelper( 'AUP' )->getRanks( $blogger->id ); ?>
		</div>

		<div class="clear"></div>

		<div class="author-meta profile-connect mtm">
			<ul class="connect-links reset-ul float-li">

				<?php if( $blogger->getTwitterLink() != '' ){ ?>
				<li>
					<a href="<?php echo $blogger->getTwitterLink();?>"><span><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?></span></a>
				</li>
				<?php } ?>

				<?php if ( EasyBlogHelper::getHelper( 'Messaging' )->getHTML( $blogger->id ) ){ ?>
				<!-- Jomsocial messaging -->
				<li><?php echo EasyBlogHelper::getHelper( 'Messaging' )->getHTML( $blogger->id ); ?></li>
				<?php } ?>

				<?php if ( EasyBlogHelper::getHelper( 'Friends' )->getHTML( $blogger->id ) ){ ?>
				<!-- Jomsocial friends -->
				<li><?php echo EasyBlogHelper::getHelper( 'Friends' )->getHTML( $blogger->id ); ?></li>
				<?php } ?>

				<?php if ( EasyBlogHelper::getHelper( 'Followers' )->getHTML( $blogger->id ) ){ ?>
				<!-- Jomsocial friends -->
				<li><?php echo EasyBlogHelper::getHelper( 'Followers' )->getHTML( $blogger->id ); ?></li>
				<?php } ?>


				<?php if( $blogger->getProfileLink() !== false ) { ?>
				<li><a href="<?php echo $blogger->getProfileLink();?>" class="author-profile"><span><?php echo JText::_( 'COM_EASYBLOG_AUTHOR_VIEW_PROFILE' );?></span></a></li>
				<?php } ?>

				<li>
					<a class="author-profile" href="<?php echo $blogger->getPermalink(); ?>" title="<?php echo JText::_( 'COM_EASYBLOG_AUTHOR_VIEW_MORE_POSTS' );?>"><span><?php echo JText::_( 'COM_EASYBLOG_AUTHOR_VIEW_MORE_POSTS' );?></span></a>
				</li>

				<?php if( $system->config->get('main_bloggersubscription') ) { ?>
				<li>
					<a class="link-subscribe" href="javascript:void(0);" onclick="eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_BLOGGER; ?>' , '<?php echo $blogger->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE'); ?>">
						<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>

		<?php echo EasyBlogHelper::getHelper( 'Achievements' )->getHTML( $blogger->id ); ?>

		<?php if( $system->config->get('main_showauthorposts') && !empty($authorRecentPosts) && ( count( $authorRecentPosts ) > 0 ) ) { ?>
		<div class="clear"></div>		
		<div class="mtm">
			<?php echo JText::_('COM_EASYBLOG_AUTHOR_RECENT_POSTS'); ?>
			<ul class="entry-related-post reset-ul">
			<?php foreach( $authorRecentPosts as $aitem ){ ?>
				<li id="entry_<?php echo $aitem->id; ?>">
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$aitem->id); ?>"><?php echo $aitem->title; ?></a> 
					 - <a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $aitem->category_id );?>" class="blog-category small"><?php echo $this->escape( JText::_( $aitem->category ) ); ?></a>
					<span class="blog-date float-r small"><?php echo $this->formatDate( $system->config->get('layout_shortdateformat'), $aitem->created ); ?></span>
				</li>
			<?php } ?>
		    </ul>
	    </div>
		<?php } ?>

	</div>

</div>
