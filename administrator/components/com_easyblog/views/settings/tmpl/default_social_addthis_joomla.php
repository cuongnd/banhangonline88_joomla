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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_PUBLISHING_CODE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_PUBLISHING_CODE_DESC' ); ?></div>
							<input type="text" name="social_addthis_customcode" class="inputbox half-width" style="margin-bottom: 10px;" value="<?php echo $this->config->get('social_addthis_customcode');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE_DESC' ); ?></div>
							<table class="social-buttons-preview" style="width: 80% !important;">
								<tr>
									<td width="50%">
										<div>
											<input id="addthis_normal" type="radio" name="social_addthis_style" value="1" <?php echo ($this->config->get('social_addthis_style') == '1') ? 'checked' : ''; ?>  style="vertical-align: middle;" />
											<label for="addthis_normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_ADDTHIS_NORMAL');?></label>
										</div>
										<div>
											<img style="vertical-align: middle;" src="<?php echo JURI::root() . '/administrator/components/com_easyblog/assets/images/addthis_button1.png'; ?>" />
										</div>
									</td>
									<td>
										<div>
											<input id="addthis_compact" type="radio" name="social_addthis_style" value="2" <?php echo ($this->config->get('social_addthis_style') == '2') ? 'checked' : ''; ?> style="vertical-align: middle;" />
											<label for="addthis_compact"><?php echo JText::_('COM_EASYBLOG_SETTINGS_ADDTHIS_COMPACT');?></label>
										</div>
										<div>
											<img style="vertical-align: middle;" src="<?php echo JURI::root() . '/administrator/components/com_easyblog/assets/images/addthis_button2.png'; ?>" />
										</div>
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
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>