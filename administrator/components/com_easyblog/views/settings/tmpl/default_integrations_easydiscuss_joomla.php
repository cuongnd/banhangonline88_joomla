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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_GENERAL_TITLE' ); ?></legend>
			<p class="small">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/easydiscuss.png" class="float-l" width="32" style="margin: 0 15px 0 10px;"/>
				<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_DESC');?>
				<br /><br />
				<a href="http://stackideas.com/easydiscuss.html?from=easyblog" target="_blank" class="button"><?php echo JText::_( 'COM_EASYDISCUSS_TRY_BUTTON' );?></a>
			</p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_POINTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_POINTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_points' , $this->config->get( 'integrations_easydiscuss_points' ) );?>
						</div>
						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_BADGES' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_BADGES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_badges' , $this->config->get( 'integrations_easydiscuss_badges' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_TITLE' ); ?></legend>
			<p><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_DESC');?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_notification_blog' , $this->config->get( 'integrations_easydiscuss_notification_blog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_notification_comment' , $this->config->get( 'integrations_easydiscuss_notification_comment' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_RATING' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_RATING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_notification_rating' , $this->config->get( 'integrations_easydiscuss_notification_rating' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_FOLLOWER' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_FOLLOWER_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'integrations_easydiscuss_notification_comment_follower' , $this->config->get( 'integrations_easydiscuss_notification_comment_follower' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>