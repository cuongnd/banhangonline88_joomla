
EasySocial
.require()
.library( 'dialog' )
.done(function($){

	EasySocial.ajax( 'admin/controllers/languages/getLanguages', {
	}).done(function() {
		window.location = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easysocial&view=languages';
	});

});