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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATARS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_avatar' , $this->config->get( 'layout_avatar' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_avatar_link_name' , $this->config->get( 'layout_avatar_link_name' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DESC' ); ?></div>
							<?php
		  						$nameFormat = array();
								$avatarIntegration[] = JHTML::_('select.option', 'default', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DEFAULT' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'easysocial', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYSOCIAL' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'communitybuilder', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_CB' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'gravatar', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_GRAVATAR' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jomsocial', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMSOCIAL' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'kunena', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_KUNENA' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'k2', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_K2' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_PHPBB' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'mightytouch', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_MIGHTYREGISTRATION' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'anahita', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_ANAHITA' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jomwall', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMWALL' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'easydiscuss', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYDISCUSS' ) );
								echo JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default' ) );
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH_DESC' ); ?></div>
							<input type="text" name="layout_phpbb_path" class="inputbox full-width" value="<?php echo $this->config->get('layout_phpbb_path', '' );?>" />
						</div>
					</td>
				</tr>

				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORY_AVATAR_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_categoryavatar' , $this->config->get( 'layout_categoryavatar' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOGS_AVATARS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_teamavatar' , $this->config->get( 'layout_teamavatar' ) );?>
						</div>

					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

	</div>
</div>
