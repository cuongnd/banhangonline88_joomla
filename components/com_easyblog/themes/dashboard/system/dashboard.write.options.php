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

if ($isSiteWide) {
	$contributionId = 0;
} else {
	$contributionId = 1;
}

$teamId 			= false;
$contributionType	= "easyblog";
?>

<div class="ui-sectionsep">
	<div><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_OPTIONS_HEADING'); ?></div>
</div>

<ul class="list-form reset-ul">
	<?php if( !$external ){ ?>
	<li class="write-postauthor">
		<label><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_PUBLISH_UNDER' ); ?> :</label>
		<div>
			<img id="author-avatar" src="<?php echo (empty($author)) ? $user->getAvatar() : $author->getAvatar();?>" width="16" height="16" style="border:1px solid #555" class="avatar float-l mrm mts" />
			<span class="ui-span mts">
				<span>
					<?php if( $system->admin || !empty($this->acl->rules->moderate_entry)  || (isset($teamContribution) && $isCurrentTeamAdmin ) ){ ?>
					<a href="javascript:void(0);" id="author-name" onclick="eblog.dashboard.changeAuthor('<?php echo JText::_('COM_EASYBLOG_DASHBOARD_DIALOG_CHANGE_AUTHOR_TITLE' , true ); ?>','<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=listBloggers&tmpl=component&browse=1');?>');"><?php echo empty( $author ) ? $user->getName() : $author->getName(); ?></a>
					<?php } else { ?>
						<span id="author-name"><?php echo empty( $author ) ? $user->getName() : $author->getName(); ?></span>
					<?php } ?>
				</span>
			</span>
			<input type="hidden" name="created_by" id="created_by" value="<?php echo empty($author) ? $user->id : $author->id;?>" />
		</div>
	</li>
	<?php } else { ?>
	    <input type="hidden" name="created_by" id="created_by" value="<?php echo empty($author) ? $user->id : $author->id;?>" />
	<?php } ?>

	<?php if( $teams || EasyBlogHelper::getHelper( 'Groups' )->useGroups() || EasyBlogHelper::getHelper( 'Event' )->isEnabled() ) { ?>
	<li>
		<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_CONTRIBUTION');?> :</label>
		<div>
			<div class="blog-contributions clearfull mts">

				<script type="text/javascript">
					EasyBlog.ready(function($) {
						$(".blogContributeRadio").on('click', function() {

							eblog.dashboard.changeCollab('easyblog');
							$(this).parent().toggleClass( 'active' );
							$('.hidden_blog_contribute').val(this.value);
						});
					});
				</script>
				<input type="radio" name="blog_contribute" id="team_site" value="0" <?php echo ($isSiteWide) && !$external ? 'checked' : ''; ?> class="input radio blogContributeRadio" />
				<label for="team_site"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_CONTRIBUTION_SITE_WIDE'); ?></label>
			</div>

			<?php if ($teams && !$external) { ?>
				<strong style="display:block;padding:5px 0;margin-top:10px;border-top:1px solid #ddd"><?php echo JText::_( 'COM_EASYBLOG_TEAMS' );?></strong>

				<script type="text/javascript">
					EasyBlog.ready(function($) {

						$(".blogContributeTeamRadio").on('click', function(){
							eblog.dashboard.changeCollab('easyblog');
							$(this).parent().toggleClass('active');

							$('.group-blog-contribute').val(null);
							$('.event-blog-contribute').val(null);
		    				$('.hidden_blog_contribute').val(this.value);
						});
					});
				</script>

				<?php foreach ($teams as $team) { ?>
					<?php
						$isTeamChecked  = false;
						$selectedTeamId	= false;

						if ($isDraft) {

							if (isset($draft->blog_contribute) && $draft->blog_contribute == $team->id) {
								$isTeamChecked	= true;
								$teamId 		= $team->id;
							}

						} else {

							if ($team->isPostOwner($blog->id)) {
								$isTeamChecked	= true;
								$teamId 		= $team->id;
							}
						}
					?>
					<div class="blog-contributions clearfull mts">
						<input type="radio" name="blog_contribute" id="team_<?php echo $team->id;?>" value="<?php echo $team->id;?>" <?php echo ($isTeamChecked) ? ' checked="checked"' : ''; ?> class="input radio blogContributeTeamRadio" />
						<label for="team_<?php echo $team->id;?>"><img src="<?php echo $team->getAvatar();?>" width="16" height="16" class="avatar float-l mrm" /><?php echo $team->title;?></label>
					</div>
				<?php } ?>
			<?php } ?>

			<?php if( EasyBlogHelper::getHelper( 'Groups' )->useGroups() ){ ?>
				<?php echo $this->fetch( 'dashboard.write.options.group.php' , array( 'external' => $external , 'extGroupId' => $extGroupId , 'blogSource' => $blogSource, 'isPending' => $blog->ispending ) ); ?>
			<?php } ?>

			<?php if( EasyBlogHelper::getHelper( 'Event' )->isEnabled() ){ ?>
				<?php echo $this->fetch( 'dashboard.write.options.event.php' , array( 'external' => $external , 'uid' => $uid , 'source' => $source , 'blogSource' => $blogSource  ) ); ?>
			<?php } ?>
		</div>

		<?php if ($isEdit) { ?>
			<?php
				$contributionId		= $draft->blog_contribute;

				if ($blogSource == "group") {
					$contributionId = $extGroupId;
					$contributionType = "jomsocial";
				} else if( $blogSource == "event" ) {
					$contributionId = $external;
					$contributionType = "jomsocial.event";
				} else if ($teamId) {
					$contributionType	= 'easyblog';
					$contributionId 	= $teamId;
				}

				if (is_null($contributionId)) {
					$contributionId = 0;
				}
			?>
			<input type="hidden" id="blog_contribute_source" name="blog_contribute_source" value="<?php echo $contributionType;?>" />
			<input type="hidden" name="blog_contribute" id="blog_contribute" value="<?php echo $contributionId;?>" class="hidden_blog_contribute"/>
		<?php } else {?>

			<input type="hidden" id="blog_contribute_source" name="blog_contribute_source" value="easyblog" />
			<input type="hidden" name="blog_contribute" id="blog_contribute" value="<?php echo $external ? $external : 0;?>" class="hidden_blog_contribute"/>
		<?php } ?>
	</li>
	<?php } else { ?>
		<li style="display:none">
			<input type="hidden" name="blog_contribute" value="0" />
		</li>
	<?php } ?>

	<?php if ( $system->config->get('main_comment') && $this->acl->rules->change_setting_comment ) { ?>
	<li>
		<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_ALLOW_COMMENTING'); ?> :</label>
		<div style="margin-top:4px"><?php echo $this->renderCheckbox( 'allowcomment' , $allowComment );?></div>
	</li>
	<?php } ?>

	<?php if ( $this->acl->rules->change_setting_subscription && $system->config->get('main_subscription') ) { ?>
	<li>
		<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_ALLOW_SUBSCRIPTIONS'); ?> :</label>
		<div style="margin-top:4px"><?php echo $this->renderCheckbox( 'subscription' , $subscription );?></div>
	</li>
	<?php } ?>

	<?php if ( $this->acl->rules->contribute_frontpage ) { ?>
	<li>
		<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_DISPLAY_ON_FRONTPAGE'); ?> :</label>
		<div style="margin-top:4px">
			<?php echo $this->renderCheckbox( 'frontpage' , $frontpage );?>
		</div>
	</li>
	<?php } ?>


