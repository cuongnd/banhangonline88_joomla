EasySocial.require()
.library( 'popbox' )
.done(function($)
{
	$( document )
		.on( 'click.es.updates.update' , '[data-es-outdated]', function()
		{
			var element 		= $(this),
				popboxContent 	= $.Deferred();
				local 			= element.data( 'local-version' ),
				online 			= element.data( 'online-version' );

			element.popbox(
			{
				content	: popboxContent,
				id 		: "fd",
				component   : "es",
				type 	: "updates",
				toggle 	: "click",
				position: "right"
			});

			element.popbox( 'show' );

			EasySocial.ajax( 'admin/views/easysocial/popboxUpdate' ,
			{
				"local"		: local,
				"online"	: online
			})
			.done(function( content )
			{
				popboxContent.resolve( content );
			});
		});
});
