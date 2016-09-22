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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_blogadmin' , $this->config->get( 'notification_blogadmin' ) );?>
						</div>
					</td>
				</tr>
				
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_allmembers' , $this->config->get( 'notification_allmembers' ) );?>
						</div>
					</td>
				</tr>
				
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_blogsubscriber' , $this->config->get( 'notification_blogsubscriber' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_categorysubscriber' , $this->config->get( 'notification_categorysubscriber' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_sitesubscriber' , $this->config->get( 'notification_sitesubscriber' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span>
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_teamsubscriber' , $this->config->get( 'notification_teamsubscriber' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>
	</div>
</div>