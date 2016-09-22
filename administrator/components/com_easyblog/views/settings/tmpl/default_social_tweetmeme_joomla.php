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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_TITLE' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TWEETMEME_INFO');?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_tweetmeme' , $this->config->get( 'main_tweetmeme' ) );?>
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
							<?php echo $this->renderCheckbox( 'main_tweetmeme_frontpage' , $this->config->get( 'main_tweetmeme_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_BUTTON_STYLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_BUTTON_STYLE_DESC' ); ?></div>
							<table class="social-buttons-preview" style="width:50% !important;" cellspacing="3">
								<td valign="top" width="50%">
									<div>
										<input type="radio" name="main_tweetmeme_style" id="normal" value="normal"<?php echo $this->config->get('main_tweetmeme_style') == 'normal' ? ' checked="checked"' : '';?> />
										<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_LARGE');?></label>
									</div>
									<div style="clear:both;"></div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/tweetmeme/button_normal.png';?>" /></div>
								</td>
								<td valign="top" width="50%">
									<div>
										<input type="radio" name="main_tweetmeme_style" id="compact" value="compact"<?php echo $this->config->get('main_tweetmeme_style') == 'compact' ? ' checked="checked"' : '';?> />
										<label for="compact"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SMALL');?></label>
									</div>
									<div style="clear:both;"></div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/tweetmeme/button_compact.png';?>" /></div>
								</td>
							</table>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_URLSHORTENER' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_URLSHORTENER_DESC' ); ?></div>
							<?php
		  						$tweetmemeurl = array();
								$tweetmemeurl[] = JHTML::_('select.option', 'bit.ly', JText::_( 'bit.ly' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'awe.sm', JText::_( 'awe.sm' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'cli.gs', JText::_( 'cli.gs' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'digg.com', JText::_( 'digg.com' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'is.gd', JText::_( 'is.gd' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'tinyurl.com', JText::_( 'tinyurl.com' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'tr.im', JText::_( 'tr.im' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'su.pr', JText::_( 'su.pr' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'ow.ly', JText::_( 'ow.ly' ) );
								$tweetmemeurl[] = JHTML::_('select.option', 'twurl.nl', JText::_( 'twurl.nl' ) );
								$showdet = JHTML::_('select.genericlist', $tweetmemeurl, 'main_tweetmeme_url', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_tweetmeme_url' , 'bit.ly' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_RTSOURCE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWEETMEME_RTSOURCE_DESC' ); ?></div>
							<input type="text" name="main_tweetmeme_rtsource" class="inputbox" value="<?php echo $this->config->get('main_tweetmeme_rtsource');?>" size="60" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>