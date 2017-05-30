<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<script type="text/javascript">
EasySocial
.require()
.script( 'site/conversations/composer' )
.done(function($)
{
	// Apply conversation controller
	$( '[data-start-conversation]' ).implement( EasySocial.Controller.Conversations.Composer.Dialog ,
	{
		"recipient" :
		{
			"id"	: "<?php echo $user->id;?>",
			"name"	: "<?php echo $this->escape( $user->getName() );?>",
			"avatar": "<?php echo $user->getAvatar();?>"
		}
	});
});
</script>
<a href="javascript:void(0);" class="author-friend" data-start-conversation><span><?php echo JText::_( 'COM_EASYBLOG_MESSAGE_AUTHOR' ); ?></span></a>