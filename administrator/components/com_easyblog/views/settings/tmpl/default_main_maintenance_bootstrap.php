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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_TITLE' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_INFO' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER_DESC' ); ?></div>
							<input type="text" name="main_orphanitem_ownership" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_orphanitem_ownership', $this->defaultSAId );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_VERSIONING' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_VERSIONING_TIPS' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JAVASCRIPT_VERSIONING' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JAVASCRIPT_VERSIONING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_script_versioning' , $this->config->get( 'main_script_versioning' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_ENVIRONMENT' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_SYSTEM_ENVIRONMENT' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_SYSTEM_ENVIRONMENT_DESC' ); ?></div>
							<select name="easyblog_environment">
								<option value="static"<?php echo $this->config->get( 'easyblog_environment' ) == 'static' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_SYSTEM_ENVIRONMENT_STATIC' ); ?></option>
								<option value="optimized"<?php echo $this->config->get( 'easyblog_environment' ) == 'optimized' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_SYSTEM_ENVIRONMENT_OPTIMIZED' ); ?></option>
								<option value="development"<?php echo $this->config->get( 'easyblog_environment' ) == 'development' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_SYSTEM_ENVIRONMENT_DEVELOPMENT' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JS_COMPRESSION' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JS_COMPRESSION_DESC' ); ?></div>
							<select name="easyblog_mode">
								<option value="compressed"<?php echo $this->config->get( 'easyblog_mode' ) == 'compressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JS_COMPRESSION_COMPRESSED' ); ?></option>
								<option value="uncompressed"<?php echo $this->config->get( 'easyblog_mode' ) == 'uncompressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MAINTENANCE_JS_COMPRESSION_UNCOMPRESSED' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

	</div>
</div>