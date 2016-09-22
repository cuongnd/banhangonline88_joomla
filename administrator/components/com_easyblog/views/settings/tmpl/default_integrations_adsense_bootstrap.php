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
			<a name="main_config" id="feedburner_config"></a>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE' ); ?></legend>
			
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integration_google_adsense_enable' , $this->config->get( 'integration_google_adsense_enable' ) );?>
						</div>
					</td>
				</tr>	
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USE_CENTRALIZED' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USE_CENTRALIZED_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integration_google_adsense_centralized' , $this->config->get( 'integration_google_adsense_centralized' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ALLOW_BLOGGER_UPDATE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ALLOW_BLOGGER_UPDATE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_google_adsense_blogger' , $this->config->get( 'integrations_google_adsense_blogger' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" valign="top">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_DESC' ); ?></div>
							<textarea name="integration_google_adsense_code" class="inputbox full-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>
							<div class="notice"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?></div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_DESC' ); ?></div>
							<?php
							$display = array();
							$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BOTH' ) );
							$display[] = JHTML::_('select.option', 'header', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER' ) );
							$display[] = JHTML::_('select.option', 'footer', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER' ) );
							$display[] = JHTML::_('select.option', 'beforecomments', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_COMMENT' ) );
							$display[] = JHTML::_('select.option', 'userspecified', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USER_SPECIFIED' ) );
							$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('integration_google_adsense_display' , 'both' ) );
							echo $showOption;
							?>
							<div class="notice" style="margin-top: 10px;"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_NOTE');?></div>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_DESC' ); ?></div>
							<?php
							$display = array();
							$display[] = JHTML::_('select.option', 'both', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ALL' ) );
							$display[] = JHTML::_('select.option', 'members', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS' ) );
							$display[] = JHTML::_('select.option', 'guests', JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS' ) );
							$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display_access', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('integration_google_adsense_display_access' , 'both' ) );
							echo $showOption;
							?>
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