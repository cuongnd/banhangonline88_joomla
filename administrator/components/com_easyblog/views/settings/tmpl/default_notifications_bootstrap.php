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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_NOTIFICATIONS' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#notifications-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#notifications-blogs" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_BLOGS' );?></a>
			</li>
			<li>
				<a href="#notifications-comments" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_COMMENTS' );?></a>
			</li>
			<li>
				<a href="#notifications-templates" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_TEMPLATES' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="notifications-general">
			<?php echo $this->loadTemplate( 'notifications_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="notifications-blogs">
			<?php echo $this->loadTemplate( 'notifications_blogs_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="notifications-comments">
			<?php echo $this->loadTemplate( 'notifications_comments_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="notifications-templates">
			<?php echo $this->loadTemplate( 'notifications_templates_' . $this->getTheme() ); ?>
		</div>

	</div>

</div>