<li>
	<label><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS'); ?> :</label>
	<div style="margin-top:4px">
		<?php echo $this->renderCheckbox( 'send_notification_emails' , $send_notification_emails );?>
	</div>
</li>

<?php if($system->config->get('main_password_protect') && !$blog->isFeatured() ){ ?>
<li>
	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_PROTECTION'); ?> :</label>
	<div>
		<div><input type="text" class="input has-icon text width-250 publish-password" name="blogpassword" id="blogpassword" value="<?php echo $blog->blogpassword;?>" /></div>
		<div class="small"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_PUBLISHING_PROTECTION_INSTRUCTIONS' );?></div>
	</div>
</li>
<?php } ?>

<li>
	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_CREATION_DATE'); ?> : </label>
	<div>
		<?php
			jimport( 'joomla.utilities.date' );
			$date 			= EasyBlogDateHelper::getDate();
			$now 			= EasyBlogDateHelper::toFormat( $date );
			$displaynow 	= EasyBlogDateHelper::toFormat( $date, $system->config->get( 'layout_dateformat' ) );

			if($blog->created != "")
			{
				$newDate    	= EasyBlogDateHelper::getDate($blog->created);
				$now 			= EasyBlogDateHelper::toFormat( $newDate );
				$displaynow 	= EasyBlogDateHelper::toFormat( $newDate, $system->config->get( 'layout_dateformat' ) );
			}

			echo EasyBlogHelper::dateTimePicker('created', $displaynow, $now);
		?>
	</div>
