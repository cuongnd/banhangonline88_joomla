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
<script type="text/javascript">
EasyBlog.ready(function($){

	window.downloadPatches = function()
	{
		var apikey = '<?php echo EasyBlogHelper::getConfig()->get( 'apikey' ); ?>';

		if( apikey == '' )
		{
			$( '#result-notices' ).html( '<div style="color:red;"><?php echo JText::_( 'COM_EASYBLOG_UPDATER_API_KEY_NEEDED' );?></div>');
			return false;
		}


		if( $( '#agree' ).is(':checked' ) )
		{
			// Append the loader image.
			$( '#progress-box' ).prepend( '<i class="bar-loader"></i>' );

			$( '#result-holder' ).append( '<div><?php echo JText::_( 'COM_EASYBLOG_UPDATER_STARTING' );?></div>' );
			$( '#result-holder' ).append( '<div><?php echo JText::_( 'COM_EASYBLOG_UPDATER_DOWNLOADING' );?></div>' );
			ejax.load( 'updater' , 'download' , '<?php echo $this->getLatestVersion();?>' );
			$( '#bar-progress' ).css( 'width' , '5%' );
			$( '#bar-progress #progress-indicator' ).html( '5%' );
		}
		else
		{
			$( '#result-notices' ).html( '<?php echo JText::_( 'COM_EASYBLOG_UPDATER_ACCEPT_TERMS' );?>');
		}
	}

	window.toggleLog = function()
	{
		$( '#result-notices' ).toggle();
	}


	window.toggleLogs = function()
	{
		$( '#progress-log' ).toggleClass('show-logs');
	}

});
</script>
<?php echo $this->loadTemplate( $this->getTheme() ); ?>