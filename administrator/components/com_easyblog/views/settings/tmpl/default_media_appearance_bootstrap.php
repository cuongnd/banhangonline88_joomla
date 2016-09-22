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
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GALLERY_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GALLERY_SHOW_ON_FRONTPAGE' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GALLERY_SHOW_ON_FRONTPAGE_DESC' ); ?></div>
								<?php echo $this->renderCheckbox( 'main_image_gallery_frontpage' , $this->config->get( 'main_image_gallery_frontpage' ) );?>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GALLERY_MAX_WIDTH_PER_ITEM' ); ?>
						</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_GALLERY_MAX_WIDTH_PER_ITEM_DESC' ); ?></div>
								<input type="text" name="main_image_gallery_maxwidth" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_image_gallery_maxwidth' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_LIGHTBOX_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_USE_LIGHTBOX_PREVIEW' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_USE_LIGHTBOX_PREVIEW_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_lightbox_preview' , $this->config->get( 'main_media_lightbox_preview' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SHOW_LIGHTBOX_CAPTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SHOW_LIGHTBOX_CAPTION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_show_lightbox_caption' , $this->config->get( 'main_media_show_lightbox_caption' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_LIGHTBOX_CAPTION_STRIP_EXTENSION' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_LIGHTBOX_CAPTION_STRIP_EXTENSION_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_lightbox_caption_strip_extension' , $this->config->get( 'main_media_lightbox_caption_strip_extension' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_LIGHTBOX_ENFORCE_SIZE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_LIGHTBOX_ENFORCE_SIZE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_media_lightbox_enforce_size' , $this->config->get( 'main_media_lightbox_enforce_size' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="main_media_lightbox_max_width" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_media_lightbox_max_width' );?>" />
						<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="main_media_lightbox_max_height" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_media_lightbox_max_height' );?>" />
						<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

	</div>
</div>