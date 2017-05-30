
EasySocial
.require()
.script( 'site/search/toolbar' )
.done(function($){


	$( '[data-mod-search]' ).implement( EasySocial.Controller.Search.Toolbar );

});
