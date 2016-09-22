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
?>
<div class="pa-15">
	<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/linkedin_setup.png" style="float:left;margin-right:20px;" />
	<h3 class="head-3"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_LINKEDIN');?></h3>
	<div class="clear"></div>
	<table width="100%" class="admintable">
		<tr>
			<td valign="top" valign="top">
				<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_BASIC_SETTINGS' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE_DESC'); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_linkedin' , $this->config->get( 'integrations_linkedin' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span>
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY_DESC'); ?></div>
								<input type="text" name="integrations_linkedin_api_key" class="inputbox" value="<?php echo $this->config->get('integrations_linkedin_api_key');?>" size="60" />
								<a href="http://stackideas.com/docs/easyblog/how-tos/142-setting-up-linkedin-integration" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY_DESC'); ?></div>
								<input type="text" name="integrations_linkedin_secret_key" class="inputbox" value="<?php echo $this->config->get('integrations_linkedin_secret_key');?>" size="60" />
								<a href="http://stackideas.com/docs/easyblog/how-tos/142-setting-up-linkedin-integration" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_CENTRALIZED' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_DESC', 'LinkedIn'); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_linkedin_centralized' , $this->config->get( 'integrations_linkedin_centralized' ) );?>
							</div>
						</td>
					</tr>
					<?php if( $this->companies && !empty( $this->companies ) ){ ?>
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_DESC' ); ?></div>

								<select name="integrations_linkedin_company[]" multiple style="height: 150px;">
									<?php foreach( $this->companies as $company ){ ?>
									<option value="<?php echo $company->id;?>"<?php echo in_array( $company->id , $this->storedCompanies ) ? ' selected="selected"' : '';?>><?php echo $company->title;?></option>
									<?php } ?>
								</select>

							</div>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_ACCESS'); ?></span>
						</td>
						<td class="paramlist_value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'LinkedIn'); ?></div>
								<?php if( $this->isAssociated ){ ?>
								<div>
									<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&c=autoposting&task=revoke&type=' . EBLOG_OAUTH_LINKEDIN . '&return=form');?>"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?></a>
								</div>
								<?php } else { ?>
									<div><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?></div>
									<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&c=autoposting&task=request&type=' . EBLOG_OAUTH_LINKEDIN . '&return=form');?>">
										<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/linkedin_signon.png" border="0" alt="here" />
									</a>
								<?php } ?>
							</div>
						</td>
					</tr>

					</tbody>
				</table>
				</fieldset>
			</td>
			<td width="50%" valign="top">
				<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_ADVANCED_SETTINGS' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?></span>
						</td>
					    <td class="paramlist_value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES_DESC', 'LinkedIn'); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_linkedin_centralized_auto_post' , $this->config->get('integrations_linkedin_centralized_auto_post', false) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_OAUTH_SEND_UPDATES'); ?></span>
						</td>
						<td class="paramlist_value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_SEND_UPDATES_DESC', 'LinkedIn'); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_linkedin_centralized_send_updates' , $this->config->get('integrations_linkedin_centralized_send_updates', false) );?>
							</div>							
						</td>
					</tr>
					<tr>
						<td width="300" class="key" style="vertical-align: top !important;">
							<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE_DESC'); ?></div>
								<textarea name="main_linkedin_message" class="inputbox half-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('main_linkedin_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
								<div class="notice half-width"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span>
								<?php echo JText::sprintf( 'COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'LinkedIn' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT_DESC', 'LinkedIn'); ?></div>
								<?php echo $this->renderCheckbox( 'integrations_linkedin_centralized_and_own' , $this->config->get( 'integrations_linkedin_centralized_and_own', false ) );?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
</div>
