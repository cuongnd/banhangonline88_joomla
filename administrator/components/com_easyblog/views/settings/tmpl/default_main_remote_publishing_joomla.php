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

<script type="text/javascript">

EasyBlog.ready(function($){

	window.testMailboxConnection = function()
	{
		var server	= $('#main_remotepublishing_mailbox_remotesystemname').val();
		var port	= $('#main_remotepublishing_mailbox_port').val();
		var service	= $('#main_remotepublishing_mailbox_service').val();
		var ssl		= $('#main_remotepublishing_mailbox_ssl').val();
		var mailbox	= $('#main_remotepublishing_mailbox_mailboxname').val();
		var user	= $('#main_remotepublishing_mailbox_username').val();
		var pass	= $('#main_remotepublishing_mailbox_password').val();

		$('#remote_test_result').html('<img src="<?php echo JURI::root(); ?>components/com_easyblog/assets/images/loader.gif" />');

		var result	= ejax.call('settings', 'testMailboxConnection', [server, port, service, ssl, mailbox, user, pass], {
			success: function()
			{

			},
			error: function()
			{

			}
		});
	}

});
</script>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING' ); ?></legend>
			<p class="small">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/emailpublishing.png" align="left" style="margin: 0 8px 8px;padding: 0 8px;"/>
				<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_DESC' ); ?>
				<br /><br />
				<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROMOTE_PUBLISHING_MAILBOX_INSTRUCTION'); ?> <a href="http://stackideas.com/docs/easyblog/how-tos/138-setting-up-cronjobs-in-cpanel" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_HELP_CRON' ); ?></a>
			</p>
			<div style="clear:both;"></div>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_DESC' ); ?></div>
							<button type="button" onclick="testMailboxConnection()"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_BUTTON');?></button>
							<span id="remote_test_result"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox' , $this->config->get( 'main_remotepublishing_mailbox' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME_DESC' ); ?></div>
							<input type="text" id="main_remotepublishing_mailbox_remotesystemname" name="main_remotepublishing_mailbox_remotesystemname" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_remotesystemname');?>" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/151-setting-up-email-blogging" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT_DESC' ); ?></div>
							<input type="text" id="main_remotepublishing_mailbox_port" name="main_remotepublishing_mailbox_port" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_port');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_DESC' ); ?></div>
							<?php
								$services = array();
								$services[] = JHTML::_('select.option', 'imap', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_IMAP' ) );
								$services[] = JHTML::_('select.option', 'pop3', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_POP3' ) );
								echo JHTML::_('select.genericlist', $services, 'main_remotepublishing_mailbox_service', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_service') );
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SSL' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SSL_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox_ssl' , $this->config->get( 'main_remotepublishing_mailbox_ssl' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_VALIDATE_CERT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_VALIDATE_CERT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox_validate_cert' , $this->config->get( 'main_remotepublishing_mailbox_validate_cert' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME' ); ?></div>
							<input type="text" id="main_remotepublishing_mailbox_mailboxname" name="main_remotepublishing_mailbox_mailboxname" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_mailboxname');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME_DESC' ); ?></div>
							<input type="text" id="main_remotepublishing_mailbox_username" name="main_remotepublishing_mailbox_username" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_username');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD_DESC' ); ?></div>
							<input type="password" id="main_remotepublishing_mailbox_password" autocomplete="off" name="main_remotepublishing_mailbox_password" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_password');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX_DESC' ); ?></div>
							<input type="text" name="main_remotepublishing_mailbox_prefix" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_remotepublishing_mailbox_prefix');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL_DESC' ); ?></div>
							<input type="text" name="main_remotepublishing_mailbox_run_interval" class="inputbox" style="width: 50px;text-align:center;" maxlength="2" value="<?php echo $this->config->get('main_remotepublishing_mailbox_run_interval', '5' );?>" />
							<?php echo JText::_( 'COM_EASYBLOG_MINUTES' ); ?>
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST_DESC' ); ?></div>
							<textarea id="main_remotepublishing_mailbox_from_whitelist" name="main_remotepublishing_mailbox_from_whitelist" class="inputbox" /><?php echo $this->config->get('main_remotepublishing_mailbox_from_whitelist');?></textarea>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT_DESC' ); ?></div>
							<?php
								$fetchLimit = array();
								$fetchLimit[] = JHTML::_('select.option', '1', JText::_( '1' ) );
								$fetchLimit[] = JHTML::_('select.option', '2', JText::_( '2' ) );
								$fetchLimit[] = JHTML::_('select.option', '3', JText::_( '3' ) );
								$fetchLimit[] = JHTML::_('select.option', '4', JText::_( '4' ) );
								$fetchLimit[] = JHTML::_('select.option', '5', JText::_( '5' ) );
								$fetchLimit[] = JHTML::_('select.option', '10', JText::_( '10' ) );
								$fetchLimit[] = JHTML::_('select.option', '15', JText::_( '15' ) );
								$fetchLimit[] = JHTML::_('select.option', '20', JText::_( '20' ) );
								$fetchLimit[] = JHTML::_('select.option', '50', JText::_( '50' ) );

								$showdet = JHTML::_('select.genericlist', $fetchLimit, 'main_remotepublishing_mailbox_fetch_limit', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_fetch_limit' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_OPTIONS' ); ?></legend>
			<table class="admintable">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_DESC' ); ?></div>
							<?php
								$contentType = array();
								$contentType[] = JHTML::_('select.option', 'html', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_HTML_OPTION' ) );
								$contentType[] = JHTML::_('select.option', 'plain', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_PLAINTEXT_OPTION' ) );

								$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_format', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_format' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAP_USERS_EMAIL' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAP_USERS_EMAIL_DESC' ); ?></div>
			    			<div style="margin-right: 10px; float: left;" class="half-width">
			    				<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox_syncuser' , $this->config->get( 'main_remotepublishing_mailbox_syncuser' ) ); ?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER_DESC' ); ?></div>
			    			<div style="margin-right: 10px; float: left;" class="half-width">
								<input type="hidden" name="main_remotepublishing_mailbox_userid" id="main_remotepublishing_mailbox_userid" value="<?php echo $this->config->get('main_remotepublishing_mailbox_userid') ?>" />
								<?php
									$user	= JFactory::getUser($this->config->get('main_remotepublishing_mailbox_userid'));
									$main_remotepublishing_mailbox_username	= $user->name;
								?>
			    				<div style="float: left; margin: 5px 5px 0 0;" id="remotePublishName"><?php echo $main_remotepublishing_mailbox_username; ?></div>
								<div style="float: left; margin-top: 5px;">[ <a class="modal" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1&browsefunction=insertMailboxDefaultUserId"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a> ]</div>
								<div style="clear:both;"></div>
							</div>

						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE_DESC' ); ?></div>
								<?php
									$contentType = array();
									$contentType[] = JHTML::_('select.option', 'intro', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_INTRO' ) );
									$contentType[] = JHTML::_('select.option', 'content', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_CONTENT' ) );

									$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_type', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_type' ) );
									echo $showdet;
								?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY_DESC' ); ?></div>
							<?php echo EasyBlogHelper::populateCategories('', '', 'select', 'main_remotepublishing_mailbox_categoryid', $this->config->get( 'main_remotepublishing_mailbox_categoryid') , true); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE_DESC' ); ?></div>
								<?php
									$publishFormat = array();
									$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
									$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
									$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
									$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

									$showdet = JHTML::_('select.genericlist', $publishFormat, 'main_remotepublishing_mailbox_publish', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_publish' , '1' ) );
									echo $showdet;
								?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY_DESC' ); ?></div>
								<?php
									$privacies = array();
									$privacies[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_PRIVACY_ALL_OPTION' ) );
									$privacies[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PRIVACY_REGISTERED_OPTION' ) );

									$showdet = JHTML::_('select.genericlist', $privacies, 'main_remotepublishing_mailbox_privacy', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_privacy' , '0' ) );
									echo $showdet;
								?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FRONTPAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox_frontpage' , $this->config->get( 'main_remotepublishing_mailbox_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_ATTACHMENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_ATTACHMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_remotepublishing_mailbox_image_attachment' , $this->config->get( 'main_remotepublishing_mailbox_image_attachment' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
