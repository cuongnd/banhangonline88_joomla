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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_activity_new_blog' , $this->config->get( 'integrations_mighty_activity_new_blog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_activity_update_blog' , $this->config->get( 'integrations_mighty_activity_update_blog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_activity_feature_blog' , $this->config->get( 'integrations_mighty_activity_feature_blog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_submit_content' , $this->config->get( 'integrations_mighty_submit_content' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_show_category' , $this->config->get( 'integrations_mighty_show_category' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH_DESC' ); ?></div>
							<input type="text" name="integrations_mighty_blogs_length" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('integrations_mighty_blogs_length');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_NEW_BLOG' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_NEW_BLOG_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_karma_new_blog' , $this->config->get( 'integrations_mighty_karma_new_blog' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_NEW_BLOG_POINTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_NEW_BLOG_POINTS_DESC' ); ?></div>
							<input type="text" class="input inputbox" style="width: 50px;text-align: center;" value="<?php echo $this->config->get( 'integrations_mighty_karma_new_blog_points' );?>" name="integrations_mighty_karma_new_blog_points" />
							<?php echo JText::_( 'COM_EASYBLOG_POINTS');?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_REMOVE_BLOG' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_REMOVE_BLOG_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_mighty_karma_update_blog' , $this->config->get( 'integrations_mighty_karma_update_blog' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_REMOVE_BLOG_POINTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_MIGHTY_KARMA_REMOVE_BLOG_POINTS_DESC' ); ?></div>
							<input type="text" class="input inputbox" style="width: 50px;text-align: center;" value="<?php echo $this->config->get( 'integrations_mighty_karma_remove_blog_points' );?>" name="integrations_mighty_karma_remove_blog_points" />
							<?php echo JText::_( 'COM_EASYBLOG_POINTS');?>
						</div>

					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>
	</div>
</div>