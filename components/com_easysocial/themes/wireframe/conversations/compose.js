
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/

EasySocial
.require()
.script( 'site/conversations/composer' )
.done(function($){

	// Implement composer.
	$( '[data-conversations-composer]' ).implement( EasySocial.Controller.Conversations.Composer ,
	{
		location 			: <?php echo $this->config->get( 'conversations.location' ) ? 'true' : 'false' ?>,
		attachments			: <?php echo $this->config->get( 'conversations.attachments.enabled' ) ? 'true' : 'false' ?>,
		extensionsAllowed	: "<?php echo FD::makeString( $this->config->get( 'conversations.attachments.types' ) , ',' );?>",
		maxSize 			: "<?php echo $this->config->get( 'conversations.attachments.maxsize' , 3 );?>mb"
	});

});
