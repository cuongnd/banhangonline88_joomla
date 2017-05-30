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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_STUMBLEUPON_BUTTONS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_STUMBLEUPON_BUTTON' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_STUMBLEUPON_BUTTON_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_stumbleupon_button' , $this->config->get( 'main_stumbleupon_button' ) );?>
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
							<?php echo $this->renderCheckbox( 'main_stumbleupon_button_frontpage' , $this->config->get( 'main_stumbleupon_button_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_STUMBLEUPON_BUTTON_STYLE'); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_STUMBLEUPON_BUTTON_STYLE_DESC' ); ?></div>
							<table width="70%" class="social-buttons-preview">
								<tr>
									<td valign="top">
										<div>
											<input type="radio" name="main_stumbleupon_button_style" id="stumbleupon_vertical" value="vertical"<?php echo $this->config->get('main_stumbleupon_button_style') == 'vertical' ? ' checked="checked"' : '';?> />
											<label for="stumbleupon_vertical"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_LARGE');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/stumbleupon/button_vertical.png';?>" /></div>
									</td>
									<td valign="top">
										<div>
											<input type="radio" name="main_stumbleupon_button_style" id="stumbleupon_horizontal" value="horizontal"<?php echo $this->config->get('main_stumbleupon_button_style') == 'horizontal' ? ' checked="checked"' : '';?> />
											<label for="stumbleupon_horizontal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SMALL');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/stumbleupon/button_horizontal.png';?>" /></div>
									</td>
									<td valign="top">
										<div>
											<input type="radio" name="main_stumbleupon_button_style" id="stumbleupon_button" value="none"<?php echo $this->config->get('main_stumbleupon_button_style') == 'none' ? ' checked="checked"' : '';?> />
											<label for="stumbleupon_button"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_PLAIN');?></label>
										</div>
										<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/stumbleupon/button.png';?>" /></div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>