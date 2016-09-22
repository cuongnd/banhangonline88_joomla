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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_sitesubscription' , $this->config->get( 'main_sitesubscription' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_subscription' , $this->config->get( 'main_subscription' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_bloggersubscription' , $this->config->get( 'main_bloggersubscription' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_categorysubscription' , $this->config->get( 'main_categorysubscription' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_teamsubscription' , $this->config->get( 'main_teamsubscription' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_allowguestsubscribe' , $this->config->get( 'main_allowguestsubscribe' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_registeronsubscribe' , $this->config->get( 'main_registeronsubscribe' ) );?>
						</div>
					</td>
				</tr>
                </tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_MAILCHIMP_INTEGRATIONS' ); ?></legend>
			<p class="small">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/chimp.png" style="float:left;" />
				<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_INFO' );?>
				<br /><br />
				<a href="http://eepurl.com/ori65" target="_blank" class="button"><?php echo JText::_( 'COM_EASYBLOG_SIGNUP_WITH_MAILCHIMP' ); ?></a>
			</p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_ENABLE' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_ENABLE' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'subscription_mailchimp' , $this->config->get( 'subscription_mailchimp' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_APIKEY' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_APIKEY' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_APIKEY_DESC' ); ?></div>
							<input type="text" name="subscription_mailchimp_key" value="<?php echo $this->config->get( 'subscription_mailchimp_key' );?>" class="inputbox full-width" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_LISTID' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_LISTID' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_LISTID_DESC' ); ?></div>
							<input type="text" name="subscription_mailchimp_listid" value="<?php echo $this->config->get( 'subscription_mailchimp_listid' );?>" class="inputbox" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_WELCOME_EMAIL' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_WELCOME_EMAIL' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_WELCOME_EMAIL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'subscription_mailchimp_welcome' , $this->config->get( 'subscription_mailchimp_welcome' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_NOTIFICATION' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_NOTIFICATION' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SEND_NOTIFICATION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'mailchimp_campaign' , $this->config->get( 'mailchimp_campaign' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_NAME' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_NAME' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_NAME_DESC' ); ?></div>
							<input type="text" name="mailchimp_from_name" value="<?php echo $this->config->get( 'mailchimp_from_name' , EasyBlogHelper::getJConfig()->get( 'fromname' ) );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip" title="<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL' ); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL_DESC' ); ?></div>
							<input type="text" name="mailchimp_from_email" value="<?php echo $this->config->get( 'mailchimp_from_email' , EasyBlogHelper::getJConfig()->get( 'mailfrom' ) );?>" />
						</div>
					</td>
				</tr>
                </tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>