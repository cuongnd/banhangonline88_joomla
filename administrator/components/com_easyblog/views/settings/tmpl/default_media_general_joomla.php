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
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_UPLOADER_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS' ); ?>
							</span>
						</td>
						<td valign="top" class="value">
							<div class="has-tip">

								<script type="text/javascript">
									EasyBlog.ready(function($){
										$(".extensionButton").click(function(){
											$("#media_extensions").val('jpg,png,gif,3g2,3gp,aac,f4a,f4v,flv,m4a,m4v,mov,mp3,mp4,zip,rar,7z,pdf,doc,docx,ppt,pptx,xls,xlsx');
										});
									});
								</script>

								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS_DESC' ); ?></div>
								<input type="text" class="input inputbox" style="width: 300px;" value="<?php echo $this->config->get( 'main_media_extensions' );?>" id="media_extensions" name="main_media_extensions" />
								<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_RESET_DEFAULT' );?>" class="extensionButton"/>
							</div>
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip">
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE' ); ?>
							</span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE_DESC' ); ?></div>
								<input type="text" name="main_upload_image_size" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_upload_image_size', '0' );?>" />
								<?php echo JText::_( 'COM_EASYBLOG_MEGABYTES' );?>
								<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_MAXSIZE' , ini_get( 'upload_max_filesize') ); ?></div>
								<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_POSTMAXSIZE' , ini_get( 'post_max_size') ); ?></div>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH_DESC' ); ?></div>
							<input type="text" name="main_image_path" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_image_path', 'images/easyblog_images/' );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH_DESC' ); ?></div>
							<input type="text" name="main_shared_path" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_shared_path', 'media/com_easyblog/shared/' );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH_DESC' ); ?></div>
							<input type="text" name="main_avatarpath" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_avatarpath', 'images/eblog_avatar/' );?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH_DESC' ); ?></div>
							<input type="text" name="main_categoryavatarpath" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_categoryavatarpath', 'images/eblog_cavatar/' );?>" />
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH_DESC' ); ?></div>
							<input type="text" name="main_teamavatarpath" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_teamavatarpath', 'images/eblog_tavatar/' );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGES_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY' ); ?>
								</span>
							</td>
							<td valign="top">
								<?php
			  						$options = array();

			  						for( $i = 0; $i <= 100; $i += 10 )
			  						{
			  							$message	= $i;
			  							$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
			  							$options[]	= JHTML::_('select.option', $i , $message );
			  						}

									echo JHTML::_('select.genericlist', $options, 'main_image_quality', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_image_quality' ) );
								?>
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE' ); ?>
								</span>
							</td>
							<td valign="top">
								<?php echo $this->renderCheckbox( 'main_resize_original_image' , $this->config->get( 'main_resize_original_image' ) );?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH' ); ?>
								</span>
							</td>
							<td valign="top">
								<input type="text" name="main_original_image_width" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_original_image_width');?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT' ); ?>
								</span>
							</td>
							<td valign="top">
								<input type="text" name="main_original_image_height" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_original_image_height');?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY' ); ?>
								</span>
							</td>
							<td valign="top">
								<?php
			  						$options = array();

			  						for( $i = 0; $i <= 100; $i += 10 )
			  						{
			  							$message	= $i;
			  							$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
			  							$options[]	= JHTML::_('select.option', $i , $message );
			  						}

									echo JHTML::_('select.genericlist', $options, 'main_original_image_quality', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_original_image_quality' ) );
								?>
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_TITLE' ); ?></legend>
				<table class="admintable" cellspacing="1">
					<tbody>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH' ); ?>
								</span>
							</td>
							<td valign="top">
								<input type="text" name="main_thumbnail_width" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_thumbnail_width');?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT' ); ?>
								</span>
							</td>
							<td valign="top">
								<input type="text" name="main_thumbnail_height" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('main_thumbnail_height');?>" />
								<?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span class="editlinktip">
									<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY' ); ?>
								</span>
							</td>
							<td valign="top">
								<?php
			  						$options = array();

			  						for( $i = 0; $i <= 100; $i += 10 )
			  						{
			  							$message	= $i;
			  							$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
			  							$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
			  							$options[]	= JHTML::_('select.option', $i , $message );
			  						}

									echo JHTML::_('select.genericlist', $options, 'main_thumbnail_quality', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_thumbnail_quality' ) );
								?>
								<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_VIDEOS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="max_video_width" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('max_video_width' );?>" />
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
						<input type="text" name="max_video_height" class="inputbox" style="width: 50px;text-align:center;" value="<?php echo $this->config->get('max_video_height' );?>" />
						<?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>


		</td>
	</tr>
</table>
