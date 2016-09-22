<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if( $active != 'complete' ){ ?>
<script type="text/javascript">
$(document).ready( function(){

	$( '[data-installation-nav-prev]' ).bind( 'click' , function()
	{
		$( '[data-installation-form-nav-active]' ).val( <?php echo $active; ?> - 2 );
		$( '[data-installation-form-nav]' ).submit();
	});

	$( '[data-installation-nav-cancel]' ).bind( 'click' , function()
	{
		window.location 	= '<?php echo JURI::base();?>';
	});

	$( '[data-installation-retry]' ).bind( 'click' , function()
	{
		var step 	= $( this ).data( 'retry-step' );

		$( this ).hide();
		$( '[data-installation-loading]' ).show();

		window[ 'es' ][ 'installation' ][ step ]();
	});
});
</script>

<form action="index.php?option=com_easysocial" method="post" data-installation-form-nav class="hidden">
	<input type="hidden" name="active" value="" data-installation-form-nav-active />

	<?php if( $reinstall ){ ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if( $update ){ ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>
</form>

<div class="es-installer-ft">
	<div class="pull-left">
		<?php if( $active > 1 ){ ?>
			<a href="javascript:void(0);" class="btn btn-es btn-sm" data-installation-nav-prev><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PREVIOUS' ); ?></a>
		<?php } else { ?>
			<a href="javascript:void(0);" class="btn btn-es-danger btn-sm" data-installation-nav-cancel><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_EXIT' );?></a>
		<?php } ?>
	</div>

	<div class="pull-right">
		<button class="btn btn-es-primary btn-sm" data-installation-submit>
			<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_NEXT_STEP' ); ?> &rarr;
		</button>

		<button class="btn btn-loading disabled" data-installation-loading>
			<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_LOADING' ); ?>
		</button>

		<button class="btn btn-retry btn-es-success" data-installation-retry>
			<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_RETRY' ); ?>
		</button>

		<button class="btn btn-retry btn-es-success" data-installation-refresh>
			<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_RETRY' ); ?>
		</button>
	</div>
</div>
<?php } ?>
