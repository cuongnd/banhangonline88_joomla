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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_WORKFLOW' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable ">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#workflow-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#workflow-rss" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RSS' );?></a>
			</li>
			<li>
				<a href="#workflow-subscriptions" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_SUBSCRIPTIONS' );?></a>
			</li>
			<li>
				<a href="#workflow-publishing" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_REMOTE_PUBLISHING' );?></a>
			</li>
			<li>
				<a href="#workflow-location" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_LOCATION' );?></a>
			</li>
			<li>
				<a href="#workflow-microblogging" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_MICROBLOGGING' );?></a>
			</li>
			<li>
				<a href="#workflow-ratings" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RATINGS' );?></a>
			</li>
			<li>
				<a href="#workflow-autodrafts" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_AUTODRAFTS' );?></a>
			</li>
			<li>
				<a href="#workflow-maintenance" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_MAINTENANCE' );?></a>
			</li>

		</ul>

	</div>


	<div class="tab-content">

		<div class="tab-pane active" id="workflow-general">
			<?php echo $this->loadTemplate( 'main_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-rss">
			<?php echo $this->loadTemplate( 'main_rss_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-subscriptions">
			<?php echo $this->loadTemplate( 'main_subscriptions_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-publishing">
			<?php echo $this->loadTemplate( 'main_remote_publishing_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-location">
			<?php echo $this->loadTemplate( 'main_location_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-microblogging">
			<?php echo $this->loadTemplate( 'main_microblog_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-ratings">
			<?php echo $this->loadTemplate( 'main_ratings_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-autodrafts">
			<?php echo $this->loadTemplate( 'main_autodrafts_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="workflow-maintenance">
			<?php echo $this->loadTemplate( 'main_maintenance_' . $this->getTheme() ); ?>
		</div>

	</div>


</div>
