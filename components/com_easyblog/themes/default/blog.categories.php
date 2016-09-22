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
$mainframe	= JFactory::getApplication();
?>
<div id="ezblog-body">
	<div id="ezblog-section"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_PAGE_HEADING'); ?></div>
	<div id="ezblog-category">
	<?php foreach($data as $category) { ?>
		<div class="profile-item clearfix">
			<div class="profile-head">
				<?php if($system->config->get('layout_categoryavatar', true)) : ?>
				<div class="profile-avatar float-l">
					<i class="pabs"></i>
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>" class="avatar">
						<img src="<?php echo $category->avatar;?>" align="top" class="avatar" />
					</a>
				</div><!--end: .profile-avatar-->
				<?php endif; ?>

				<div class="profile-info">
					<h3 class="profile-title rip"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>"><?php echo JText::_( $category->title ); ?></a></h3>
					<?php if ( $category->description ) { ?>
					<div class="profile-info mts">
						<?php echo $category->description; ?>
					</div>
					<?php } ?>
					<div class="profile-connect mts pbs">
						<ul class="connect-links reset-ul float-li">
							<?php if($system->config->get('main_categorysubscription')) { ?>
							<?php if( ($category->private && $system->my->id != 0 ) || ($system->my->id == 0 && $system->config->get( 'main_allowguestsubscribe' )) || $system->my->id != 0) : ?>
								<li>
									<a href="javascript:eblog.subscription.show( '<?php echo EBLOG_SUBSCRIPTION_CATEGORY; ?>' , '<?php echo $category->id;?>');" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>" class="link-subscribe">
										<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?></span>
									</a>
								</li>
							<?php endif; ?>
							<?php } ?>
							<?php if( $system->config->get('main_rss') ){ ?>
								<li>
									<a href="<?php echo $category->rssLink;?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="link-rss">
										<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
					<?php if(! empty($category->nestedLink)) { ?>
					<div class="profile-child ptm small">
						<span><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_SUBCATEGORIES' ); ?></span>
						<?php echo $category->nestedLink; ?>
					</div>
					<?php } ?>
				</div><!--end: .profile-info-->
				<div class="clear"></div>
			</div><!--end: .profile-head-->


			<?php if( $this->getParam( 'show_categorystats') || $this->getParam( 'show_categorystatsitem')) { ?>

			<div class="profile-body clearfix">
				<?php if( $this->getParam( 'show_categorystats') ) { ?>
				<div class="profile-sidebar">
					<div class="profile-brief">
						<div class="in">
							<ul class="profile-stats reset-ul clearfix">
								<li class="total-post">
									<span class="traits float-r"><?php echo $category->cnt; ?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TOTAL_POSTS' );?></span>
								</li>

								<?php if( $system->isBloggerMode === false && $category->blogs ) : ?>

								<li class="total-comment">
									<span class="traits float-r"><?php echo ( $category->bloggers  ) ? count( $category->bloggers ) : '0' ;?></span>
									<span class="key"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_ACTIVE_BLOGGERS' );?></span>
									<?php
									$initialLimit = ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
									if( $this->getParam( 'show_category_bloggers_avatar') ){
									?>
									<ul class="active-bloggers clearfix reset-ul list-full<?php echo ( $system->config->get('layout_avatar') ) ? '' : ' no-avatar'; ?>">

										<?php

											$initialLimit = ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');

											if( !empty( $category->bloggers ) )
											{
												$i = 1;
												// Repeat this to simulate blogger data
												// $category->bloggers[] = $category->bloggers[0];
												foreach($category->bloggers as $member )
												{
										 ?>
										<li <?php if ($i > $initialLimit) { ?>class="more-activebloggers" style="display: none;"<?php } ?>>
											<div class="pts pbs">
												<a href="<?php echo $member->getProfileLink(); ?>" title="<?php echo $member->getName(); ?>" class="avatar">
												<?php if ( $system->config->get('layout_avatar') ) { ?>
													<img <?php if ($i <= $initialLimit) { ?> src="<?php echo $member->getAvatar(); ?>" <?php } else { ?> data-src="<?php echo $member->getAvatar(); ?>" <?php } ?> alt="<?php echo $member->getName(); ?>" width="40" height="40" class="avatar float-l mrm" style="overflow: hidden;"/>
													<?php echo EasyBlogTooltipHelper::getBloggerHTML( $member->id, array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'parent')) ); ?>
												<?php } ?>
													<?php echo $member->getName(); ?>
												</a>
											</div>
										</li>
										 <?php
												$i++;
												}
											}
										?>
									</ul>
									<?php } ?>
										<?php
											if( !empty( $category->bloggers ) )
											{
												if (count($category->bloggers) > $initialLimit) {
										?>
	                                            <script type="text/javascript">
	                                                EasyBlog.ready(function($){
	                                                    $(".showAllBloggers").click(function(){

	                                                        $('.more-activebloggers')
	                                                            .each(function() {
	                                                                $(this).find('.avatar')
	                                                                    .attr('src', $(this).find('.avatar').attr('data-src'));
	                                                            })
	                                                            .show();

	                                                        $(this).remove();
	                                                    });
	                                                });
	                                            </script>
	                                            <a class="pts showAllBloggers" style="display: inline-block;" href="javascript: void(0);"><?php echo JText::sprintf('COM_EASYBLOG_SHOW_ALL_BLOGGERS', count($category->bloggers)); ?> &raquo;</a>
										<?php
												}
											}
										?>
								</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
				<?php } // if for statistic?>


				<?php if( $this->getParam( 'show_categorystatsitem') ) { ?>

				<div class="profile-main">
					<?php if(empty($category->blogs)) { ?>
						<div><?php echo JText::_('COM_EASYBLOG_CATEGORIES_NO_POST_YET'); ?></div>
					<?php } else { ?>
					<h4 class="rip mbm"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_RECENT_POSTS' );?></h4>
					<ul class="post-list reset-ul">
						<?php foreach( $category->blogs as $entry ) { ?>
							<?php echo $this->fetch( 'blog.item.simple'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $entry->source ) . '.php' , array( 'entry' => $entry , 'customClass' => 'category' )); ?>
						<?php } ?>
						<li class="post-listmore fwb">
							<div>
								<?php echo JText::_('COM_EASYBLOG_OTHER_ENTRIES_FROM'); ?>
								<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$category->id); ?>"><?php echo $category->title;?></a>
							</div>
						</li>
					</ul>
					<?php } ?>
				</div>

				<?php } // if for statistic items ?>

			</div><!--end: .profile-body -->

			<?php } //end if for outer if ?>
		</div><!--end: .blogger-item-->
	<?php } //end foreach ?>

	<?php if(count($data) <= 0) { ?>
		<div><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
	<?php } ?>

	<?php if ( $pagination ) : ?>
		<div class="pagination clearfix">
			<?php echo $pagination; ?>
		</div>
	<?php endif; ?>
</div>
</div><!--end: #ezblog-body-->
