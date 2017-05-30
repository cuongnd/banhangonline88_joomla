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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_TITLE' ); ?></legend>
			<p><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_INSTRUCTIONS');?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_ENABLE_INTEGRATIONS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_ENABLE_INTEGRATIONS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_alpha_userpoint' , $this->config->get( 'main_alpha_userpoint' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_POINTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_POINTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_alpha_userpoint_points' , $this->config->get( 'main_alpha_userpoint_points' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_MEDALS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_MEDALS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_alpha_userpoint_medals' , $this->config->get( 'main_alpha_userpoint_medals' ) );?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_RANKS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_RANKS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_alpha_userpoint_ranks' , $this->config->get( 'main_alpha_userpoint_ranks' ) );?>
						</div>
					</td>
				</tr>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>