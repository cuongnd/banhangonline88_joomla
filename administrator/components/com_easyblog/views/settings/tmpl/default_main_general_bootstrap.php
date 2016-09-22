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
	<div class="span6">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_INFO_TITLE' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOGINFO_INFO' ); ?></p>
		<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE_DESC' ); ?></div>
						<input type="text" name="main_title" class="inputbox full-width" value="<?php echo $this->config->get('main_title');?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_AUTOMATIC_APPEND_BLOG_TITLE_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_pagetitle_autoappend' , $this->config->get( 'main_pagetitle_autoappend' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION_DESC' ); ?></div>
						<textarea name="main_description" rows="5" class="inputbox full-width textarea" cols="35"><?php echo $this->config->get('main_description');?></textarea>
					</div>
				</td>
			</tr>
		</tbody>
		</table>
		</fieldset>


		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_FEATURES_TITLE' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_FEATURES_INFO' ); ?></p>
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_password_protect' , $this->config->get( 'main_password_protect' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_copyrights' , $this->config->get( 'main_copyrights' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_autofeatured' , $this->config->get( 'main_autofeatured' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_login_read' , $this->config->get( 'main_login_read' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_INTROTEXT_IN_ENTRY' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_INTROTEXT_IN_ENTRY_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_hideintro_entryview' , $this->config->get( 'main_hideintro_entryview' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_categories_hideempty' , $this->config->get( 'main_categories_hideempty' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LIST_PRIVATE_TEAMBLOG' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LIST_PRIVATE_TEAMBLOG_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_listprivateteamblog' , $this->config->get( 'main_listprivateteamblog' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_TEAMBLOG_INCLUDE_TEAMBLOG_POSTS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_TEAMBLOG_INCLUDE_TEAMBLOG_POSTS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_includeteamblogpost' , $this->config->get( 'main_includeteamblogpost' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_bloggerlistingoption' , $this->config->get( 'main_bloggerlistingoption' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS_DESC' ); ?></div>
						<input type="text" name="layout_exclude_bloggers" class="inputbox" value="<?php echo $this->config->get( 'layout_exclude_bloggers' );?>" />
					</div>

				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_CATEGORIES_FROM_FRONTPAGE' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_CATEGORIES_FROM_FRONTPAGE_DESC' ); ?></div>
						<input type="text" name="layout_exclude_categories" class="inputbox" value="<?php echo $this->config->get( 'layout_exclude_categories' );?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_BLOGGER_TO_SWITCH' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_BLOGGER_TO_SWITCH_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_blogprivacy_override' , $this->config->get( 'main_blogprivacy_override' ) );?>
					</div>
				</td>
			</tr>

			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DAYLIGHT_SAVING_TIME' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DAYLIGHT_SAVING_TIME_DESC' ); ?></div>
						<?php echo $this->dstList; ?>
					</div>
				</td>
			</tr>

			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_NON_BLOGGER_PROFILE' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_NON_BLOGGER_PROFILE_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_nonblogger_profile' , $this->config->get( 'main_nonblogger_profile' ) );?>
					</div>
				</td>
			</tr>

			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_anchor_nofollow' , $this->config->get( 'main_anchor_nofollow' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_multi_language' , $this->config->get( 'main_multi_language' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_GENERAL_ALLOW_JOIN_TEAM' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_GENERAL_ALLOW_JOIN_TEAM_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'teamblog_allow_join' , $this->config->get( 'teamblog_allow_join' ) );?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		</fieldset>


		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_OPTIONS' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_INFO' );?></p>
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PUBLISHING_STATUS' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PUBLISHING_STATUS_DESC' ); ?></div>
						<?php
	  						$publishFormat = array();
							$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $publishFormat, 'main_blogpublishing', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_blogpublishing' , '1' ) );
							echo $showdet;
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_NEW_ENTRY_ON_FRONTPAGE' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_NEW_ENTRY_ON_FRONTPAGE_DESC' ); ?></div>
						<?php
	  						$frontpageFormat = array();
							$frontpageFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_NO_OPTION' ) );
							$frontpageFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_YES_OPTION' ) );
							echo JHTML::_('select.genericlist', $frontpageFormat, 'main_newblogonfrontpage', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_newblogonfrontpage' ) );
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY' ); ?>
				</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY_DESC' ); ?></div>
						<?php
	  						//$nameFormat = array();
							//$nameFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_PUBLIC_OPTION' ) );
							//$nameFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PRIVATE_OPTION' ) );
							$nameFormat = EasyBlogHelper::getHelper('privacy')->getOptions();
							$showdet = JHTML::_('select.genericlist', $nameFormat, 'main_blogprivacy', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_blogprivacy' ) );
							echo $showdet;
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SEND_NOTIFICATION_EMAILS_DESC' ); ?></div>
						<?php
	  						$frontpageFormat = array();
							$frontpageFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_NO_OPTION' ) );
							$frontpageFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_YES_OPTION' ) );
							echo JHTML::_('select.genericlist', $frontpageFormat, 'main_sendemailnotifications', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_sendemailnotifications' ) );
						?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		</fieldset>

	</div>

	<div class="span6">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_TRACKBACKS' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_TRACKBACKS_DESC' ); ?></p>
		<table class="admintable" cellspacing="1">
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TRACKBACKS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TRACKBACKS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_trackbacks' , $this->config->get( 'main_trackbacks' , true ) );?>
					</div>
				</td>
			</tr>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HITS' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_HITS_DESC' ); ?></p>
		<table class="admintable" cellspacing="1">
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_hits_session' , $this->config->get( 'main_hits_session' , true ) );?>
					</div>
				</td>
			</tr>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO' ); ?></p>
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_REPORTING' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_REPORTING_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_reporting' , $this->config->get( 'main_reporting' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_REPORTS_ALLOW_GUEST_TO_REPORT' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_REPORTS_ALLOW_GUEST_TO_REPORT_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_reporting_guests' , $this->config->get( 'main_reporting_guests' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_REPORTS_MAX_REPORTS_PER_IP' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_REPORTS_MAX_REPORTS_PER_IP_DESC' ); ?></div>
						<input type="text" size="5" value="<?php echo $this->config->get( 'main_reporting_maxip' );?>" name="main_reporting_maxip" style="text-align:center;" />
						<span style="line-height: 24px;" class="small"><?php echo JText::_( 'COM_EASYBLOG_REPORTS_REPORTS' ); ?></span>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_CONTENT_FILTERING' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_BLOCKED_WORDS_INFO' ); ?></p>
		<table class="admintable" cellpadding="1">
		<tbody>
		<tr>
			<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_BLOCKED_WORDS' ); ?>
				</span>
			</td>
			<td valign="top">
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_BLOCKED_WORDS_DESC' ); ?></div>
					<textarea class="inputbox full-width textarea" name="main_blocked_words"><?php echo $this->config->get( 'main_blocked_words' ); ?></textarea>
				</div>
			</td>
		</tr>
		</tbody>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ANTI_SPAM' ); ?></legend>
		<table class="admintable" cellpadding="1">
		<tbody>
		<tr>
			<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH' ); ?>
				</span>
			</td>
			<td valign="top">
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH_DESC' ); ?></div>
					<?php echo $this->renderCheckbox( 'main_post_min' , $this->config->get( 'main_post_min' ) ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH' ); ?>
				</span>
			</td>
			<td valign="top">
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH_DESC' ); ?></div>
					<input type="text" name="main_post_length" value="<?php echo $this->escape( $this->config->get( 'main_post_length' ) );?>" style="width:50px;text-align:center;" />
					<span class="small" style="line-height: 24px;"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' );?></span>
				</div>
			</td>
		</tr>
		</tbody>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS' ); ?></legend>
		<p class=""><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS_INFO' );?></p>
		<table class="admintable" cellspacing="1">
			<tbody>
			<tr>
				<td width="300" class="key">
				<span class="editlinktip">
					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS' ); ?>
				</span>
				</td>
				<td valign="top" class="value">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_joomlauserparams' , $this->config->get( 'main_joomlauserparams' , true ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_EDIT_ACCOUNT' ); ?>
					</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_EDIT_ACCOUNT_DESC' ); ?></div>
						<?php echo $this->renderCheckbox( 'main_dashboard_editaccount' , $this->config->get( 'main_dashboard_editaccount' ) );?>
					</div>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER' ); ?>
					</span>
				</td>
				<td valign="top">
					<div class="has-tip">
						<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER_DESC' ); ?></div>
						<select class="inputbox" name="main_login_provider">
							<option value="easysocial"<?php echo $this->config->get( 'main_login_provider' ) == 'easysocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL' );?></option>
							<option value="joomla"<?php echo $this->config->get( 'main_login_provider' ) == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOOMLA' );?></option>
							<option value="cb"<?php echo $this->config->get( 'main_login_provider' ) == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_CB' );?></option>
							<option value="jomsocial"<?php echo $this->config->get( 'main_login_provider' ) == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOMSOCIAL' );?></option>
						</select>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
</div>

