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
	<?php if( $this->getParam( 'show_blogger_filterbar') ){ ?>
		<!-- Filtering and search form that appears at the top of the bloggers page -->
		<?php echo $this->fetch( 'blog.bloggers.search.php' , array( 'search' => $search ) ); ?>
	<?php } ?>

	<div id="ezblog-section"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PAGE_HEADING'); ?></div>
	
	<div id="ezblog-bloggers">
		<?php foreach($data as $row) { ?>
		<div id="blogger-<?php echo $row->id; ?>" class="profile-item<?php echo ( $row->blogger->isFeatured() ) ? ' featured-blogger' : ''; ?> clearfix" >
			<div class="profile-head">

				<?php if ( $system->config->get('layout_avatar') ){ ?>
				<!-- Avatar in bloggers listing -->
				<div class="profile-avatar float-l">
					<i class="pabs"></i>
					<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="avatar">
						<img src="<?php echo $row->blogger->getAvatar(); ?>" alt="<?php echo $row->blogger->getName(); ?>" class="avatar" width="50"  height="50" />
					</a>
				</div>
				<?php } ?>

				<div class="profile-info">
					<h3 class="profile-title rip mbm">
						<a href="<?php echo $row->blogger->getProfileLink(); ?>"><?php echo $row->blogger->getName(); ?></a>
						<?php if ($row->blogger->isFeatured() ){ ?>
							<sup class="tag-featured"><?php echo JText::_( 'COM_EASYBLOG_FEATURED_BLOGGER_FEATURED' );?></sup>
						<?php } ?>
					</h3>

					<?php if( $row->blogger->getBiography() ){ ?>
					<div class="profile-bio">
						<div class="mbs"><?php echo $row->blogger->getBiography(); ?></div>
					</div>
					<?php } ?>

					<div class="profile-connect mtm pbs">
						<ul class="connect-links reset-ul float-li">
							<?php if( $row->blogger->getWebsite() != '' ){ ?>
							<li>
								<a href="<?php echo $this->escape( $row->blogger->getWebsite() );?>" target="_blank" class="link-website"><span><?php echo $this->escape( $row->blogger->getWebsite() );?></span></a>
							</li>
							<?php } ?>

							<?php if( $system->config->get('main_bloggersubscription') ) { ?>
							<!-- Blogger subscription links -->
							<li>
								<a class="link-subscribe" href="javascript:void(0);" onclick="eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_BLOGGER; ?>' , '<?php echo $row->id;?>');">
									<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
								</a>
							</li>
							<?php } ?>
						
							<?php if( $system->config->get('main_rss') ) { ?>
							<li>
								<a class="link-rss" href="<?php echo $row->rssLink;?>">
									<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>

				</div>

				<div class="clear"></div>
			</div>

			<?php if( $this->getParam( 'show_bloggerstats') || $this->getParam( 'show_bloggerstatsitem')) { ?>

			<!-- Blog content wrapper -->
			<div class="profile-body clearfix">
				<?php if( $this->getParam( 'show_bloggerstats') ) { ?>
				<div class="profile-sidebar">
					<div class="profile-brief">
						<div class="in">
							<ul class="profile-stats reset-ul clearfix">
								<?php if( $row->blogger->getProfileLink() !== false ) { ?>
								<li>
									<a href="<?php echo $row->blogger->getProfileLink();?>"><?php echo JText::_( 'COM_EASYBLOG_AUTHOR_VIEW_PROFILE' );?></a>
								</li>
								<?php } ?>
								<?php if ( EasyBlogHelper::getHelper( 'Messaging' )->getHTML( $row->id ) ) : ?>
								<li>
									<?php echo EasyBlogHelper::getHelper( 'Messaging' )->getHTML( $row->id ); ?>
								</li>
								<?php endif; ?>
								<?php if ( !empty( $row->twitterLink ) ) : ?>
								<li>
									<a class="link-twitter" href="<?php echo $row->twitterLink; ?>" title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>">
										<span><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?></span>
									</a>
								</li>
								<?php endif; ?>

								<?php if( $system->admin ){ ?>
								<?php if( $row->blogger->isFeatured() ) { ?>
								<li>
									<a href="javascript:eblog.featured.remove('blogger','<?php echo $row->id;?>');"  <?php echo ($row->blogger->isFeatured() ) ? '' : 'style="display:none;"';?> class="feature-del">
										<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE'); ?></span>
									</a>
								</li>
								<?php } else { ?>
								<li>
									<a href="javascript:eblog.featured.add('blogger','<?php echo $row->id;?>');" <?php echo ($row->blogger->isFeatured() ) ? 'style="display:none;"' : '';?> class="feature-add">
										<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS'); ?></span>
									</a>
								</li>
								<?php } ?>
								<?php } ?>
								<li class="total-post">
									<span class="traits float-r"><?php echo $row->blogCount;?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TOTAL_POSTS' );?></span>
								</li>

								<?php if( EasyBlogHelper::getHelper( 'Comment' )->isBuiltin() && $system->config->get('main_comment')){ ?>
								<li class="total-comment">
									<span class="traits float-r"><?php echo $row->commentsCount;?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TOTAL_COMMENTS' );?></span>
								</li>
								<?php } ?>

								<li class="total-categories">
									<span class="traits float-r"><?php echo count($row->categories); ?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TOTAL_CATEGORIES' );?></span>

									<?php if(count($row->categories) > 0) : ?>
									<ul class="list-square reset-ul mts">
										<?php for($i = 0; $i < count($row->categories); $i++) : ?>
										<li>
										<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=statistic&id='.$row->id. '&stat=category&catid='.$row->categories[$i]->id); ?>">
											<?php echo JText::_( $row->categories[$i]->title ); ?>
										</a>
										</li>
										<?php endfor; ?>
									</ul>
									<?php endif; ?>
								</li>
								<li class="total-tag">
									<span class="traits float-r"><?php echo count( $row->tags );?></span>
									<span><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TAGS' );?></span>
									<div class="clear"></div>
									<div class="tag-list mts">
									<?php
									if( $row->tags )
									{
										$i  = 1;
										foreach( $row->tags as $tag )
										{
											$delimeter  = $i == count( $row->tags ) ? '' : ',';
									?>
										<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=statistic&id='.$row->id.'&stat=tag&tagid=' . $tag->id );?>"><?php echo JText::_( $tag->title ); ?></a><?php echo $delimeter; ?>
									<?php
											$i++;
										}
									}
									?>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if( $this->getParam( 'show_bloggerstatsitem') ) { ?>

				<div class="profile-main">
				<?php if( !empty( $row->blogs ) ) { ?>
					<h4 class="rip mbm"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_RECENT_POSTS' );?></h4>

					<ul class="post-list reset-ul">
						<?php foreach( $row->blogs as $entry ){ ?>
							<?php if( $system->config->get( 'main_password_protect' ) && !empty( $entry->blogpassword ) ){ ?>
								<!-- Password protected theme files -->
								<?php echo $this->fetch( 'blog.bloggers.protected.php' , array( 'entry' => $entry ) ); ?>
							<?php } else { ?>
								<!-- Normal post theme files -->
								<?php echo $this->fetch( 'blog.item.simple'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $entry->source ) . '.php' , array( 'entry' => $entry , 'customClass' => 'blogger' ) ); ?>
							<?php } ?>

						<?php } ?>

						<li class="post-listmore fwb">
							<div>
								<?php echo JText::sprintf('COM_EASYBLOG_BLOGGERS_OTHER_ENTRIES', EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&id=' . $row->blogger->id . '&layout=listings' ), $row->blogger->getName() ); ?>
							</div>
						</li>
					</ul>
				<?php } else { ?>
					<div class="profile-nopost"><?php echo JText::sprintf('COM_EASYBLOG_BLOGGERS_NO_POST_YET', $row->blogger->getName() ); ?></div>
				<?php } ?>
				</div>

				<?php } // end statitem ?>


			</div><!--end: .profile-body -->

			<?php } // end outer if ?>

			<div class="clear"></div>
		</div>
		<?php } ?>
	</div>

	<?php if( $pagination ){ ?>
		<div class="eblog-pagination clearfix"><?php echo $pagination; ?></div>
	<?php } ?>
</div>