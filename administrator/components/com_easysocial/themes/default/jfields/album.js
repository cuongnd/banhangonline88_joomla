
EasySocial.require()
.library( 'dialog' )
.done(function($)
{
	<?php if( FD::version()->getVersion() < 3 ){ ?>
		$('body').addClass( 'com_easysocial25' );
	<?php } ?>

	window.selectAlbum 	= function( obj )
	{
		$( '[data-jfield-album-title]' ).val( obj.title );

		$( '[data-jfield-album-value]' ).val( obj.alias );

		// Close the dialog when done
		EasySocial.dialog().close();
	}

	$( '[data-jfield-album]' ).on( 'click', function()
	{
		EasySocial.dialog(
		{
			content 	: EasySocial.ajax( 'admin/views/albums/browse' , { 'jscallback' : 'selectAlbum' })
		});
	});

});
