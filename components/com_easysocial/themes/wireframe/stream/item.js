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
				source : "<?php echo JRequest::getVar('view', ''); ?>",
				sourceId : "<?php echo JRequest::getVar('id', ''); ?>",
				loadmore : false
			} );

});
