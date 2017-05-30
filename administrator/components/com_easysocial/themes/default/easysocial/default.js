
EasySocial.require()
.script( 'admin/widgets/news' )
.done( function($){

	// Bind the news controller on the news widget.
	$( '[data-dashboard]' ).implement( EasySocial.Controller.News );

	$.Joomla( 'submitbutton' , function(task)
	{
		if( task == 'clearCache' )
		{
			EasySocial.dialog(
			{
				content 	: EasySocial.ajax( 'admin/views/easysocial/confirmPurgeCache' ),
				bindings 	:
				{
					"{purgeButton} click" : function()
					{
						this.form().submit();
						return false;
					} 
				}
			});
		}

	});

});