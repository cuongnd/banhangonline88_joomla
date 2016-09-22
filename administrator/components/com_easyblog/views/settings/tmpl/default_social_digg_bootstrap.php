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
<div class="row-fluid">
	<div class="span12">

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_DIGG_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_DIGG_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_DIGG_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_digg_button' , $this->config->get( 'main_digg_button' ) );?>
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
							<?php echo $this->renderCheckbox( 'main_digg_button_frontpage' , $this->config->get( 'main_digg_button_frontpage' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_DIGG_BUTTON_STYLE' ); ?>
						</span>
					</td>
				    <td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_DIGG_BUTTON_STYLE_DESC' ); ?></div>
							<table width="70%" class="social-buttons-preview">
								<tr>
									<td valign="top">
										<div>
											<input type="radio" name="main_digg_button_style" id="digg_medium" value="medium"<?php echo $this->config->get('main_digg_button_style') == 'medium' ? ' checked="checked"' : '';?> />
											<label for="digg_medium"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_LARGE');?></label>
										</div>
										<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/digg/medium.png';?>" /></div>
									</td>
									<td valign="top" width="50%">
										<div>
											<input type="radio" name="main_digg_button_style" id="digg_compact" value="compact"<?php echo $this->config->get('main_digg_button_style') == 'compact' ? ' checked="checked"' : '';?> />
											<label for="digg_compact"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_SMALL');?></label>
										</div>
										<div><img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/digg/compact.png';?>" /></div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>
	</div>
</div>