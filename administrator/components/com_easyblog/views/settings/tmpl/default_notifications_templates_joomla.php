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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key" valign="top" style="vertical-align: top !important;">
						<span><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME'); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME_INFO' ); ?></div>
							<input type="text" class="inputbox full-width" name="notification_from_name" value="<?php echo $this->config->get('notification_from_name' , $this->jConfig->get( 'fromname' ) );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td class="key" valign="top" style="vertical-align: top !important;">
						<span><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL'); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL_INFO' ); ?></div>
							<input type="text" class="inputbox full-width" name="notification_from_email" value="<?php echo $this->config->get('notification_from_email' , $this->jConfig->get( 'mailfrom' ) );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td class="key" valign="top" style="vertical-align: top !important;">
						<span><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_TITLE'); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_TITLE_INFO' ); ?></div>
							<input type="text" class="inputbox full-width" name="notifications_title" value="<?php echo $this->config->get('notifications_title' , $this->jConfig->get( 'sitename' ) );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key" valign="top" style="vertical-align: top !important;">
						<span><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_FILENAME'); ?></span>
					</td>
					<td valign="top">
						<?php echo $this->getEmailsTemplate(); ?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>