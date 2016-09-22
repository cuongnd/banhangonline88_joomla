<?php
/**
* @package      EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$active	= JRequest::getString( 'active' , '' );
?>

<script type="text/javascript">
EasyBlog.ready(function($){

	$(window).load(function(){
<?php
	if(!empty($active))
	{
		?>$$('ul#submenu li a#<?php echo $active; ?>').fireEvent('click');<?php
	}
?>
	});

});
</script>

<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="settings">
					<li><a id="main"<?php echo $active == 'main' || $active == '' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_WORKFLOW' ); ?></span></a></li>
					<li><a id="media"<?php echo $active == 'media' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_MEDIA' ); ?></span></a></li>
					<li><a id="seo"<?php echo $active == 'seo' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SEO' ); ?></span></a></li>
					<li><a id="comments"<?php echo $active == 'comments' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_COMMENTS' ); ?></span></a></li>
					<li><a id="ebloglayout"<?php echo $active == 'ebloglayout' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_LAYOUT' ); ?></span></a></li>
					<li><a id="notifications"<?php echo $active == 'notifications' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_NOTIFICATIONS' ); ?></span></a></li>
					<li><a id="integrations"<?php echo $active == 'integrations' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_INTEGRATIONS' ); ?></span></a></li>
					<li><a id="social"<?php echo $active == 'social' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SOCIALINTEGRATIONS' ); ?></span></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
