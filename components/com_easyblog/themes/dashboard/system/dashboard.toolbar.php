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

<?php if($system->config->get('layout_enabledashboardtoolbar')) : ?>

<script type="text/javascript">
EasyBlog.require()
.script('layout/responsive')
.done(function($){

	$('#eb-topbar').responsive({at: 540, switchTo: 'narrow'});
	$('.eb-nav-collapse').responsive({at: 560, switchTo: 'nav-hide'});
	$('.btn-eb-navbar').click(function() {
		$('.eb-nav-collapse').toggleClass("nav-show");
		return false;
	});

});
</script>

<div id="eb-topbar" class="clearfix">
	<a href="javascript:void(0);" class="btn-eb-navbar"></a>
	<div class="eb-nav-collapse">
		<ul class="ui-toolbar reset-ul float-li clearfix">
			<?php if($system->config->get('layout_dashboardhome')) { ?>
			<li class="go-blog">
	        	<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' );?>">
	        		<i><?php echo JText::_( 'COM_EASYBLOG_BACK_TO_BLOGS' ); ?></i>
	        	</a>
	        	<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_BACK_TO_BLOGS'); ?></b>
						<?php echo JText::_('COM_EASYBLOG_BACK_TO_BLOGS_TIPS'); ?>
					</div>
				</div>
	        </li>
	        <?php } ?>

		    <?php if($system->config->get('layout_dashboardmain')) { ?>
		    <li class="posts-overview<?php echo $current == 'display' ? ' active' : ''; ?>">
		    	<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard'); ?>">
		    		<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW'); ?></i>
		    	</a>
				<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW'); ?></b>
						<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW_TIPS' ); ?>
					</div>
				</div>
		    </li>
			<?php } ?>

			<?php if($system->config->get('layout_dashboardblogs')) { ?>
				<?php if(($this->acl->rules->publish_entry) || ($this->acl->rules->add_entry) || ($this->acl->rules->delete_entry)) { ?>
				<li class="posts-entries<?php echo $current == 'entries' ? ' active' : ''; ?>">
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries'); ?>">
						<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_POSTS'); ?></i>
					</a>
					<div>
			    		<i></i>
			    		<div>
			    			<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_POSTS'); ?></b>
			    			<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_POSTS_TIPS' ); ?>
			    		</div>
			    	</div>
				</li>
				<?php } ?>
			<?php } ?>

			<?php if($system->config->get('layout_dashboarddrafts')) { ?>
				<?php if(($this->acl->rules->publish_entry) || ($this->acl->rules->add_entry) || ($this->acl->rules->delete_entry)) { ?>
				<li class="drafts<?php echo $current == 'drafts' ? ' active' : ''; ?><?php echo ( $totalDrafts > 0 ) ? ' hasdraft' : '';?>">
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=drafts'); ?>">
						<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_DRAFTS'); ?></i>
						<?php if( $totalDrafts > 0 ){ ?>
						<b><?php echo $totalDrafts; ?></b>
						<?php } ?>
					</a>
					<div>
						<i></i>
						<div>
							<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_DRAFTS'); ?></b>
							<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_DRAFTS_TIPS' );?>
						</div>
					</div>
				</li>
				<?php } ?>
			<?php } ?>

			<?php if( ( $isBlogger || $this->acl->rules->manage_comment ) && $system->config->get('layout_dashboardcomments') && EasyBlogHelper::getHelper( 'Comment')->isBuiltin() ) : ?>
			<li class="user-comments<?php echo $current == 'comments' ? ' active' : ''; ?>">
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=comments'); ?>">
					<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_COMMENTS'); ?></i>
				</a>
				<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_COMMENTS'); ?></b>
						<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_COMMENTS_TIPS' );?>
					</div>
				</div>
			</li>
			<?php endif; ?>

			<?php if($system->config->get('layout_dashboardcategories') && $this->acl->rules->create_category) : ?>
			<li class="categories<?php echo $current == 'categories' ? ' active' : ''; ?>">
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=categories'); ?>">
					<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_CATEGORIES'); ?></i>
				</a>
				<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_CATEGORIES'); ?></b>
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_CATEGORIES_TIPS'); ?>
					</div>
				</div>
			</li>
			<?php endif; ?>

			<?php if($system->config->get('layout_dashboardtags') && $this->acl->rules->create_tag) : ?>
			<li class="tags<?php echo $current == 'tags' ? ' active' : ''; ?>">
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=tags'); ?>">
					<i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TAGS'); ?></i>
				</a>
				<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TAGS'); ?></b>
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TAGS_TIPS'); ?>
					</div>
				</div>
			</li>
			<?php endif; ?>

	        <?php if($isTeamAdmin && $totalTeamRequest > 0 ) : ?>
	        <li class="teamblog">
	            <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs'); ?>">
	                <i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAMREQUEST'); ?></i>
	                <b><?php echo ($totalTeamRequest > 0) ? $totalTeamRequest  : 0; ?></b>
	            </a>
	            <div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAMREQUEST'); ?></b>
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAMREQUEST_TIPS'); ?>
					</div>
				</div>
	        </li>
	        <?php endif; ?>

	        <?php if( !empty($this->acl->rules->manage_pending) && !empty($this->acl->rules->publish_entry) && $totalPending > 0 ){ ?>
	        <li class="review<?php echo $current == 'pending' ? ' active' : ''; ?>">
	            <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=pending&id=' . $user->id ); ?>" title="<?php echo $this->getNouns( 'COM_EASYBLOG_PENDING_REVIEW' , $totalPending , true); ?>">
	                <i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_REVIEW'); ?></i>
	                <b><?php echo ($totalPending > 0) ? $totalPending : 0 ?></b>
	            </a>
	            <div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_REVIEW'); ?></b>
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_REVIEW_TIPS'); ?>
					</div>
				</div>
	        </li>
	        <?php } ?>

	        <?php
	        /*
	        * All links below will be floated to the right
	        */
	        ?>
	        <script type="text/javascript">
	        EasyBlog.ready(function($) {

	        	$(".dashboardLogoutButton").click(function(){

	        		$('#eblog-logout').submit();
	        	});
	        });
	        </script>

	        <?php if($system->config->get('layout_dashboardsettings') ) : ?>
	        <li class="settings<?php echo $current == 'profile' ? ' active' : ''; ?> cog float-r">
				<?php if( $system->config->get( 'toolbar_editprofile' ) ){ ?>
				<a href="<?php echo EasyBlogHelper::getEditProfileLink(); ?>">
	                <i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_EDIT_PROFILE'); ?></i>
	            </a>
				<?php } else { ?>
				<a href="javascript:void(0);">
	                <i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_EDIT_PROFILE'); ?></i>
	            </a>
				<?php } ?>
	            <div>
					<i></i>
					<div>
						<img src="<?php echo $user->getAvatar();?>" class="dashboard-avatar float-r mlm">
						<b style="font-size:12px"><?php echo $user->getName();?></b>
						<p class="clearfix">
							<?php if( $system->config->get( 'toolbar_editprofile' ) ){ ?>
							<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile'); ?>">
	                			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_EDIT_YOUR_SETTINGS'); ?>
	            			</a>
							<?php } ?>
						</p>
						<?php if( $system->config->get( 'layout_dashboardlogout') ){ ?>
						<form id="eblog-logout" action="<?php echo JRoute::_($logoutActionLink); ?>" method="post">
							<a class="buttons logout dashboardLogoutButton" href="javascript: void(0);">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_SIGN_OUT'); ?>
							</a>
							<input type="hidden" value="<?php echo $logoutURL; ?>" name="return">
							<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						<?php } ?>
					</div>
				</div>
			</li>
	        <?php endif; ?>

	        <?php if( $system->config->get( 'main_microblog' ) && $this->acl->rules->add_entry ) { ?>
	        <li class="micro-post float-r<?php echo $current == 'microblog' ? ' active' : '';?>">
				<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=microblog' );?>">
					<i><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_MICROBLOG' ); ?></i>
				</a>
				<div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_TOOLBAR_MICROPOST'); ?></b>
						<?php echo JText::_( 'COM_EASYBLOG_TOOLBAR_MICROPOST_TIPS' );?>
					</div>
				</div>
			</li>
			<?php } ?>

	        <?php if( $system->config->get('layout_dashboardnewpost') && $this->acl->rules->add_entry ){ ?>
	        <li class="new-post float-r<?php echo $current == 'write' ? ' active' : '';?>">
	            <a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write'); ?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>">
	                <i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_NEW_POST'); ?></i>
	            </a>
	            <div>
					<i></i>
					<div>
						<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_NEW_POST'); ?></b>
						<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_TOOLBAR_NEW_POST_TIPS' );?>
					</div>
				</div>
	        </li>
	        <?php } ?>
		</ul>
	</div>
</div>
<?php endif; ?>
