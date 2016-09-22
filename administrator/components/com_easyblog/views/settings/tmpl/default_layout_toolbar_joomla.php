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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_GOOGLE_FONT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_GOOGLE_FONT_DESC' ); ?></div>
							<?php
								$fonts = array();
								$fonts[] = JHTML::_('select.option', 'site', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_IGNORE_USE_SITE_FONT' ) );
								$fonts[] = JHTML::_('select.option', 'Droid Sans', JText::_( 'Droid Sans' ) );
								$fonts[] = JHTML::_('select.option', 'Inconsolata', JText::_( 'Inconsolata' ) );
								$fonts[] = JHTML::_('select.option', 'Pacifico', JText::_( 'Pacifico' ) );
								$fonts[] = JHTML::_('select.option', 'Cabin Sketch bold', JText::_( 'Cabin Sketch Bold' ) );
								$fonts[] = JHTML::_('select.option', 'Kristi', JText::_( 'Kristi' ) );
								$fonts[] = JHTML::_('select.option', 'Molengo', JText::_( 'Molengo' ) );
								$fonts[] = JHTML::_('select.option', 'Orbitron', JText::_( 'Orbitron' ) );
								$showdet = JHTML::_('select.genericlist', $fonts , 'layout_googlefont', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_googlefont' ) );
								echo $showdet;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_HEADER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_HEADER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_headers' , $this->config->get( 'layout_headers' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_headers_respect_author' , $this->config->get( 'layout_headers_respect_author' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_toolbar' , $this->config->get( 'layout_toolbar' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ICONLESS_TOOLBAR' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ICONLESS_TOOLBAR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_iconless' , $this->config->get( 'layout_iconless' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_FEATURES_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LATEST_POST' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LATEST_POST_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_latest' , $this->config->get( 'layout_latest' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BUTTON_IN_TOOLBAR' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BUTTON_IN_TOOLBAR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_option_toolbar' , $this->config->get( 'layout_option_toolbar' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORIES' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORIES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_categories' , $this->config->get( 'layout_categories' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TAGS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TAGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_tags' , $this->config->get( 'layout_tags' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_bloggers' , $this->config->get( 'layout_bloggers' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ARCHIVE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_ARCHIVE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_archive' , $this->config->get( 'layout_archive' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_SEARCH' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_SEARCH_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_search' , $this->config->get( 'layout_search' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_teamblog' , $this->config->get( 'layout_teamblog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LOGIN' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_LOGIN_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_login' , $this->config->get( 'layout_login' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_EDIT_PROFILE' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_EDIT_PROFILE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'toolbar_editprofile' , $this->config->get( 'toolbar_editprofile' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_LOGOUT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TOOLBAR_SHOW_LOGOUT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'toolbar_logout' , $this->config->get( 'toolbar_logout' ) );?>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
