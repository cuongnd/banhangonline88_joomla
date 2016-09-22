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
	<div id="ezblog-section">
		<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_PAGE_HEADING'); ?></span>
	</div>
	<div id="ezblog-teamblog">
		<?php foreach($teams as $row) { ?>
		<div id="team-blog_<?php echo $row->id; ?>" class="profile-item clearfix">
			<div class="profile-head">
				<?php if($system->config->get('layout_teamavatar', true)) : ?>
				<div class="profile-avatar float-l">
					<i class="pabs"></i>
					<a href="<?php echo  EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$row->id);?>" class="avatar">
						<img src="<?php echo $row->avatar; ?>" alt="<?php echo $this->escape( $row->title ); ?>" width="50" height="50" class="avatar" />
					</a>
				</div>
				<?php endif; ?>

				<div class="profile-info">
					<h3 class="profile-title rip">
						<a href="<?php echo  EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$row->id);?>"><?php echo $row->title;?></a>
						<?php if ($row->isFeatured) : ?><sup class="tag-featured cap"><?php echo JText::_( 'COM_EASYBLOG_FEATURED_TEAMBLOG_FEATURED' );?></sup><?php endif; ?>
					</h3>
					<div class="profile-bio mtm">
						<?php if(! empty( $row->description )) : ?>
						<?php   echo nl2br($row->description); ?>
						<?php endif; ?>
					</div>

					<div class="profile-connect mtm pbs">
						<ul class="connect-links reset-ul float-li">
						<?php if( $row->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $row->isMember || EasyBloghelper::isSiteAdmin() && ($system->config->get('main_teamsubscription')) ){ ?>
							<li>
								<a class="link-subscribe" href="javascript:eblog.subscription.show('<?php echo EBLOG_SUBSCRIPTION_TEAMBLOG; ?>','<?php echo $row->id;?>');">
									<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM'); ?></span>
								</a>
							</li>
						<?php } ?>

						<?php if( ($row->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $row->isMember || EasyBloghelper::isSiteAdmin() ) && ($system->config->get('main_rss')) ){ ?>
							<li>
								<a class="link-rss" href="<?php echo  EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=teamblog&id=' . $row->id );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
									<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
								</a>
							</li>
						<?php } ?>

						<?php if( $system->config->get( 'teamblog_allow_join' ) ){ ?>
							<?php if( !$row->isMember && $system->my->id != 0 ) { ?>
								<li>
									<a class="link-jointeam" href="javascript:eblog.teamblog.join('<?php echo $row->id;?>');">
										<span><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOG_JOIN_TEAM' );?></span>
									</a>
								</li>
							<?php } ?>
						<?php } ?>

						<?php if( $system->admin ) : ?>
							<li>
							<?php if ($row->isFeatured) { ?>
								<a class="feature-del" href="javascript:eblog.featured.remove('teamblog','<?php echo $row->id;?>');" title="<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?>">
									<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?></span>
								</a>
							<?php } else { ?>
								<a class="feature-add" href="javascript:eblog.featured.add('teamblog','<?php echo $row->id;?>');" title="<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?>">
									<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?></span>
								</a>
							<?php } ?>
							</li>
						<?php endif; ?>
						</ul>

						<?php if ($row->isActualMember && $system->my->id != 0) { ?>
						<div class="mtl eblog-message info">
							<span><?php echo JText::_( 'COM_EASYBLOG_CURRENTLY_MEMBER_OF_THE_TEAM' );?></span>
							<a class="link-jointeam" href="javascript:eblog.teamblog.leave('<?php echo $row->id;?>');"><span><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM' );?></span></a>?
						</div>
						<?php } ?>
					</div> <!-- end profile-connect -->
				</div> <!-- end profile-info -->
				<div class="clear"></div>
			</div> <!-- end profile-head -->

			<?php if( $this->getParam( 'show_teamblogstats') || $this->getParam( 'show_teamblogstatsitem')) { ?>


			<div class="profile-body clearfix">

				<?php if( $this->getParam( 'show_teamblogstats') ) { ?>

				<div class="profile-sidebar">
					<div class="profile-brief">
						<div class="in">
							<ul class="profile-stats reset-ul clearfix">
								<li class="total-post">
									<span class="traits float-r"><?php echo $row->totalEntries; ?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_POSTS' );?></span>
								</li>
								<li class="total-categories">
									<span class="traits float-r"><?php echo count( $row->categories );?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_CATEGORIES' );?></span>

									<ul class="list-square reset-ul mts">
									<?php if(count($row->categories) > 0) : ?>
										<?php for($i = 0; $i < count($row->categories); $i++) : ?>
										<li>
										<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=statistic&id='.$row->id. '&stat=category&catid='.$row->categories[$i]->id); ?>">
											<?php echo $row->categories[$i]->title; ?>
											<?php // echo $row->categories[$i]->post_count;?>
										</a>
										</li>
										<?php endfor; ?>
									<?php endif; ?>
									</ul>
								</li>
								<li class="total-tag">
									<span class="traits float-r"><?php echo count( $row->tags );?></span>
									<span><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_TAGS' );?></span>

									<div class="clear"></div>
									<div class="tag-list mts">
									<?php
									if( $row->tags )
									{
										$i	= 1;
										foreach( $row->tags as $tag )
										{
											$delimeter	= $i == count( $row->tags ) ? '' : ', ';
									?>
										<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=teamblog&layout=statistic&id=' . $row->id . '&stat=tag&&tagid=' . $tag->id );?>"><?php echo $tag->title; ?></a><?php echo $delimeter; ?>
									<?php
											$i++;
										}
									}
									?>
									</div>
								</li>
								<li class="profile-module my-writers">
									<span class="traits float-r"><?php echo count( $row->members );?></span>
									<span><?php echo JText::_('COM_EASYBLOG_TEMBLOG_CONTRIBUTORS' ); ?></span>
									<ul class="active-bloggers clearfix reset-ul list-full<?php echo ( $system->config->get('layout_avatar') ) ? '' : ' no-avatar'; ?>">
										<?php
											if(! empty($row->members))
											{
												foreach($row->members as $member)
												{
										 ?>
										<li>
											<div class="pts pbs">
												<a href="<?php echo $member->getProfileLink(); ?>" title="<?php echo $member->displayName; ?>">
												<?php if ( $system->config->get('layout_avatar') ) { ?>
													<img src="<?php echo $member->getAvatar(); ?>" alt="<?php echo $member->displayName; ?>" width="40" height="40" class="avatar float-l mrm"/>
												<?php echo EasyBlogTooltipHelper::getBloggerHTML( $member->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'prev')) ); ?>
												<?php } ?>
													<?php echo $member->displayName;?>
												</a>
											</div>
										</li>
										 <?php
												}
											}
										?>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div><!--end: .profile-sidebar-->

				<?php } // end stat ?>

				<?php if( $this->getParam( 'show_teamblogstatsitem') ) { ?>

				<div class="profile-main">
					<?php if( $row->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$row->isMember && !EasyBlogHelper::isSiteAdmin() ){?>
					<div class="eblog-message warning mtm">
						<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY'); ?>
						<?php echo ($system->my->id != 0) ? JText::sprintf('COM_EASYBLOG_TEAMBLOG_CLICK_TO_JOIN', 'eblog.teamblog.join('.$row->id.')') : '' ; ?>
					</div>
					<?php } else { ?>
					<?php if(empty($row->blogs)) { ?>
					<div class="profile-main">
						<div class="intro">
							<h3 class="fsx reset-h"><?php echo JText::_('COM_EASYBLOG_NO_POST_IN_TEAM'); ?></h3>
						</div>
					</div><!--end: .profile-main-->
					<?php }else{ ?>
					<h4 class="rip mbm"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOG_RECENT_POSTS' );?></h4>
					<ul class="post-list reset-ul">
						<?php foreach( $row->blogs as $entry ) { ?>
							<?php echo $this->fetch( 'blog.item.simple'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $entry->source ) . '.php' , array( 'entry' => $entry , 'customClass' => 'team' )); ?>
						<?php } ?>
						<li class="post-listmore fwb">
							<div>
							<span>
								<?php echo JText::_('COM_EASYBLOG_OTHER_ENTRIES_FROM'); ?>
								<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$row->id); ?>"><?php echo $row->title;?></a>
							</span>
							</div>
						</li>
					</ul>
					<?php } //end if else ?>
				<?php } //end if else ?>
				</div>

				<?php } // end stat item ?>

			</div><!--end: .profile-body-->

			<?php } //end outer if ?>

			<div class="clear"></div>
		</div><!--end: .profile-item -->
		<?php } //end foreach ?>

		<?php if(count($teams) <= 0) { ?>
		<div><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
		<?php } ?>

		<div class="eblog-pagination">
			<?php echo $pagination; ?>
		</div>
	</div>
</div>
