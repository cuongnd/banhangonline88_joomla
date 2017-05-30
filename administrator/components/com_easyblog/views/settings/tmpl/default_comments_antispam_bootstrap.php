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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_TITLE' ); ?></legend>
			<div><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_DESC');?></div>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_akismet' , $this->config->get( 'comment_akismet' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY_DESC' ); ?></div>
							<input type="text" class="inputbox full-width" name="comment_akismet_key" value="<?php echo $this->config->get('comment_akismet_key');?>" size="60" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_FILTER_TRACKBACKS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_FILTER_TRACKBACKS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_akismet_trackback' , $this->config->get( 'comment_akismet_trackback' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_CAPTCHA' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tr>
						<td class="key" width="300">
							<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA' ); ?>
							</span>
						</td>
						<td>
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'comment_captcha' , $this->config->get( 'comment_captcha' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="key" width="300">
							<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED' ); ?>
							</span>
						</td>
						<td>
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'comment_captcha_registered' , $this->config->get( 'comment_captcha_registered' ) );?>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_INTEGRATIONS_TITLE' ); ?></legend>
			<p><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_INTEGRATIONS_DESC');?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA' ); ?>::<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_recaptcha' , $this->config->get( 'comment_recaptcha' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_USE_SSL' ); ?>::<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_USE_SSL_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_USE_SSL' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_USE_SSL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_recaptcha_ssl' , $this->config->get( 'comment_recaptcha_ssl' ) );?>
						</div>	
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY' ); ?>::<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY_DESC' ); ?></div>
							<input type="text" class="inputbox" name="comment_recaptcha_public" value="<?php echo $this->config->get('comment_recaptcha_public');?>" size="60" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY_DESC' ); ?></div>
							<input type="text" class="inputbox" name="comment_recaptcha_private" value="<?php echo $this->config->get('comment_recaptcha_private');?>" size="60" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_DESC' ); ?></div>
							<select name="comment_recaptcha_theme" class="inputbox">
								<option value="clean"<?php echo $this->config->get('comment_recaptcha_theme') == 'clean' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_CLEAN');?></option>
								<option value="white"<?php echo $this->config->get('comment_recaptcha_theme') == 'white' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_WHITE');?></option>
								<option value="red"<?php echo $this->config->get('comment_recaptcha_theme') == 'red' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_RED');?></option>
								<option value="blackglass"<?php echo $this->config->get('comment_recaptcha_theme') == 'blackglass' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_BLACKGLASS');?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_DESC' ); ?></div>
							<select name="comment_recaptcha_lang" class="inputbox">
								<option value="en"<?php echo $this->config->get('comment_recaptcha_lang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_ENGLISH');?></option>
								<option value="ru"<?php echo $this->config->get('comment_recaptcha_lang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_RUSSIAN');?></option>
								<option value="fr"<?php echo $this->config->get('comment_recaptcha_lang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_FRENCH');?></option>
								<option value="de"<?php echo $this->config->get('comment_recaptcha_lang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_GERMAN');?></option>
								<option value="nl"<?php echo $this->config->get('comment_recaptcha_lang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_DUTCH');?></option>
								<option value="pt"<?php echo $this->config->get('comment_recaptcha_lang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_PORTUGUESE');?></option>
								<option value="tr"<?php echo $this->config->get('comment_recaptcha_lang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_TURKISH');?></option>
								<option value="es"<?php echo $this->config->get('comment_recaptcha_lang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_SPANISH');?></option>
							</select>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

	</div>
</div>