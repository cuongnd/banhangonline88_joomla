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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GENERAL_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_IMAGE_MANAGER' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_IMAGE_MANAGER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_manager' , $this->config->get( 'main_media_manager' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_SHARED_MEDIA' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_ENABLE_SHARED_MEDIA_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_manager_place_shared_media' , $this->config->get( 'main_media_manager_place_shared_media' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ITEMS_PER_PAGE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ITEMS_PER_PAGE_DESC' ); ?></div>
							<input type="text" name="main_media_manager_items_per_page" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_media_manager_items_per_page' );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PANEL_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_LIGHTBOX' ); ?>
							</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_LIGHTBOX_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_media_manager_image_panel_enable_lightbox' , $this->config->get( 'main_media_manager_image_panel_enable_lightbox' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_DIMENSION' ); ?>
							</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_DIMENSION_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_media_manager_image_panel_enforce_image_dimension' , $this->config->get( 'main_media_manager_image_panel_enforce_image_dimension' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_WIDTH' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_WIDTH_DESC' ); ?></div>
								<input type="text" name="main_media_manager_image_panel_enforce_image_width" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_media_manager_image_panel_enforce_image_width' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_HEIGHT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ENFORCE_IMAGE_HEIGHT_DESC' ); ?></div>
								<input type="text" name="main_media_manager_image_panel_enforce_image_height" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_media_manager_image_panel_enforce_image_height' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAX_VARIATION_IMAGE_WIDTH' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAX_VARIATION_IMAGE_WIDTH_DESC' ); ?></div>
								<input type="text" name="main_media_manager_image_panel_max_variation_image_width" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_media_manager_image_panel_max_variation_image_width' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAX_VARIATION_IMAGE_HEIGHT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAX_VARIATION_IMAGE_HEIGHT_DESC' ); ?></div>
								<input type="text" name="main_media_manager_image_panel_max_variation_image_height" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_media_manager_image_panel_max_variation_image_height' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_VIDEO_PANEL_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_VIDEO_WIDTH' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_VIDEO_WIDTH_DESC' ); ?></div>
								<input type="text" name="dashboard_video_width" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('dashboard_video_width' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_VIDEO_HEIGHT' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_VIDEO_HEIGHT_DESC' ); ?></div>
								<input type="text" name="dashboard_video_height" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('dashboard_video_height' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>



		</td>
	</tr>
</table>
