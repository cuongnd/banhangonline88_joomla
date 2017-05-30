
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectUser 	= function( obj )
	{
		$( '[data-jfield-user-title]' ).val( obj.title );

		$( '[data-jfield-user-value]' ).val( obj.alias );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-user]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/users/browse' ,
							{
								'dialogTitle'	: '<?php echo JText::_( 'COM_EASYSOCIAL_USERS_BROWSE_USERS_DIALOG_TITLE' );?>',
								'jscallback' : 'selectUser'
							})
		});
	});

});
