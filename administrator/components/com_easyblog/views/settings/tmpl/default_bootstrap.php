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

JHTML::_('behavior.modal' , 'a.modal' );

// There seems to be some errors when suhosin is configured with the following settings
// which most hosting provider does! :(
//
// suhosin.post.max_vars = 200
// suhosin.request.max_vars = 200
if(in_array('suhosin', get_loaded_extensions()))
{
	$max_post		= @ini_get( 'suhosin.post.max_vars');
	$max_request	= @ini_get( 'suhosin.request.max_vars' );

	if( !empty( $max_post ) && $max_post < 400 || !empty( $max_request ) && $max_request < 400 )
	{
?>
	<div class="alert alert-error" style="background: #ffcccc;border: 1px solid #cc3333;padding: 5px;">
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS_MAX' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_RESOLVE_MESSAGE' ); ?>
	</div>
<?php
	}
}
?>

<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav pt-10">
		<ul class="nav nav-list">
			<li class="nav-header">Settings</li>
			<li class="active">
				<a href="#workflow" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_WORKFLOW' ); ?></a>
			</li>

			<li>
				<a href="#media" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_MEDIA' ); ?></a>
			</li>

			<li>
				<a href="#seo" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SEO' ); ?></a>
			</li>

			<li>
				<a href="#comments" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_COMMENTS' ); ?></a>
			</li>

			<li>
				<a href="#layout" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_LAYOUT' ); ?></a>
			</li>

			<li>
				<a href="#notifications" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_NOTIFICATIONS' ); ?></a>
			</li>

			<li>
				<a href="#integrations" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_INTEGRATIONS' ); ?></a>
			</li>

			<li>
				<a href="#social" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SOCIALINTEGRATIONS' ); ?></a>
			</li>
		</ul>
		</div>
	</div>
	<div class="span10">
		<div class="tab-content">

			<div class="tab-pane active" id="workflow">
				<?php echo $this->loadTemplate( 'main_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="media">
				<?php echo $this->loadTemplate( 'media_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="seo">
				<?php echo $this->loadTemplate( 'seo_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="comments">
				<?php echo $this->loadTemplate( 'comments_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="layout">
				<?php echo $this->loadTemplate( 'layout_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="notifications">
				<?php echo $this->loadTemplate( 'notifications_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="integrations">
				<?php echo $this->loadTemplate( 'integrations_' . $this->getTheme() );?>
			</div>

			<div class="tab-pane" id="social">
				<?php echo $this->loadTemplate( 'social_' . $this->getTheme() );?>
			</div>
		</div>
	</div>
</div>




