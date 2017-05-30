<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if( $this->getParam( 'show_fontchanger' , true ) ){ ?>
<script type="text/javascript">
EasyBlog.ready(function($){

	// Bind event's on the font size changer.
	$( '#ezblog-body .font-switcher a' ).click( function(){
		var blogText	= $( '#ezblog-body .blog-text' );
		var current 	= $( blogText ).css( 'font-size' );
		var num			= parseFloat(current, 10);
		var unit		= current.slice(-2);

		if( this.id == 'fontLarge' )
		{
			num = num * 1.4;
		}
		else if (this.id == 'fontSmall')
		{
			num = num / 1.4;
		}

		$( blogText ).css( 'font-size' , num + unit );
		$( blogText ).css( 'line-height' , num + unit );

		return false;
	});
});
</script>
<li class="font-switcher">
	<span><?php echo JText::_( 'COM_EASYBLOG_FONT_SIZE' ); ?>:</span>
	<a id="fontLarge" class="fontChanger" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYBLOG_FONT_LARGER' ); ?></a>
	<a id="fontSmall" class="fontChanger" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYBLOG_FONT_SMALLER' );?></a>
</li>
<?php } ?>
