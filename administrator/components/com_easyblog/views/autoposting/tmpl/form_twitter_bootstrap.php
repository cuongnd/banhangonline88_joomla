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
<div class="row-fluid">
	<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/twitter_setup.png" style="float:left;margin-right:20px;" />
	<h3 class="head-3"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER');?></h3>
</div>

<div class="row-fluid">

	<div class="tabbable">

		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#twitter-basic" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_BASIC_SETTINGS' ); ?></a>
			</li>
			<li>
				<a href="#twitter-advanced" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_ADVANCED_SETTINGS' ); ?></a>
			</li>
			<li>
				<a href="#twitter-url" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER' ); ?></a>
			</li>
		</ul>
	</div>

	<div class="tab-content">
		<div id="twitter-basic" class="tab-pane active">
			<table class="table table-striped">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE_DESC'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter' , $this->config->get( 'integrations_twitter' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY_DESC'); ?></div>
							<input type="text" name="integrations_twitter_api_key" class="inputbox" value="<?php echo $this->config->get('integrations_twitter_api_key');?>" size="60" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/141-setting-up-twitter-integration" target="_BLANK" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY_DESC'); ?></div>
							<input type="text" name="integrations_twitter_secret_key" class="inputbox" value="<?php echo $this->config->get('integrations_twitter_secret_key');?>" size="60" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/141-setting-up-twitter-integration" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_AUTOPOSTING_CENTRALIZED' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_DESC', 'Twitter'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_centralized' , $this->config->get( 'integrations_twitter_centralized' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span>
							<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_ACCESS'); ?>
						</span>
					</td>
					<td class="paramlist_value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Twitter'); ?></div>
							<?php if( $this->isAssociated ){ ?>
							<div>
								<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&c=autoposting&task=revoke&type=' . EBLOG_OAUTH_TWITTER . '&return=form');?>"><?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?></a>
							</div>
							<?php } else { ?>
								<div><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></div>
								<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&c=autoposting&task=request&type=' . EBLOG_OAUTH_TWITTER . '&return=form');?>">
									<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/twitter_signon.png" border="0" alt="here" />
								</a>
							<?php } ?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div id="twitter-advanced" class="tab-pane">
			<table class="table table-striped">
				<tbody>
				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?></span>
					</td>
				    <td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES_DESC', 'Twitter'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_auto_post' , $this->config->get('integrations_twitter_centralized_auto_post', false) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span>
							<?php echo JText::_('COM_EASYBLOG_OAUTH_SEND_UPDATES'); ?>
						</span>
					</td>
					<td class="paramlist_value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_OAUTH_SEND_UPDATES_DESC', 'Twitter'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_send_updates' , $this->config->get('integrations_twitter_centralized_send_updates', false) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span>
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_DESC'); ?></div>
							<textarea name="main_twitter_message" class="inputbox half-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('main_twitter_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
							<div class="notice half-width"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span><?php echo JText::sprintf( 'COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'Twitter' ); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT_DESC', 'Twitter'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_and_own' , $this->config->get( 'integrations_twitter_centralized_and_own', false ) );?>
						</div>							
					</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div id="twitter-url" class="tab-pane">
			<table class="table table-striped">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_SHORTEN_URL' ); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_SHORTEN_URL_DESC'); ?></div>
							<?php echo $this->renderCheckbox( 'main_twitter_shorten_url' , $this->config->get( 'main_twitter_shorten_url' ) );?>
						</div>							
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_LOGIN' ); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_LOGIN_DESC'); ?></div>
							<input type="text" name="main_twitter_urlshortener_login" class="inputbox" value="<?php echo $this->config->get('main_twitter_urlshortener_login');?>" size="60" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_APIKEY' ); ?></span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BITLY_APIKEY_DESC'); ?></div>
							<input type="text" name="main_twitter_urlshortener_apikey" class="inputbox" value="<?php echo $this->config->get('main_twitter_urlshortener_apikey');?>" size="60" />
						</div>							
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
