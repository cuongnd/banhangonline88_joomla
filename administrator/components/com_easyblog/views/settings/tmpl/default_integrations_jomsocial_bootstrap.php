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
?>
<div class="row-fluid">
	<div class="span12">

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_GENERAL_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_PRIVACY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_PRIVACY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_jomsocial_privacy' , $this->config->get( 'main_jomsocial_privacy' ) );?>
							<span style="padding-left: 10px;line-height:28px;" class="">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_PRIVACY_INFO' );?>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_TOOLBAR' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_TOOLBAR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_toolbar' , $this->config->get( 'integrations_jomsocial_toolbar' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FRIEND' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FRIEND_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_jomsocial_friends' , $this->config->get( 'main_jomsocial_friends' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_JOMSOCIAL_PROFILE_LINK' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_JOMSOCIAL_PROFILE_LINK_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_profile_link' , $this->config->get( 'integrations_jomsocial_profile_link' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MESSAGING' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MESSAGING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_jomsocial_messaging' , $this->config->get( 'main_jomsocial_messaging' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_USERPOINT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_USERPOINT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_jomsocial_userpoint' , $this->config->get( 'main_jomsocial_userpoint' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_BLOGGER_STATUS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_BLOGGER_STATUS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_blogger_status' , $this->config->get( 'integrations_jomsocial_blogger_status' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_TITLE' ); ?></legend>
			<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_DESC' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_BLOG' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_BLOG_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_notification_blog' , $this->config->get( 'integrations_jomsocial_notification_blog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_notification_comment' , $this->config->get( 'integrations_jomsocial_notification_comment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_RATING' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_RATING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_notification_rating' , $this->config->get( 'integrations_jomsocial_notification_rating' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT_FOLLOWER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT_FOLLOWER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_notification_comment_follower' , $this->config->get( 'integrations_jomsocial_notification_comment_follower' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_TITLE' ); ?></legend>
				<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_INFO' );?></p>
				<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ENABLE_MEDIA' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ENABLE_MEDIA_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_album' , $this->config->get( 'integrations_jomsocial_album' ) );?>
						</div>
					</td>
				</tr>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_STREAM_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_blog_new_activity' , $this->config->get( 'integrations_jomsocial_blog_new_activity' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_RSS_IMPORT_NEW_POST_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_RSS_IMPORT_NEW_POST_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_rss_import_activity' , $this->config->get( 'integrations_jomsocial_rss_import_activity' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_blog_update_activity' , $this->config->get( 'integrations_jomsocial_blog_update_activity' ) );?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UNPUBLISH_POST_REMOVE_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UNPUBLISH_POST_REMOVE_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_unpublish_remove_activity' , $this->config->get( 'integrations_jomsocial_unpublish_remove_activity' ) );?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_COMMENT_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_COMMENT_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_comment_new_activity' , $this->config->get( 'integrations_jomsocial_comment_new_activity' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_feature_blog_activity' , $this->config->get( 'integrations_jomsocial_feature_blog_activity' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_submit_content' , $this->config->get( 'integrations_jomsocial_submit_content' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_show_category' , $this->config->get( 'integrations_jomsocial_show_category' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_LIKES' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_LIKES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_activity_likes' , $this->config->get( 'integrations_jomsocial_activity_likes' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_jomsocial_activity_comments' , $this->config->get( 'integrations_jomsocial_activity_comments' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH_DESC' ); ?></div>
							<input type="text" name="integrations_jomsocial_blogs_length" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('integrations_jomsocial_blogs_length');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH_DESC' ); ?></div>
							<input type="text" name="integrations_jomsocial_comments_length" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('integrations_jomsocial_comments_length');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH_DESC' ); ?></div>
							<input type="text" name="jomsocial_blog_title_length" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('jomsocial_blog_title_length');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>
	</div>
</div>
