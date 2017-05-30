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
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN' ); ?>
					</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_commentadmin' , $this->config->get( 'notification_commentadmin' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_commentauthor' , $this->config->get( 'notification_commentauthor' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'notification_commentsubscriber' , $this->config->get( 'notification_commentsubscriber' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" width="50%">&nbsp;</td>
	</tr>
</table>