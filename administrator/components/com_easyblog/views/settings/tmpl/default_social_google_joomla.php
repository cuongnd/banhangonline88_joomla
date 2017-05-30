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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_googleone' , $this->config->get( 'main_googleone' ) );?>
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
							<?php echo $this->renderCheckbox( 'main_googleone_frontpage' , $this->config->get( 'main_googleone_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_STYLE' ); ?>
						</span>
					</td>
				    <td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_STYLE_DESC' ); ?></div>
							<table width="70%" class="social-buttons-preview">
								<tr>
									<td valign="top">
										<div>
											<input type="radio" name="main_googleone_layout" id="tall" value="tall"<?php echo $this->config->get('main_googleone_layout') == 'tall' ? ' checked="checked"' : '';?> />
											<label for="large"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_LARGE');?></label>
										</div>
										<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/google/large.png';?>" /></div>
									</td>
									<td valign="top">
										<div>
											<input type="radio" name="main_googleone_layout" id="medium" value="medium"<?php echo $this->config->get('main_googleone_layout') == 'medium' ? ' checked="checked"' : '';?> />
											<label for="small"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_SMALL');?></label>
										</div>
										<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/google/small.png';?>" /></div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PROFILES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PROFILES_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PROFILES_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_google_profiles' , $this->config->get( 'main_google_profiles' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>