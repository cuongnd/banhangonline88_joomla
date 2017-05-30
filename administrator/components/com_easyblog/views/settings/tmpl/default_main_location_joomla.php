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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION' ); ?>
						</span>
					</td>
					<td class="value" style="position:relative;">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_locations' , $this->config->get( 'main_locations' ) );?>
							<a href="http://stackideas.com/docs/easyblog/how-tos/152-setting-up-location-services" target="_blank" style="position: absolute;left:120px;top:5px"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_SHOW_BLOG_FRONTPAGE' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_SHOW_BLOG_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_locations_blog_frontpage' , $this->config->get( 'main_locations_blog_frontpage' ) );?>
						</div>									
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_SHOW_BLOG_ENTRY' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_SHOW_BLOG_ENTRY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_locations_blog_entry' , $this->config->get( 'main_locations_blog_entry' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE_DESC' ); ?></div>
							<input type="text" name="main_locations_blog_language" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_locations_blog_language' );?>" />
							<a class="mlm" href="https://spreadsheets.google.com/a/stackideas.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LANGUAGE_CODE_REFERENCE');?></a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAP_FEATURES' );?></legend>
				<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_locations_static_maps' , $this->config->get( 'main_locations_static_maps' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_WIDTH' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_WIDTH_DESC' ); ?></div>
							<input type="text" name="main_locations_blog_map_width" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_locations_blog_map_width');?>" />
							<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT_DESC' ); ?></div>
							<input type="text" name="main_locations_blog_map_height" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_locations_blog_map_height');?>" />
							<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE_DESC' ); ?></div>
							<select name="main_locations_map_type" class="inputbox">
								<option value="ROADMAP"<?php echo $this->config->get( 'main_locations_map_type' ) == 'ROADMAP' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_ROADMAP' ); ?></option>
								<option value="SATELLITE"<?php echo $this->config->get( 'main_locations_map_type' ) == 'SATELLITE' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_SATELLITE' ); ?></option>
								<option value="HYBRID"<?php echo $this->config->get( 'main_locations_map_type' ) == 'HYBRID' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_HYBRID' ); ?></option>
								<option value="TERRAIN"<?php echo $this->config->get( 'main_locations_map_type' ) == 'TERRAIN' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_TERRAIN' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL_DESC' ); ?></div>
							<input type="text" name="main_locations_default_zoom_level" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_locations_default_zoom_level');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL_DESC' ); ?></div>
							<input type="text" name="main_locations_max_zoom_level" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_locations_max_zoom_level');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL_DESC' ); ?></div>
							<input type="text" name="main_locations_min_zoom_level" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_locations_min_zoom_level');?>" />
						</div>
					</td>
				</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>