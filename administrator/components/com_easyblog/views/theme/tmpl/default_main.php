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

<table width="100%">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_INFO' ); ?></legend>

				<table width="100%" cellspacing="1" class="paramlist admintable">
					<tbody>
						<tr class="">
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_NAME');?></span>
							</td>
							<td class="paramlist_value">
								<div class="pt-5"><strong><?php echo $this->theme->name;?></strong><?php echo $this->config->get( 'layout_theme' ) == $this->theme->name ? ' (default)' : ''; ?></div>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_THEME_PATH' );?></span>
							</td>
							<td class="paramlist_value">
								<input type="text" value="<?php echo $this->theme->path; ?>" disabled="disabled" class="inputbox full-width"/>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_THEME_DESCRIPTION');?></span>
							</td>
							<td class="paramlist_value">
								<div class="pt-5"><?php echo JText::_( strtoupper( $this->theme->desc ) );?></div>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW');?></span>
							</td>
							<td class="paramlist_value">
								<img src="<?php echo JURI::root();?>components/com_easyblog/themes/<?php echo $this->theme->element;?>/preview.png" style="border: 1px solid #ccc;"/>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<?php if( $this->blogImage ){ ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_IMAGE_DISPLAY_SETTINGS' ); ?></legend>
				<table width="100%" cellspacing="1" class="paramlist admintable">
					<tbody>
						<tr>
							<td width="10%" class="key">
								<span ><?php echo JText::_( 'COM_EASYBLOG_THEME_SHOW_BLOG_IMAGE_FRONTPAGE');?></span>
							</td>
							<td class="paramlist_value">
								<div class="has-tip">
									<?php echo $this->renderCheckbox( 'params[blogimage_frontpage]' , $this->param->get( 'blogimage_frontpage' ), 'blogimage_frontpage' ); ?>
									<div class="tip">
										<?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_FRONTPAGE_DESC' ); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_SHOW_BLOG_IMAGE_ENTRY');?></span>
							</td>
							<td class="paramlist_value">
								<div class="has-tip">
									<?php echo $this->renderCheckbox( 'params[blogimage_entry]' , $this->param->get( 'blogimage_entry'), 'blogimage_entry' ); ?>
									<div class="tip">
										<?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_ENTRYPAGE_DESC');?>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<?php } ?>
		</td>
		<td width="50%" valign="top">
			<fieldset class="adminform theme-param" id="theme-params">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_PARAMETERS' ); ?></legend>
				<table width="100%" cellspacing="1" class="paramlist admintable">
				<tbody>
					<?php echo $this->renderParams( $this->theme->element ); ?>
				</tbody>
				</table>
			</fieldset>
		</td>
	</tr>
</table>




