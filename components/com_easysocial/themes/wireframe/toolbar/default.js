<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
EasySocial
.require()
.script( 'site/toolbar/notifications' , 'site/search/toolbar' , 'site/layout/responsive' )
.done(function($){

	// Implement controller on friend requests.
	$( '[data-notifications]' ).implement( EasySocial.Controller.Notifications ,
	{
		friendsInterval 	: <?php echo $this->config->get( 'notifications.friends.polling' );?>,
		systemInterval 		: <?php echo $this->config->get( 'notifications.system.polling' );?>
	});

	$( '[data-nav-search]' ).implement( EasySocial.Controller.Search.Toolbar );
});
