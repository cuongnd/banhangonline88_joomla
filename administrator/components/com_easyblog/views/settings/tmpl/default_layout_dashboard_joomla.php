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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_THEME' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_THEME_DESC' ); ?></div>
							<?php echo $this->getDashboardThemes( $this->config->get('layout_dashboard_theme' ) ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR_DESC' ); ?></div>
							<?php echo $this->getEditorList( $this->config->get('layout_editor') ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_RESPECT_AUTHORS_EDITOR' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_RESPECT_AUTHORS_EDITOR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_editor_author' , $this->config->get( 'layout_editor_author' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardseo' , $this->config->get( 'layout_dashboardseo' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TRACKBACK' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TRACKBACK_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardtrackback' , $this->config->get( 'layout_dashboardtrackback' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ALLOW_HTML_FOR_BIOGRAPHY' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ALLOW_HTML_FOR_BIOGRAPHY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboard_biography_editor' , $this->config->get( 'layout_dashboard_biography_editor' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_ANCHORS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_ANCHORS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardanchor' , $this->config->get( 'layout_dashboardanchor' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_BLOG_IMAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_BLOG_IMAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboard_blogimage' , $this->config->get( 'layout_dashboard_blogimage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION_DESC' ); ?></div>
							<select name="layout_dashboardcategoryselect" class="inputbox">
								<option value="select"<?php echo $this->config->get( 'layout_dashboardcategoryselect' ) == 'select' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION_SELECT_LIST' );?></option>
								<option value="dialog"<?php echo $this->config->get( 'layout_dashboardcategoryselect' ) == 'dialog' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION_DIALOG' );?></option>
								<option value="multitier"<?php echo $this->config->get( 'layout_dashboardcategoryselect' ) == 'multitier' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION_MULTITEIR' );?></option>
							</select>
						</div>
						<div style="margin-top: 20px;" class="notice half-width"><?php echo JText::_('COM_EASYBLOG_LAYOUT_DASHBOARD_CATEGORY_SELECTION_MULTITEIR_DESC'); ?></div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_STATS_OVERVIEW' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_STATS_OVERVIEW_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardstats' , $this->config->get( 'layout_dashboardstats' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_SHOW_TAGS_LISTING' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_SHOW_TAGS_LISTING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'dashboard_tags_listing' , $this->config->get( 'dashboard_tags_listing' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED_DESC' ); ?></div>
							<input type="text" name="max_tags_allowed" class="inputbox" value="<?php echo $this->config->get('max_tags_allowed', '' );?>" />
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGER_THEME' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGER_THEME_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_enablebloggertheme' , $this->config->get( 'layout_enablebloggertheme' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_SELECTION' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_SELECTION_DESC' ); ?></div>
							<?php echo $this->getBloggerThemes() ?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_TOOLBAR_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_DISABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_DISABLE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_enabledashboardtoolbar' , $this->config->get( 'layout_enabledashboardtoolbar' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_HOME' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_HOME_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardhome' , $this->config->get( 'layout_dashboardhome' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_BLOGS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_BLOGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardblogs' , $this->config->get( 'layout_dashboardblogs' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_MAIN' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_MAIN_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardmain' , $this->config->get( 'layout_dashboardmain' ) );?>
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_COMMENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardcomments' , $this->config->get( 'layout_dashboardcomments' ) );?>
						</div>

					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_CATEGORIES' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_CATEGORIES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardcategories' , $this->config->get( 'layout_dashboardcategories' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_DRAFTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_DRAFTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboarddrafts' , $this->config->get( 'layout_dashboarddrafts' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TAGS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_TAGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardtags' , $this->config->get( 'layout_dashboardtags' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_NEW_POST' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_NEW_POST_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardnewpost' , $this->config->get( 'layout_dashboardnewpost' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SETTINGS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SETTINGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardsettings' , $this->config->get( 'layout_dashboardsettings' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_LOGOUT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_LOGOUT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'layout_dashboardlogout' , $this->config->get( 'layout_dashboardlogout' ) );?>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
