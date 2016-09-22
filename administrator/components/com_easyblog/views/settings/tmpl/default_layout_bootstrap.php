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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_LAYOUT' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#layout-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#layout-avatars" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_AVATARS' );?></a>
			</li>
			<li>
				<a href="#layout-toolbar" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TOOLBAR' );?></a>
			</li>
			<li>
				<a href="#layout-dashboard" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_DASHBOARD' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="layout-general">
			<?php echo $this->loadTemplate( 'layout_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="layout-avatars">
			<?php echo $this->loadTemplate( 'layout_avatars_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="layout-toolbar">
			<?php echo $this->loadTemplate( 'layout_toolbar_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="layout-dashboard">
			<?php echo $this->loadTemplate( 'layout_dashboard_' . $this->getTheme() ); ?>
		</div>

	</div>

</div>