</li>
<li>
	<label><?php echo JText::_('COM_EASYBLOG_PUBLISHING_DATE'); ?> :</label>
	<div>
		<?php
			if($blog->publish_up != "" && $blog->publish_up != "0000-00-00 00:00:00" )
			{
				$newDate    	= EasyBlogDateHelper::getDate($blog->publish_up);
				$now			= EasyBlogDateHelper::toFormat($newDate);
				$displaynow 	= EasyBlogDateHelper::toFormat( $newDate, $system->config->get( 'layout_dateformat' ) );
			}
			else {
				$now 			= EasyBlogDateHelper::toFormat($date);
				$displaynow 	= EasyBlogDateHelper::toFormat( $date, $system->config->get( 'layout_dateformat' ) );
			}

			echo EasyBlogHelper::dateTimePicker('publish_up', $blog->publish_up != '' ? $displaynow : JText::_('COM_EASYBLOG_IMMEDIATELY'), $now);
		?>
	</div>
</li>
<li>
	<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_UNPUBLISH_DATE'); ?> :</label>
	<div>
		<?php
			$notEmpty = true;
			if ( $blog->publish_down == "0000-00-00 00:00:00" || empty($blog->publish_down))
			{
				$newDate    	= EasyBlogDateHelper::getDate();
				$now			= '';
				$displaynow 	= '';
				$nowReset       = EasyBlogDateHelper::toFormat( $newDate );
				$notEmpty 		= false;
			}
			else {
				$newDate    	= EasyBlogDateHelper::getDate( $blog->publish_down );
				$now			= EasyBlogDateHelper::toFormat( $newDate );
				$displaynow 	= EasyBlogDateHelper::toFormat( $newDate, $system->config->get( 'layout_dateformat' ) );
				$nowReset       = EasyBlogDateHelper::toFormat( $newDate );
				$notEmpty 		= true;
			}

			echo EasyBlogHelper::dateTimePicker('publish_down', $notEmpty ? $displaynow : JText::_('COM_EASYBLOG_NEVER'), $now, true);
		?>

		<input type="hidden" name="publish_down_reset" id="publish_down_reset" value="<?php echo $nowReset; ?>"/>
		<input type="hidden" name="publish_down_ori" id="publish_down_ori" value="<?php echo $blog->publish_down; ?>"/>
	</div>
</li>

<?php if( $system->config->get( 'main_locations' ) ){ ?>
<?php echo $this->fetch( 'dashboard.write.options.location.php' ); ?>
<?php } ?>

<?php if( $system->config->get( 'main_copyrights' ) ){ ?>
<li>
	<label><?php echo JText::_( 'COM_EASYBLOG_COPYRIGHTS' ); ?> :</label>
	<div>
		<input type="text" name="copyrights" id="copyrights" class="input text width-full" value="<?php echo $this->escape( $blog->copyrights );?>" />
	</div>
</li>
<?php } ?>
</ul>
