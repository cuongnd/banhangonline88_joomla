<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

EasySocial.require()
.script( 'site/stream/stream' )
.done( function($){

	// Implement main stream controller.
	$( '[data-streams]' ).implement(
			"EasySocial.Controller.Stream",
			{
				checknew	: <?php echo $this->config->get( 'stream.updates.enabled' ) ? 'true' : 'false'; ?>,
				interval	: "<?php echo FD::config()->get('stream.updates.interval'); ?>",
				source 		: "<?php echo JRequest::getVar('view', ''); ?>",
				sourceId 	: "<?php echo JRequest::getVar('id', ''); ?>",
				autoload	: <?php echo $this->config->get( 'stream.pagination.autoload' ) ? 'true' : 'false'; ?>
			} );
});
