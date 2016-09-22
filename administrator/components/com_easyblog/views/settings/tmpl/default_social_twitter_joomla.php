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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_twitter_button' , $this->config->get( 'main_twitter_button' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHOW_ON_FRONTPAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHOW_ON_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_twitter_button_frontpage' , $this->config->get( 'main_twitter_button_frontpage' ) );?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME_DESC' ); ?></div>
							<textarea name="main_twitter_button_via_screen_name" class="inputbox half-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get( 'main_twitter_button_via_screen_name' );?></textarea>
							<div>
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME_EXAMPLE'); ?>
							</div>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_STYLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_STYLE_DESC' ); ?></div>
							<table width="70%" class="social-buttons-preview">
								<tr>
									<td valign="top">
										<div>
											<input type="radio" name="main_twitter_button_style" id="tweet_vertical" value="vertical"<?php echo $this->config->get('main_twitter_button_style') == 'vertical' ? ' checked="checked"' : '';?> />
											<label for="tweet_vertical"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_LARGE');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/tweet/button_vertical.png';?>" /></div>
									</td>
									<td valign="top">
										<div>
											<input type="radio" name="main_twitter_button_style" id="tweet_horizontal" value="horizontal"<?php echo $this->config->get('main_twitter_button_style') == 'horizontal' ? ' checked="checked"' : '';?> />
											<label for="tweet_horizontal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SMALL');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/tweet/button_horizontal.png';?>" /></div>
									</td>
									<td valign="top">
										<div>
											<input type="radio" name="main_twitter_button_style" id="tweet_button" value="none"<?php echo $this->config->get('main_twitter_button_style') == 'none' ? ' checked="checked"' : '';?> />
											<label for="tweet_button"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_PLAIN');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/tweet/button.png';?>" /></div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_TITLE' ); ?></legend>
			<p><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_DESC' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_ENABLE_DESC'); ?></div>
							<?php echo $this->renderCheckbox( 'main_twitter_cards' , $this->config->get( 'main_twitter_cards' ) );?>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>
			
		</td>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_TITLE' ); ?></legend>
			<p><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_INFO' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::sprintf( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_ENABLE', 'Twitter' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_ENABLE_DESC', 'Twitter'); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_microblog' , $this->config->get( 'integrations_twitter_microblog', false ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS_DESC' ); ?></div>
							<textarea name="integrations_twitter_microblog_hashes" class="inputbox half-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get( 'integrations_twitter_microblog_hashes' );?></textarea>
							<div><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS_INSTRUCTIONS' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_CATEGORY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_CATEGORY_DESC' ); ?></div>
							<?php echo EasyBlogHelper::populateCategories('', '', 'select', 'integrations_twitter_microblog_category', $this->config->get( 'integrations_twitter_microblog_category') , true); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE_DESC' ); ?></div>
							<?php
		  						$publishFormat = array();
								$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
								$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
								$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
								$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

								$showdet = JHTML::_('select.genericlist', $publishFormat, 'integrations_twitter_microblog_publish', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('integrations_twitter_microblog_publish' , '1' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_FRONTPAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_twitter_microblog_frontpage' , $this->config->get( 'integrations_twitter_microblog_frontpage' ) );?>
						</div>						
					</td>
				</tr>

			</table>
			</fieldset>
		</td>
	</tr>
</table>