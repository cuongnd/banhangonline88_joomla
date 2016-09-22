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
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>				
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_comment' , $this->config->get( 'main_comment' ) );?> 
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_COMMENT_THEME' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_COMMENT_THEME_DESC' ); ?></div>
							<?php
								$listLength = array();
								$listLength[] = JHTML::_('select.option', 'default', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_COMMENT_THEME_DEFAULT' ) );
								$listLength[] = JHTML::_('select.option', 'aero', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_COMMENT_THEME_AERO' ) );
								$listLength[] = JHTML::_('select.option', 'altium', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_COMMENT_THEME_ALTIUM' ) );
								echo JHTML::_('select.genericlist', $listLength, 'layout_comment_theme', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_comment_theme' ) );
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_autosubscribe' , $this->config->get( 'comment_autosubscribe' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_bbcode' , $this->config->get( 'comment_bbcode' ) );?>
						</div>	
						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_likes' , $this->config->get( 'comment_likes' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_allowguestcomment' , $this->config->get( 'main_allowguestcomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_allowguestviewcomment' , $this->config->get( 'main_allowguestviewcomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_registeroncomment' , $this->config->get( 'comment_registeroncomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_autotitle' , $this->config->get( 'comment_autotitle' ) );?>
						</div>
						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL_DESC' ); ?></div>
							<input type="text" class="inputbox" name="comment_maxthreadedlevel" value="<?php echo $this->config->get( 'comment_maxthreadedlevel' );?>" size="10" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_COMMENTS_ENABLE_AUTO_HYPERLINKS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_ENABLE_AUTO_HYPERLINKS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_autohyperlink' , $this->config->get( 'comment_autohyperlink' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_('COM_EASYBLOG_COMMENTS_ENABLE_PAGINATION'); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_COMMENTS_ENABLE_PAGINATION_DESC'); ?></div>
							<?php echo $this->getPaginationSettings('limit_comment' , $this->config->get( 'limit_comment'));?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_COMMENTS_SORTING' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_COMMENTS_SORTING_DESC'); ?></div>
							<?php
								$listLength = array();
								$listLength[] = JHTML::_('select.option', 'desc', JText::_('COM_EASYBLOG_COMMENTS_SORTING_OPTIONS_DESCENDING'));
								$listLength[] = JHTML::_('select.option', 'asc', JText::_( 'COM_EASYBLOG_COMMENTS_SORTING_OPTIONS_ASCENDING'));
								echo JHTML::_('select.genericlist', $listLength, 'comment_sorting', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('comment_sorting' , 'desc'));
							?>
						</div>
					</td>
				</tr>								
			</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_moderatecomment' , $this->config->get( 'comment_moderatecomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_moderateauthorcomment' , $this->config->get( 'comment_moderateauthorcomment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_moderateguestcomment' , $this->config->get( 'comment_moderateguestcomment' ) );?>
						</div>
						
					</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_requiretitle' , $this->config->get( 'comment_requiretitle' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_show_title' , $this->config->get( 'comment_show_title' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_require_email' , $this->config->get( 'comment_require_email' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_show_email' , $this->config->get( 'comment_show_email' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_require_website' , $this->config->get( 'comment_require_website' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_show_website' , $this->config->get( 'comment_show_website' ) );?>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_TNC_TITLE' );?></legend>

				<table class="admintable">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_tnc' , $this->config->get( 'comment_tnc' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TYPES' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TYPES_DESC' ); ?></div>
							<select name="comment_tnc_users" class="input">
								<option value="0"<?php echo $this->config->get( 'comment_tnc_users' ) == 0 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_GUESTS' ); ?></option>
								<option value="1"<?php echo $this->config->get( 'comment_tnc_users' ) == 1 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_ALL_REGISTERED_USERS' ); ?></option>
								<option value="2"<?php echo $this->config->get( 'comment_tnc_users' ) == 2 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_REGISTERED_USERS_AND_GUESTS' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top;">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT' ); ?>
					</span>
					</td>
					<td valign="top">						
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT_DESC' ); ?></div>
							<textarea name="comment_tnctext" class="inputbox full-width" style="width:100%;" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('comment_tnctext' )); ?></textarea>
						</div>
					</td>
				</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>