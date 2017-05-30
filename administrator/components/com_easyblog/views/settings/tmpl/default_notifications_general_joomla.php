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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SETTINGS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_EXAMPLE' ); ?></div>
							<input type="text" class="inputbox full-width" name="notification_email" value="<?php echo $this->config->get('notification_email');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'custom_email_as_admin' , $this->config->get( 'custom_email_as_admin' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_HTML' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_HTML_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_mailqueuehtmlformat' , $this->config->get( 'main_mailqueuehtmlformat' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH_DESC' ); ?></div>
							<input type="text" name="main_mailtitle_length" class="inputbox" value="<?php echo $this->config->get( 'main_mailtitle_length' );?>" style="text-align:center;" size="5" />
							<span class="small"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
						</div>
					</td>
				</tr>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_PROCESSING_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_mailqueueonpageload' , $this->config->get( 'main_mailqueueonpageload' ) );?>
							<span style="padding-left: 10px;line-height:28px;">
								<a href="http://stackideas.com/docs/easyblog/how-tos/138-setting-up-cronjobs-in-cpanel" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_HELP_CRON' ); ?></a>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME_DESC' ); ?></div>
							<input type="text" name="main_mail_total" class="inputbox" value="<?php echo $this->config->get( 'main_mail_total' );?>" style="text-align:center;" size="5" />
							<span class="small"><?php echo JText::_( 'COM_EASYBLOG_EMAILS' ); ?></span>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
