<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="ezblog-body">
	<!-- Blogger Block -->
	<div id="ezblog-detail" class="forBlogger mtl">
		<div id="blogger-<?php echo $blogger->id; ?>" class="profile-head<?php echo ($blogger->isFeatured() ) ? ' featured-blogger' : ''; ?> clearfix prel">
			<?php if ( $system->config->get('layout_avatar') ) : ?>
			<div class="profile-avatar float-l prel">
				<i class="pabs"></i>
				<img src="<?php echo $blogger->getAvatar(); ?>" alt="<?php echo $blogger->getName(); ?>" width="80" class="avatar" />
			</div>
			<?php endif; ?>
			<div class="profile-info">
				<h3 class="profile-title rip mbm">
					<a href="<?php echo $blogger->getProfileLink(); ?>"><?php echo $blogger->getName(); ?></a>
					<?php if ($blogger->isFeatured() ) : ?>
					<sup class="tag-featured"><?php echo JText::_( 'COM_EASYBLOG_FEATURED_BLOGGER_FEATURED' );?></sup>
					<?php endif; ?>
				</h3>

				<?php if ( !empty( $twitterLink ) || $blogger->getWebsite() ) { ?>
				<div class="profile-connect mbm">
					<ul class="connect-links reset-ul float-li clearfix">
					<?php if ( !empty( $twitterLink ) ) { ?>
					<li><a class="link-twitter" href="<?php echo $twitterLink; ?>" title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>"><span><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?></span></a></li>
					<?php } ?>
					<?php echo $blogger->getWebsite() == '' ? '' : '<li><a href="' . $this->escape( $blogger->getWebsite() ) .'" target="_blank" class="link-globe"><span>' . $this->escape( $blogger->getWebsite() ) . '</span></a></li>'; ?>
					</ul>
				</div>
				<?php } ?>

				<div class="profile-bio mts">
					<?php echo $blogger->getBiography();?>
				</div>
				<div class="profile-connect ptm">
					<ul class="connect-links reset-ul float-li clearfix">
					<?php $html = EasyBlogHelper::getHelper( 'Messaging' )->getHTML( $blogger->id ); ?>
					<?php if( !empty($html)){ ?>
						<li><?php echo $html;?></li>
					<?php } ?>

					<?php if( $system->config->get('main_bloggersubscription') && $system->my->id != $blogger->id ) { ?>
					<li>
						<a class="link-subscribe" href="javascript:eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_BLOGGER; ?>' , '<?php echo $blogger->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?>">
							<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $system->config->get('main_rss') ) { ?>
					<li>
						<a class="link-rss" href="<?php echo $this->escape( $blogger->getRSS() );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
							<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $system->admin ) : ?>
					<li>
						<?php if ( !$blogger->isFeatured() ) { ?>
						<a href="javascript:eblog.featured.add('blogger','<?php echo $blogger->id;?>');" class="feature-add">
							<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS'); ?></span>
						</a>
						<?php } else { ?>
						<a href="javascript:eblog.featured.remove('blogger','<?php echo $blogger->id;?>');" class="feature-del">
							<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE'); ?></span>
						</a>
						<?php } ?>
					</li>
					<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div><!--end: .profile-head-->
	</div>

	<div id="ezblog-posts" class="forBlogger">
	<?php if(isset($statType)) : ?>
		<div>
			<h2><?php echo ($statType == 'tag') ? JText::sprintf( 'COM_EASYBLOG_BLOGGER_STAT_TAG' , $statObject->title) : JText::sprintf('COM_EASYBLOG_BLOGGER_STAT_CATEGORY', $statObject->title); ?></h2>
		</div>
		<?php endif ?>
		<?php
		if(!empty($blogs))
		{
			foreach ($blogs as $row)
			{
		?>
			<?php if( $system->config->get( 'main_password_protect' ) && !empty( $row->blogpassword ) ){ ?>
				<!-- Password protected theme files -->
				<?php echo $this->fetch( 'blog.item.protected.php' , array( 'row' => $row ) ); ?>
			<?php } else { ?>
				<!-- Normal post theme files -->
				<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
			<?php } ?>
		<?php
			}
		}
		else
		{
		?>
		<div class="eblog-message info"><?php echo JText::sprintf('COM_EASYBLOG_BLOGGERS_NO_POST_YET' , $blogger->getName() ); ?></div>
		<?php
		}
		?>

		<?php if ( $pagination ) : ?>
		<div class="eblog-pagination clearfix">
			<?php echo $pagination; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
