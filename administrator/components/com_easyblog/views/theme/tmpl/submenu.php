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
$form = JRequest::getVar('layout') == 'form' ? true : false;
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
<?php if( !$form ){ ?>
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
					<li><a id="main"<?php echo $active == 'main' || $active == '' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_TAB_MAIN' ); ?></span></a></li>
					<li><a id="advanceTheme"<?php echo $active == 'advanceTheme' ? ' class="active"' :'';?>><span><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME' ); ?></span></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php } ?>