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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal' , 'a.modal' );
?>
<?php
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
	<div class="error" style="background: #ffcccc;border: 1px solid #cc3333;padding: 5px;">
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS_MAX' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_RESOLVE_MESSAGE' ); ?>
	</div>
<?php
	}
}
?>
<div id="config-document">
	<div id="page-main" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('main_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-ebloglayout" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('layout_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-media" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('media_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-seo" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('seo_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-comments" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('comments_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-integrations" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('integrations_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-notifications" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('notifications_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-social" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('social_joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="clr"></div>
