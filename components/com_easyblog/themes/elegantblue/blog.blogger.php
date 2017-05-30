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
<div id="ezblog-body">
	<!-- Blogger Block -->
	<div id="ezblog-detail" class="forBlogger mtl">
		<div id="blogger-<?php echo $blogger->id; ?>" class="profile-head<?php echo ($blogger->isFeatured() ) ? ' featured-blogger' : ''; ?> clearfix prel">
			<?php if ( $system->config->get('layout_avatar') ) : ?>
			<div class="blog-avatar float-l prel">
				<div class="avatar-wrap-a">
					<div class="author-avatar clearfix">
						<div class="float-l prel mls">
						<img src="<?php echo $blogger->getAvatar(); ?>" alt="<?php echo $blogger->getName(); ?>" width="60" class="avatar" />
						</div>
					</div>
				</div>
				<div class="avatar-wrap-b"></div>
			</div>
			<?php endif; ?>

			<div class="profile-basic">
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
					<?php echo $blogger->getWebsite() == '' ? '' : '<li><a href="' . $blogger->getWebsite() .'" target="_blank" class="link-globe"><span>' . $blogger->getWebsite() . '</span></a></li>'; ?>
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

					<?php if( $system->config->get('main_bloggersubscription') ) { ?>
					<li>
						<a class="link-subscribe" href="javascript:eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_BLOGGER; ?>' , '<?php echo $blogger->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?>">
							<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $system->config->get('main_rss') ) { ?>
					<li>
						<a class="link-rss" href="<?php echo $blogger->getRSS();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
							<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $system->admin ) : ?>
					<li>
						<?php if ( $blogger->isFeatured() ) { ?>
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
				$isMineBlog = EasyBlogHelper::isMineBlog($row->created_by, $system->my->id);
				$team   	= ( isset( $teamId) && ! empty($teamId)) ? $teamId : '';

				$this->set( 'team' , $team );
				$this->set( 'data' , array( $row ) );

				echo $this->fetch( 'blog.item' . EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php', array( 'row' => $row ) );
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
		<div class="pagination clearfix">
			<?php echo $pagination; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
