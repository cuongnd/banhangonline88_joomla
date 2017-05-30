<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$acl			= EasyBlogACLHelper::getRuleSet();
$isBloggerMode	= EasyBlogRouter::isBloggerMode();
$itemid			= JRequest::getVar('Itemid', '');
$menu			= JFactory::getApplication()->getMenu();
$item			= $menu->getItem($itemid);
$params  		= EasyBlogHelper::getRegistry();

if( $item )
{
	$params->load( $item->params );
}
?>
<?php if( $system->config->get( 'layout_responsive' ) ){ ?>
<script type="text/javascript">
EasyBlog.require()
.script('layout/responsive')
.done(function($){

	$('#ezblog-head #ezblog-search').bind('focus', function(){
		$(this).animate({ width: '170'} );
	});

	$('#ezblog-head #ezblog-search').bind( 'blur' , function(){
		$(this).animate({ width: '120'});
	});

	$('#ezblog-menu').responsive({at: 540, switchTo: 'narrow'});
	$('.eb-nav-collapse').responsive({at: 560, switchTo: 'nav-hide'});
	$('.btn-eb-navbar').click(function() {
		$('.eb-nav-collapse').toggleClass("nav-show");
		return false;
	});

});
</script>
<?php } ?>
<?php echo EasyBlogHelper::renderModule( 'easyblog-before-header' ); ?>
<?php if ( $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) || $system->config->get( 'layout_headers' ) || $system->config->get( 'layout_toolbar' ) ) { ?>
<div id="ezblog-head">
	<div class="in clearfix">
<?php } ?>

		<?php if( $system->config->get( 'main_sitesubscription' )  ||  $system->config->get( 'main_rss' ) ){ ?>
		<div class="component-links float-r">
			<?php if( $system->config->get( 'main_sitesubscription' ) ){ ?>
			<a href="javascript:void(0);" onclick="eblog.subscription.show('<?php echo EBLOG_SUBSCRIPTION_SITE; ?>');" class="link-email">
				<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_SITE'); ?></span>
			</a>
			<?php } ?>

			<?php if( $system->config->get( 'main_rss' ) ){ ?>
			<a href="<?php echo EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog' );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="link-rss">
				<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></span>
			</a>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php 
			$show_page_heading  = $params->get( 'show_page_heading' , '' );
			if( $system->config->get('layout_headers') ){ 
			if ($show_page_heading) { $title = $params->get( 'page_heading' , '' ); }
		?>

		<h2 class="component-title reset-h"><?php echo JText::_( $title ); ?></h2>
		<p class="rip mts mbm"><?php echo JText::_( $desc ); ?></p>
		<?php } ?>

		<?php echo EasyBlogHelper::renderModule( 'easyblog-before-toolbar' ); ?>


		<?php if( $system->config->get('layout_toolbar') && $this->acl->rules->access_toolbar ){ ?>
		<div id="ezblog-menu" class="clearfix">
			<a href="javascript:void(0);" class="btn-eb-navbar"></a>
			<div class="eb-nav-collapse">
				<ul class="blog-navi <?php echo $system->config->get( 'layout_iconless' ) ? ' iconless' : '';?> reset-ul float-li clearfix">
					<?php if($system->config->get('layout_latest', 1)) : ?>
					<li class="toolbar-item toolbar-latest <?php echo $views->home;?>">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_LATEST_POSTS'); ?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_LATEST_POSTS'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_LATEST_POSTS_TIPS' );?>
						</div>
					</li>
					<?php endif; ?>

					<?php if($system->config->get('layout_categories', 1)) : ?>
					<li class="toolbar-item toolbar-categories <?php echo $views->categories;?>">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=categories'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES'); ?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_CATEGORIES_TIPS' );?>
						</div>
					</li>
					<?php endif; ?>

					<?php if($system->config->get('layout_tags', 1)) : ?>
					<li class="toolbar-item toolbar-tags <?php echo $views->tags;?>">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=tags'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS'); ?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_TAGS_TIPS' );?>
						</div>
					</li>
					<?php endif; ?>

					<?php if($isBloggerMode === false) : ?>
						<?php if($system->config->get('layout_bloggers', 1)) : ?>
						<li class="toolbar-item toolbar-blogger <?php echo $views->blogger;?>">
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS'); ?></span></a>
							<div class="tips">
								<i></i>
								<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS'); ?></b>
								<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_BLOGGERS_TIPS' );?>
							</div>
						</li>
						<?php endif; ?>

						<?php if($system->config->get('layout_teamblog', 1)) : ?>
						<li class="toolbar-item toolbar-teamblog <?php echo $views->teamblog;?>">
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS'); ?></span></a>
							<div class="tips">
								<i></i>
								<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS'); ?></b>
								<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_TEAMBLOGS_TIPS' );?>
							</div>
						</li>
						<?php endif; ?>
					<?php endif; ?>

					<?php if($system->config->get('layout_archive', 1)) : ?>
					<li class="toolbar-item toolbar-archive <?php echo $views->archive;?>">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=archive'); ?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVE'); ?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVE'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_ARCHIVE_TIPS' );?>
						</div>
					</li>
					<?php endif; ?>

					<?php if($system->config->get('layout_search' , 1 ) ) : ?>
					<li class="toolbar-item toolbar-search <?php echo $views->search;?>">
						<form method="get" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=search&layout=parsequery' );?>">
							<input id="ezblog-search" type="text" name="query" class="input text" alt="query" autocomplete="off" />
							<button class="submit-search" type="submit"><?php echo JText::_('COM_EASYBLOG_SEARCH') ?></button>


							<?php if( EasyBlogHelper::getJConfig()->get( 'sef' ) != 1 ){ ?>
							<input type="hidden" name="option" value="com_easyblog" />
							<input type="hidden" name="view" value="search" />
							<input type="hidden" name="layout" value="parsequery" />
							<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
							<?php } ?>

						</form>
					</li>
					<?php endif; ?>


					<?php if( $system->my->id < 1 ){ ?>
					<?php if( $system->config->get( 'layout_login' ) ){ ?>
					<li class="toolbar-item user-access float-r">
						<a href="javascript:void(0);" onclick="eblog.login.toggle()"><span><?php echo JText::_( 'COM_EASYBLOG_LOGIN' );?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_LOGIN'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_LOGIN_TIPS' );?>
						</div>
						<div class="user-form pabs" style="display: none;" id="easyblog-login-form">
							<div class="clearfix">
								<form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
									<label for="username" class="float-l width-full rip">
										<span class="trait"><?php echo JText::_('COM_EASYBLOG_USERNAME') ?></span>
										<?php 
											$usersConfig	= JComponentHelper::getParams( 'com_users' );
											if($usersConfig->get('allowUserRegistration')){ ?>
												<a href="<?php echo EasyBlogHelper::getRegistrationLink();?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_REGISTER' );?></a>
											<?php } ?>
										<input id="username" type="text" name="username" class="input text" alt="username" tabindex="31"/>
									</label>
									<label for="passwd" class="float-l width-full rip">
										<span class="trait"><?php echo JText::_('COM_EASYBLOG_PASSWORD') ?></span>
										<a href="<?php echo EasyBlogHelper::getResetPasswordLink();?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_FORGOTTEN_PASSWORD' );?></a>
										<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
											<input type="password" id="passwd" class="input text" name="password" tabindex="32"/>
										<?php } else { ?>
											<input type="password" id="passwd" class="input text" name="passwd" tabindex="32"/>
										<?php } ?>
									</label>
									<div class="mts float-l width-full">
										<?php if(JPluginHelper::isEnabled('system', 'remember')) { ?>
										<label for="remember" class="remember float-l">
											<input id="remember" type="checkbox" name="remember" value="yes" alt="Remember Me" class="rip" tabindex="33"/>
											<span><?php echo JText::_('COM_EASYBLOG_REMEMBER_ME') ?></span>
										</label>
										<?php } ?>
										<button class="button submit float-r" type="submit" tabindex="34"><?php echo JText::_('COM_EASYBLOG_LOGIN') ?></button>
									</div>

									<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
									<input type="hidden" value="com_users"  name="option">
									<input type="hidden" value="user.login" name="task">
									<input type="hidden" name="return" value="<?php echo $return; ?>" />
									<?php } else { ?>
									<input type="hidden" value="com_user"  name="option">
									<input type="hidden" value="login" name="task">
									<input type="hidden" name="return" value="<?php echo $return; ?>" />
									<?php } ?>
									<?php echo JHTML::_( 'form.token' ); ?>
								</form>
							</div>
						</div>
					</li>
					<?php } ?>

					<?php } else { ?>

					<?php if( $system->config->get( 'layout_option_toolbar' ) ) { ?>
					<li class="toolbar-item user-setting float-r">
						<a onclick="eblog.toolbar.dashboard();" class="user-dashboard"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_SETTINGS');?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_SETTINGS'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_SETTINGS_TIPS' );?>
						</div>
						<div class="user-options pabs toggle-show" style="display:none;">
							<i class="subtoolbar-arrow pabs"></i>
							<ul class="reset-ul">
								<li class="user-info">
									<?php if( $system->config->get( 'toolbar_editprofile' ) ){ ?>
									<a href="<?php echo EasyBlogHelper::getEditProfileLink();?>" class="avatar user-avatar float-r">
										<img class="avatar" src="<?php echo $system->profile->getAvatar();?>" alt="" width="35" height="35" />
									</a>
									<?php } else { ?>
									<a href="javascript:void(0);" class="avatar user-avatar float-r">
										<img class="avatar" src="<?php echo $system->profile->getAvatar();?>" alt="" width="35" height="35" />
									</a>
									<?php } ?>
									<div class="dashboard-user">
										<a href="<?php echo $system->profile->getProfileLink(); ?>" class="user-name"><?php echo $system->profile->getName();?></a>
										<?php if( $system->config->get( 'toolbar_editprofile' ) ){ ?>
										<br />
										<a href="<?php echo EasyBlogHelper::getEditProfileLink();?>" class="fss"><?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_DASHBOARD_EDIT_PROFILE' );?></a>
										<?php } ?>
									</div>
								</li>
								<?php if(($acl->rules->publish_entry) || ($acl->rules->add_entry) || ($acl->rules->delete_entry)) { ?>
								<li class="user-blogs"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries'); ?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_ENTRIES');?></a></li>
								<?php } ?>
								<?php if(($acl->rules->add_entry)) { ?>
								<li class="user-blogs"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=review'); ?>"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_REVIEW');?></a></li>
								<?php } ?>
								<?php if($acl->rules->manage_comment && EasyBlogHelper::getHelper( 'Comment')->isBuiltin() ) : ?>
								<li class="user-comments">
									<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments'); ?>">
										<?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_COMMENTS'); if($totalModComment > 0) { echo '<sup>' . $totalModComment. '</sup>'; } ?>
									</a>
								</li>
								<?php endif; ?>
								<?php if($acl->rules->create_category) : ?>
								<li class="categories"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=categories'); ?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_CATEGORIES');?></a></li>
								<?php endif; ?>
								<?php if($acl->rules->create_tag) : ?>
								<li class="tags has_separator"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=tags'); ?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_TAGS');?></a></li>
								<?php endif; ?>

								<li class="tags has_separator"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=subscription'); ?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_SUBSCRIPTION');?></a></li>

								<?php if($isTeamAdmin) : ?>
								<li class="teamblogs has_separator">
									<a class="blog" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs'); ?>">
										<?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_TEAMBLOG_REQUESTS'); ?>
										<?php echo ($totalTeamRequest > 0) ? '<sup>' . $totalTeamRequest . '</sup>' : '' ?>
									</a>
								</li>
								<?php endif; ?>

								<?php if( $system->config->get( 'toolbar_logout') ){ ?>
								<li class="sign-out">
									<form id="eblog-logout" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog' );?>">
										<a class="logout" href="javascript:eblog.dashboard.logout();"><?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_DASHBOARD_LOGOUT' );?></a>
										<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
										<input type="hidden" value="com_users"  name="option">
										<input type="hidden" value="user.logout" name="task">
										<input type="hidden" value="<?php echo $logoutURL; ?>" name="return">
										<?php } else { ?>
										<input type="hidden" value="com_user"  name="option">
										<input type="hidden" value="logout" name="task">
										<input type="hidden" value="<?php echo $logoutURL; ?>" name="return">
										<?php } ?>
										<?php echo JHTML::_( 'form.token' ); ?>
									</form>
								</li>
								<?php } ?>
							</ul>
						</div>
					</li>
					<?php } ?>

					<?php if( $system->config->get( 'main_microblog' ) && $acl->rules->add_entry ){ ?>
					<li class="toolbar-item user-micro float-r">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=microblog'); ?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>"><span><?php echo JText::_('COM_EASYBLOG_TOOLBAR_MICROPOST');?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_MICROPOST'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_MICROPOST_TIPS' );?>
						</div>
					</li>
					<?php } ?>

					<?php if(($acl->rules->publish_entry) || ($acl->rules->add_entry) || ($acl->rules->delete_entry)) { ?>
					<li class="toolbar-item user-write float-r">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write'); ?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>"><span><?php echo JText::_('COM_EASYBLOG_WRITE');?></span></a>
						<div class="tips">
							<i></i>
							<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_NEW_POST'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_NEW_POST_TIPS' );?>
						</div>
					</li>
					<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>

		<?php echo EasyBlogHelper::renderModule( 'easyblog-after-toolbar' ); ?>

<?php if ( $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) || $system->config->get( 'layout_headers' ) || $system->config->get( 'layout_toolbar' ) ) { ?>
	</div>
</div>
<?php } ?>
