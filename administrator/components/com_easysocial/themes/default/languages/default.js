
EasySocial
.require()
.script( 'admin/users/users' , 'admin/grid/grid' )
.library( 'dialog' )
.done(function($){

	$( '[data-table-grid]' ).implement( EasySocial.Controller.Grid );

	$.Joomla( 'submitbutton' , function(task)
	{
		if( task == 'discover' )
		{
			window.location 	= 'index.php?option=com_easysocial&view=languages&layout=discover';
			return false;
		}

		$.Joomla( 'submitform' , [task] );

	});
});