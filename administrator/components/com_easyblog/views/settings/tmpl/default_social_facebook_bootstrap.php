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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_facebook_like' , $this->config->get( 'main_facebook_like' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_FRONTPAGE' ); ?>
						</span>
					</td>
					<td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_facebook_show_in_listing' , $this->config->get( 'integrations_facebook_show_in_listing' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID_DESC' ); ?></div>
							<input type="text" name="main_facebook_like_admin" class="inputbox full-width" value="<?php echo $this->config->get('main_facebook_like_admin');?>" size="60" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/140-setting-up-facebook-integration" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID_DESC' ); ?></div>
							<input type="text" name="main_facebook_like_appid" class="inputbox full-width" value="<?php echo $this->config->get('main_facebook_like_appid');?>" size="60" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/140-setting-up-facebook-integration" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_BUTTON_STYLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_facebook_like_faces' , $this->config->get( 'main_facebook_like_faces' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_facebook_like_send' , $this->config->get( 'main_facebook_like_send' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_POSITION' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_POSITION_DESC' ); ?></div>
							<select id="main_facebook_like_position" name="main_facebook_like_position" class="inputbox" onchange="switchFBPosition();">
								<option<?php echo $this->config->get( 'main_facebook_like_position' ) == '1' ? ' selected="selected"' : ''; ?> value="1"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_POSITION_OPTION_SIDE_BY_SIDE');?></option>
								<option<?php echo $this->config->get( 'main_facebook_like_position' ) == '0' ? ' selected="selected"' : ''; ?> value="0"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_POSITION_OPTION_DEFAULT');?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_LAYOUT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_LAYOUT_DESC' ); ?></div>
							<table width="100%" class="social-buttons-preview">
								<td valign="top" id="fb-likes-standard" <?php echo ($this->config->get( 'main_facebook_like_position' ) == '1') ? 'style="display:none;"' : ''; ?> width="30%">
									<div>
										<input type="radio" name="main_facebook_like_layout" id="standard" value="standard"<?php echo $this->config->get('main_facebook_like_layout') == 'standard' ? ' checked="checked"' : '';?> />
										<label for="standard"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_BUTTON_STANDARD');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/facebook/standard.png';?>" /></div>
								</td>

								<td valign="top" width="30%">
									<div>
										<input type="radio" name="main_facebook_like_layout" id="box_count" value="box_count"<?php echo $this->config->get('main_facebook_like_layout') == 'box_count' ? ' checked="checked"' : '';?> />
										<label for="box_count"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_LARGE');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/facebook/box_count.png';?>" /></div>
								</td>

								<td valign="top" width="30%">
									<div>
										<input type="radio" name="main_facebook_like_layout" id="button_count" value="button_count"<?php echo $this->config->get('main_facebook_like_layout') == 'button_count' ? ' checked="checked"' : '';?> />
										<label for="button_count"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SMALL');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/facebook/button_count.png';?>" /></div>
								</td>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_WIDTH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_WIDTH_DESC' ); ?></div>
							<input type="text" name="main_facebook_like_width" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_facebook_like_width');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_DESC' ); ?></div>
							<select name="main_facebook_like_verb" class="inputbox">
								<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'like' ? ' selected="selected"' : ''; ?> value="like"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES');?></option>
								<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'recommend' ? ' selected="selected"' : ''; ?> value="recommend"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS');?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DESC' ); ?></div>
							<select name="main_facebook_like_theme" class="inputbox">
								<option<?php echo $this->config->get( 'main_facebook_like_theme' ) == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
								<option<?php echo $this->config->get( 'main_facebook_like_theme' ) == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
							</select>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_ADVANCED_SETTINGS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_facebook_scripts' , $this->config->get( 'main_facebook_scripts' ) );?>
							<div class="notice full-width" style="margin-top:20px;"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS_DESC' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_facebook_opengraph' , $this->config->get( 'main_facebook_opengraph' ) );?>
							<div class="notice full-width" style="margin-top:20px;"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS_WARN' ); ?></div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>
	</div>
</div>
