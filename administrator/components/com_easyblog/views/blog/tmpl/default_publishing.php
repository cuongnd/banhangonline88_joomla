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
$blogContributeVal = '0';
if(!$this->isSiteWide){$blogContributeVal = '1';}
?>
<ul class="list-form reset-ul">
	<li>
		<label for="authorId"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTHOR' ); ?></label>
		<div>
			<div class="has-tip tip-above">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTHOR_DESC' );?></div>
				<input type="hidden" name="authorId" id="authorId" value="<?php echo empty($this->author)? $user->id : $this->author->id;?>" />
				<input type="text" readonly="readonly" name="authorName" id="authorName" value="<?php echo empty($this->author)? $user->getName() : $this->author->getName();?>" class="input" />
				<a class="modal-button modal button btn btn-primary" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a>
			</div>
		</div>
	</li>
	<li>
		<label for="category_id"><?php echo JText::_('COM_EASYBLOG_BLOGS_SELECT_CATEGORY'); ?></label>
		<div>
			<div class="has-tip tip-above">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_SELECT_CATEGORY_DESC' );?></div>
				<?php if( $this->categoryselecttype == 'select'){ ?>
					<?php echo $this->nestedCategories; ?>
				<?php } else { ?>
				<input type="text" readonly="readonly" name="categoryTitle" id="categoryTitle" value="<?php echo $this->category->title;?>" class="inputbox" />
				<input type="hidden" value="<?php echo $this->category->id;?>" name="category_id" id="category_id" />
				<a class="modal-button modal button btn btn-primary" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=categories&tmpl=component&browse=1&p=1"><?php echo JText::_('COM_EASYBLOG_SELECT_CATEGORY');?></a>
				<?php } ?>
			</div>
		</div>
	</li>
	<li>
		<label for="published"><?php echo JText::_('COM_EASYBLOG_BLOGS_PUBLISHING_STATUS'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_PUBLISHING_STATUS_DESC' );?></div>
				<select name="published" id="published" class="inputbox" style="width: 150px;">
					<option value="1" <?php if($this->blog->published == "1") echo "selected='selected'"; ?>><?php echo JText::_('COM_EASYBLOG_PUBLISHED');?></option>
					<option value="0" <?php if($this->blog->published == "0") echo "selected='selected'"; ?>><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED');?></option>
				</select>
			</div>
		</div>
	</li>
	<?php if( $this->teams || EasyBlogHelper::getHelper( 'Groups' )->useGroups() || EasyBlogHelper::getHelper( 'Event' )->isEnabled() ) { ?>
		<li>
			<script>

			<?php if (EasyBlogHelper::getJoomlaVersion() >= '3.0') { ?>
			jQuery(document).ready(function()
			{
				jQuery('[data-blog-contribute]').chosen().change(function(elem, obj)
				{
					jQuery( '.blogContributeTeamRadio' ).attr( 'checked' , false );

					eblog.dashboard.changeCollab('jomsocial');
					jQuery(".hidden_blog_contribute").val(obj.selected);
				});
			});

			<?php } ?>

			EasyBlog.ready(function($) {


				$(".blogContributeTeamRadio").click(function(){

					eblog.dashboard.changeCollab('easyblog');
					$(this).parent().toggleClass('active');

					$('.group-blog-contribute').val(null);
					$('.event-blog-contribute').val(null);
    				$('.hidden_blog_contribute').val(this.value);
				});
			});
			</script>

			<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_CONTRIBUTION');?></label>
			<div>
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_CONTRIBUTION_DESC' );?></div>
					<?php if( !$this->external ){ ?>
					<div class="blog-contributions clearfix">
						<input type="radio" name="blog_contribute" id="team_site" value="0" <?php echo ($this->isSiteWide) ? 'checked="checked"' : ''; ?> class="input radio blogContributeTeamRadio" />
						<label for="team_site"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_CONTRIBUTION_SITE_WIDE'); ?></label>
					</div>
					<?php } ?>

					<?php
					if( $this->teams && !$this->external )
					{
					?>
					<strong class="contribution-seperator"><?php echo JText::_( 'COM_EASYBLOG_TEAMS' );?></strong>
					<?php
						foreach( $this->teams as $team )
						{
							$teamTable	= EasyBlogHelper::getTable( 'TeamBlog' );
							$teamTable->load( $team->team_id );

							$isTeamChecked  = false;

							if( $this->isDraft )
							{
								if( isset($this->draft->blog_contribute) )
								{
									$isTeamChecked  = ($team->team_id == $this->draft->blog_contribute) ? true : false;
								}
							}
							else
							{
								$isTeamChecked  = $teamTable->isPostOwner( $this->blog->id );
							}
					?>
					<div class="blog-contributions clearfix mts">

						<input type="radio" name="blog_contribute" id="team_<?php echo $teamTable->id;?>" value="<?php echo $teamTable->id;?>" <?php echo ($isTeamChecked) ? ' checked="checked"' : ''; ?> class="input radio blogContributeTeamRadio" />
						<label for="team_<?php echo $teamTable->id;?>"><img src="<?php echo $teamTable->getAvatar();?>" width="30" height="30" class="avatar float-l mrm" /><?php echo $teamTable->title;?></label>
					</div>
						<?php
							if ($isTeamChecked) {
							?>
							<script type="text/javascript">
							EasyBlog.ready(function($)
							{
								$('#team_<?php echo $teamTable->id;?>').click();
							});
							</script>
							<?php 
							}
						}
					}
					?>

					<?php
					$groups	= EasyBlogHelper::getHelper( 'Groups' )->getFormHTML( $this->external, $this->extGroupId, 'group', $this->blog->ispending );

					if( $groups )
					{
					?>
						<strong><?php echo JText::_( 'COM_EASYBLOG_INTEGRATION_GROUPS' );?></strong>
						<input type="hidden" name="groups_type" value="" />
						<input type="hidden" name="return" value="<?php echo JRequest::getVar( 'return' ); ?>" />
					<?php
						echo $groups;
					}
					?>

					<?php
					$events	= EasyBlogHelper::getHelper( 'Event' )->getFormHTML( $this->externalEventId, 'event' );

					if( $events )
					{
					?>
						<strong><?php echo JText::_( 'COM_EASYBLOG_INTEGRATION_EVENTS' );?></strong>
					<?php
						echo $events;
					}
					?>
				</div>
			</div>
		</li>
	<?php } else { ?>
	<li style="display:none;">
		<input type="hidden" name="blog_contribute" value="0" style="display: none;" />
	</li>
	<?php } ?>
	<li>
		<label for="allowcomment"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_ENABLE_COMMENT'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_ENABLE_COMMENT_DESC' );?></div>
				<?php echo $this->renderCheckbox( 'allowcomment' , $this->allowComment ); ?>
			</div>
		</div>
	</li>
	<li>
		<label for="subscription"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_ENABLE_BLOG_SUBSCRIPTION'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_ENABLE_BLOG_SUBSCRIPTION_DESC' );?></div>
				<?php echo $this->renderCheckbox( 'subscription' , $this->subscription ); ?>
			</div>
		</div>
	</li>
	<li>
		<label for="frontpage"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SHOW_ON_FRONTPAGE'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SHOW_ON_FRONTPAGE_DESC' );?></div>
				<?php echo $this->renderCheckbox( 'frontpage' , $this->frontpage ); ?>
			</div>
		</div>
	</li>
	<li>
		<label for="frontpage"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS_DESC' );?></div>

				<?php echo $this->renderCheckbox( 'send_notification_emails', $this->send_notification_emails ); ?>
			</div>
		</div>
	</li>
	<li>
		<label for="created"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_CREATION_DATE'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_CREATION_DATE_DESC' );?></div>
				<?php
				$now 		=  EasyBlogDateHelper::toFormat( EasyBlogDateHelper::getDate() );
				$displaynow =  EasyBlogDateHelper::toFormat( EasyBlogDateHelper::getDate(), $this->config->get( 'layout_dateformat' ) );

				if($this->blog->created != "")
				{
					$newDate    = EasyBlogDateHelper::getDate($this->blog->created);
					$now 		= EasyBlogDateHelper::toFormat($newDate);
					$displaynow =  EasyBlogDateHelper::toFormat( $newDate, $this->config->get( 'layout_dateformat' ) );
				}

				echo EasyBlogHelper::dateTimePicker('created', $displaynow, $now);
				?>
			</div>
		</div>
	</li>
	<li>
		<label for="publish_up"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_DATE'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_DATE_DESC' );?></div>
				<?php
				if($this->blog->publish_up != "")
				{
					$newDate    = EasyBlogDateHelper::getDate($this->blog->publish_up);
					$now 		= EasyBlogDateHelper::toFormat($newDate);
					$displaynow =  EasyBlogDateHelper::toFormat( $newDate, $this->config->get( 'layout_dateformat' ) );
				}
				else {
					$newDate    	= EasyBlogDateHelper::getDate();
					$now 			= EasyBlogDateHelper::toFormat( $newDate );
					$displaynow 	= EasyBlogDateHelper::toFormat( $newDate, $this->config->get( 'layout_dateformat' ) );
				}

				echo EasyBlogHelper::dateTimePicker('publish_up', $this->blog->publish_up != '' ? $displaynow : JText::_('COM_EASYBLOG_IMMEDIATELY'), $now);
				?>
			</div>
		</div>
	</li>
	<li>
		<label for="publish_down"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_UNPUBLISHING_DATE'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_UNPUBLISHING_DATE_DESC' );?></div>
				<?php
				$notEmpty = true;
				if ( $this->blog->publish_down == "0000-00-00 00:00:00" || empty($this->blog->publish_down))
				{
					$newDate    	= EasyBlogDateHelper::getDate($this->blog->publish_down);
					$now			= '';
					$displaynow 	= '';
					$nowReset       = EasyBlogDateHelper::toFormat( $newDate );
					$notEmpty 		= false;
				}
				else {
					$newDate    = EasyBlogDateHelper::getDate($this->blog->publish_down);
					$now 		= EasyBlogDateHelper::toFormat($newDate);
					$nowReset   = EasyBlogDateHelper::toFormat( $newDate );
					$displaynow = EasyBlogDateHelper::toFormat( $newDate, $this->config->get( 'layout_dateformat' ) );
					$notEmpty 	= true;
				}
				echo EasyBlogHelper::dateTimePicker('publish_down', $notEmpty ? $displaynow : JText::_('COM_EASYBLOG_NEVER'), $notEmpty ? $now : '', true);
				?>
				<input type="hidden" name="publish_down_reset" id="publish_down_reset" value="<?php echo $nowReset; ?>"/>
				<input type="hidden" name="publish_down_ori" id="publish_down_ori" value="<?php echo $this->blog->publish_down; ?>"/>
			</div>
		</div>
	</li>
	<li>
		<label for="hits"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_HITS' );?></label>
		<div>
			<span style="line-height:22px;" class="hits-counter"><?php echo $this->blog->hits;?></span>
			<?php if( $this->blog->id ){ ?>
			<a id="reset-hits" href="javascript:void(0)" class="ui-button"><?php echo JText::_( 'COM_EASYBLOG_RESET_HITS_BUTTON' );?></a>
			<?php } ?>
		</div>
	</li>
	<li>
		<label for="private"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_PERMISSIONS'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PERMISSIONS_DESC' );?></div>
				<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions( '', $this->blog->created_by ) , 'private' , 'size="1" class="inputbox" style="width: 150px;"' , 'value' , 'text' , $this->isPrivate ); ?>
			</div>
		</div>
	</li>
	<li>
		<label for="copyrights"><?php echo JText::_('COM_EASYBLOG_COPYRIGHTS'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_COPYRIGHTS_INSTRUCTIONS' ); ?></div>
				<input type="text" name="copyrights" id="copyrights" value="<?php echo $this->escape( $this->blog->copyrights );?>" class="input inputbox full-width" />
			</div>
		</div>
	</li>

	<?php if($this->config->get('main_password_protect') && !$this->blog->isFeatured() ){ ?>
	<li>
		<label for="blogpassword"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_PROTECTION'); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_PROTECTION_INSTRUCTIONS' ); ?></div>
				<input type="text" name="blogpassword" id="blogpassword" value="<?php echo $this->escape( $this->blog->blogpassword );?>" class="input inputbox full-width" />
			</div>
		</div>
	</li>
	<?php } ?>
</ul>
<input type="hidden" id="blog_contribute_source" name="blog_contribute_source" value="easyblog" />
<input type="hidden" name="blog_contribute" id="blog_contribute" value=<?php echo $blogContributeVal;?> class="hidden_blog_contribute"/>
